import { NextRequest, NextResponse } from 'next/server';
import { loadConfig, saveConfig } from '@/lib/config';
import { emit, SyncEvent } from '@/lib/sync-bus';

export interface Webhook {
  id: string;
  url: string;
  events: string[];
  secret: string;
  active: boolean;
  createdAt: number;
}

const WEBHOOKS_FILE = () => {
  const { join } = require('path');
  const { DATA_DIR } = require('@/lib/bot-engine');
  return join(DATA_DIR, 'webhooks.json');
};

function loadWebhooks(): Webhook[] {
  try {
    const { existsSync, readFileSync } = require('fs');
    const f = WEBHOOKS_FILE();
    if (!existsSync(f)) return [];
    return JSON.parse(readFileSync(f, 'utf-8'));
  } catch { return []; }
}

function saveWebhooks(hooks: Webhook[]) {
  try {
    const { writeFileSync } = require('fs');
    writeFileSync(WEBHOOKS_FILE(), JSON.stringify(hooks, null, 2), 'utf-8');
  } catch {}
}

function createWebhookSecret(): string {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let s = '';
  for (let i = 0; i < 32; i++) s += chars[Math.floor(Math.random() * chars.length)];
  return s;
}

export async function GET(request: NextRequest) {
  try {
    const hooks = loadWebhooks();
    const masked = hooks.map(h => ({ ...h, secret: h.secret.slice(0, 8) + '...' }));
    return NextResponse.json({ webhooks: masked, count: hooks.length });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const { url, events } = body;

    if (!url || !events || !Array.isArray(events)) {
      return NextResponse.json({ error: 'url and events[] required' }, { status: 400 });
    }

    const hooks = loadWebhooks();
    const id = `wh_${Date.now().toString(36)}`;
    const secret = createWebhookSecret();
    const webhook: Webhook = {
      id, url, events, secret,
      active: true,
      createdAt: Date.now(),
    };
    hooks.push(webhook);
    saveWebhooks(hooks);

    emit('config.updated', 'web', { action: 'webhook_added', webhookId: id });

    return NextResponse.json({
      ok: true,
      webhook: { ...webhook, secret: webhook.secret.slice(0, 8) + '...' },
      message: 'Webhook registered. Use the secret to verify payloads.',
    });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function DELETE(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const id = searchParams.get('id');
    if (!id) return NextResponse.json({ error: 'id required' }, { status: 400 });

    let hooks = loadWebhooks();
    const before = hooks.length;
    hooks = hooks.filter(h => h.id !== id);
    if (hooks.length === before) return NextResponse.json({ error: 'Webhook not found' }, { status: 404 });

    saveWebhooks(hooks);
    emit('config.updated', 'web', { action: 'webhook_removed', webhookId: id });

    return NextResponse.json({ ok: true });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function PUT(request: NextRequest) {
  try {
    const body = await request.json();
    const { id, active, url, events } = body;
    if (!id) return NextResponse.json({ error: 'id required' }, { status: 400 });

    let hooks = loadWebhooks();
    const idx = hooks.findIndex((h: Webhook) => h.id === id);
    if (idx < 0) return NextResponse.json({ error: 'Webhook not found' }, { status: 404 });

    if (active !== undefined) hooks[idx].active = active;
    if (url) hooks[idx].url = url;
    if (events) hooks[idx].events = events;
    saveWebhooks(hooks);

    return NextResponse.json({ ok: true, webhook: hooks[idx] });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function POST_handler(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const id = searchParams.get('id');
    const action = searchParams.get('(') || request.headers.get('x-webhook-test');

    if (action === 'test') {
      const hooks = loadWebhooks();
      const hook = hooks.find((h: Webhook) => h.id === id);
      if (!hook) return NextResponse.json({ error: 'Not found' }, { status: 404 });

      const payload = { test: true, webhookId: id, timestamp: Date.now() };
      const sig = require('crypto').createHmac('sha256', hook.secret).update(JSON.stringify(payload)).digest('hex');

      await fetch(hook.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Webhook-Signature': sig },
        body: JSON.stringify(payload),
      }).catch(() => {});

      return NextResponse.json({ ok: true, sent: true });
    }

    return NextResponse.json({ error: 'Unknown action' }, { status: 400 });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}
