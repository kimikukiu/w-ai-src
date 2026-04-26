import { NextRequest, NextResponse } from 'next/server';

const subscribers: Map<string, any> = new Map();

export async function GET() {
  const list = Array.from(subscribers.values());
  return NextResponse.json({ subscribers: list });
}

export async function POST(request: NextRequest) {
  try {
    const { telegramId, plan, action } = await request.json();

    if (action === 'add') {
      subscribers.set(telegramId, {
        telegramId,
        plan: plan || 'free',
        active: true,
        createdAt: new Date().toISOString(),
        expiresAt: null,
      });
    } else if (action === 'remove') {
      subscribers.delete(telegramId);
    }

    return NextResponse.json({ success: true });
  } catch {
    return NextResponse.json({ success: false, error: 'Server error' }, { status: 500 });
  }
}
