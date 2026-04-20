import { NextRequest, NextResponse } from 'next/server';
import { emit, SyncEvent } from '@/lib/sync-bus';
import { loadConfig } from '@/lib/config';

const SYNC_SECRET = process.env.SYNC_SECRET || 'internal-sync-key';

export async function POST(request: NextRequest) {
  try {
    const secret = request.headers.get('x-sync-secret');
    if (secret !== SYNC_SECRET) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const event: SyncEvent = await request.json();

    if (!event.type || !event.source) {
      return NextResponse.json({ error: 'Invalid event' }, { status: 400 });
    }

    emit(event.type, event.source, event.payload || {});

    if (event.type === 'config.updated') {
      const config = loadConfig();
      if (event.payload.glm_model && event.payload.glm_model !== config.glm_model) {
        config.glm_model = event.payload.glm_model;
      }
      if (event.payload.glm_endpoint && event.payload.glm_endpoint !== config.glm_endpoint) {
        config.glm_endpoint = event.payload.glm_endpoint;
      }
    }

    return NextResponse.json({ received: true, eventId: event.id });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function GET(request: NextRequest) {
  try {
    const secret = request.headers.get('x-sync-secret');
    if (secret !== SYNC_SECRET) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const { searchParams } = new URL(request.url);
    const since = parseInt(searchParams.get('since') || '0');
    const type = searchParams.get('type') || undefined;

    const events = []; // getEvents(since, type as any);

    return NextResponse.json({ events });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}
