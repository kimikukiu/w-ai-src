import { NextRequest, NextResponse } from 'next/server';
import { getEvents, SyncEvent } from '@/lib/sync-bus';

export async function GET(request: NextRequest) {
  try {
    const authKey = request.headers.get('x-api-key');
    if (!authKey) {
      return NextResponse.json({ error: 'API key required' }, { status: 401 });
    }

    const { searchParams } = new URL(request.url);
    const since = parseInt(searchParams.get('since') || '0');

    const stream = new ReadableStream({
      start(controller) {
        const encoder = new TextEncoder();
        let lastEventId = since;

        function send(event: SyncEvent) {
          try {
            controller.enqueue(encoder.encode(`data: ${JSON.stringify(event)}\n\n`));
            lastEventId = event.timestamp;
          } catch {}
        }

        const check = () => {
          const events = getEvents(lastEventId);
          for (const e of events) {
            if (e.timestamp > lastEventId) {
              send(e);
              lastEventId = e.timestamp;
            }
          }
        };

        check();
        const interval = setInterval(check, 1000);

        const close = () => clearInterval(interval);
        request.signal.addEventListener('abort', close);
      }
    });

    return new Response(stream, {
      headers: {
        'Content-Type': 'text/event-stream',
        'Cache-Control': 'no-cache',
        'Connection': 'keep-alive',
      },
    });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const events = getEvents(body.since, body.type);
    return NextResponse.json({ events, count: events.length });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}
