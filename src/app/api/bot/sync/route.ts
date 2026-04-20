import { NextRequest, NextResponse } from 'next/server';
import { loadConfig } from '@/lib/config';
import { emit, SyncEvent } from '@/lib/sync-bus';

const INTERNAL_SECRET = process.env.SYNC_SECRET || 'internal-sync-key';

export async function POST(request: NextRequest) {
  try {
    const secret = request.headers.get('x-sync-secret');
    if (secret !== INTERNAL_SECRET) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const body = await request.json();
    const { type, source, payload } = body;

    if (!type || !source) {
      return NextResponse.json({ error: 'type and source required' }, { status: 400 });
    }

    const event = emit(type as any, source as any, payload || {});

    const baseUrl = (loadConfig() as any).NEXT_PUBLIC_BASE_URL;
    if (baseUrl) {
      try {
        await fetch(`${baseUrl}/api/internal/sync-event`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'x-sync-secret': INTERNAL_SECRET,
            'x-internal-origin': 'bot',
          },
          body: JSON.stringify(event),
        });
      } catch {}
    }

    return NextResponse.json({ ok: true, eventId: event.id });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function GET(request: NextRequest) {
  try {
    const secret = request.headers.get('x-sync-secret');
    if (secret !== INTERNAL_SECRET) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const { searchParams } = new URL(request.url);
    const since = parseInt(searchParams.get('since') || '0');
    const type = searchParams.get('type') || undefined;
    const events = []; // getEvents(since, type as any);

    return NextResponse.json({ events, count: events.length });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}
