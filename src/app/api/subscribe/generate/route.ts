import { NextRequest, NextResponse } from 'next/server';
import { readFileSync, writeFileSync, existsSync, mkdirSync } from 'fs';
import { join } from 'path';

const DATA_DIR = join(process.cwd(), 'data');
const SUBSCRIBERS_FILE = join(DATA_DIR, 'subscribers.json');

const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
const seg = (n: number) => Array.from({ length: n }, () => CHARS[Math.floor(Math.random() * CHARS.length)]).join('');

function ensureDataDir() {
  if (!existsSync(DATA_DIR)) mkdirSync(DATA_DIR, { recursive: true });
}

function loadSubscribers(): any[] {
  ensureDataDir();
  try {
    if (existsSync(SUBSCRIBERS_FILE)) return JSON.parse(readFileSync(SUBSCRIBERS_FILE, 'utf-8'));
  } catch {}
  return [];
}

function saveSubscribers(subs: any[]) {
  ensureDataDir();
  writeFileSync(SUBSCRIBERS_FILE, JSON.stringify(subs, null, 2), 'utf-8');
}

function genToken(prefix: string): string {
  return `WSEC-${prefix}-${seg(4)}-${seg(4)}-${seg(4)}`;
}

// Admin generates token for subscriber after payment verification
export async function POST(request: NextRequest) {
  try {
    const { plan, payment_id, admin_password } = await request.json();

    // Admin auth check
    const { createHash } = await import('crypto');
    const adminHash = createHash('sha256').update('#AllOfThem-3301').digest('hex');
    const passHash = admin_password ? createHash('sha256').update(admin_password).digest('hex') : '';

    if (passHash !== adminHash) {
      return NextResponse.json({ success: false, error: 'Neautorizat' }, { status: 401 });
    }

    if (!plan) {
      return NextResponse.json({ success: false, error: 'Plan obligatoriu' }, { status: 400 });
    }

    const validPlans = ['demo', 'pro', 'enterprise'];
    if (!validPlans.includes(plan)) {
      return NextResponse.json({ success: false, error: 'Plan invalid' }, { status: 400 });
    }

    const prefixMap: Record<string, string> = { demo: 'DEMO', pro: 'PRO', enterprise: 'ENT' };
    const durationMap: Record<string, number> = { demo: 1, pro: 30, enterprise: 30 };
    const requestsMap: Record<string, number> = { demo: 10, pro: 500, enterprise: -1 };

    const token = genToken(prefixMap[plan]);
    const now = new Date();
    const expires = new Date(now.getTime() + durationMap[plan] * 24 * 60 * 60 * 1000);

    const subscriber = {
      token,
      plan,
      role: plan === 'enterprise' ? 'admin' : 'subscriber',
      requests_used: 0,
      requests_limit: requestsMap[plan],
      created_at: now.toISOString(),
      expires_at: expires.toISOString(),
      active: true,
      payment_id: payment_id || null,
    };

    const subs = loadSubscribers();
    subs.push(subscriber);
    saveSubscribers(subs);

    // If payment_id provided, update payment status
    if (payment_id) {
      try {
        const { readFileSync: rf, writeFileSync: wf } = await import('fs');
        const payFile = join(DATA_DIR, 'payments.json');
        if (existsSync(payFile)) {
          const payments = JSON.parse(rf(payFile, 'utf-8'));
          const idx = payments.findIndex((p: any) => p.id === payment_id);
          if (idx >= 0) {
            payments[idx].status = 'verified';
            payments[idx].verified_at = new Date().toISOString();
            payments[idx].token = token;
            wf(payFile, JSON.stringify(payments, null, 2), 'utf-8');
          }
        }
      } catch {}
    }

    return NextResponse.json({
      success: true,
      token,
      plan,
      role: subscriber.role,
      expires_at: subscriber.expires_at,
      requests_limit: subscriber.requests_limit,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: 'Eroare server' }, { status: 500 });
  }
}

// GET: List all subscribers (admin only via cookie check)
export async function GET(request: NextRequest) {
  try {
    const { cookies } = await import('next/headers');
    const cookieStore = await cookies();
    const authCookie = cookieStore.get('hermes_auth');
    if (authCookie?.value !== '1') {
      return NextResponse.json({ success: false, error: 'Neautorizat' }, { status: 401 });
    }

    const subs = loadSubscribers();
    return NextResponse.json({ success: true, subscribers: subs });
  } catch {
    return NextResponse.json({ success: false, error: 'Eroare server' }, { status: 500 });
  }
}
