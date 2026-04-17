import { NextRequest, NextResponse } from 'next/server';
import { readFileSync, writeFileSync, existsSync, mkdirSync } from 'fs';
import { join } from 'path';

const DATA_DIR = join(process.cwd(), 'data');
const PAYMENTS_FILE = join(DATA_DIR, 'payments.json');

function ensureDataDir() {
  if (!existsSync(DATA_DIR)) mkdirSync(DATA_DIR, { recursive: true });
}

function loadPayments(): any[] {
  ensureDataDir();
  try {
    if (existsSync(PAYMENTS_FILE)) return JSON.parse(readFileSync(PAYMENTS_FILE, 'utf-8'));
  } catch {}
  return [];
}

function savePayments(payments: any[]) {
  ensureDataDir();
  writeFileSync(PAYMENTS_FILE, JSON.stringify(payments, null, 2), 'utf-8');
}

export async function POST(request: NextRequest) {
  try {
    const { plan, tx_hash, wallet, contact } = await request.json();

    if (!plan || !tx_hash || !wallet) {
      return NextResponse.json({ success: false, error: 'Plan, TX hash și wallet sunt obligatorii' }, { status: 400 });
    }

    const validPlans = ['pro', 'enterprise'];
    if (!validPlans.includes(plan)) {
      return NextResponse.json({ success: false, error: 'Plan invalid. Alege: pro sau enterprise' }, { status: 400 });
    }

    const payments = loadPayments();

    // Check duplicate TX hash
    if (payments.some((p: any) => p.tx_hash === tx_hash)) {
      return NextResponse.json({ success: false, error: 'Acest TX hash a fost deja înregistrat' }, { status: 409 });
    }

    const payment = {
      id: `PAY-${Date.now()}-${Math.random().toString(36).slice(2, 8).toUpperCase()}`,
      plan,
      tx_hash: tx_hash.trim(),
      wallet: wallet.trim(),
      contact: contact || '',
      status: 'pending',
      amount: plan === 'pro' ? 25 : 75,
      currency: plan === 'pro' ? 'EUR (XMR/USDT)' : 'EUR (XMR/USDT)',
      created_at: new Date().toISOString(),
      verified_at: null,
      token: null,
    };

    payments.push(payment);
    savePayments(payments);

    return NextResponse.json({
      success: true,
      message: 'Plată înregistrată cu succes! Verificarea manuală se face în maxim 24h.',
      payment_id: payment.id,
      next_steps: [
        '1. Plata ta a fost înregistrată',
        '2. Verificarea se face manual în maxim 24 de ore',
        '3. Vei primi token-ul WSEC prin t.me/loghandelbot',
        '4. Contactează t.me/loghandelbot pentru confirmare rapidă',
      ],
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: 'Eroare server' }, { status: 500 });
  }
}

// GET: Admin list payments
export async function GET() {
  try {
    const payments = loadPayments();
    return NextResponse.json({ success: true, payments });
  } catch {
    return NextResponse.json({ success: false, error: 'Eroare server' }, { status: 500 });
  }
}
