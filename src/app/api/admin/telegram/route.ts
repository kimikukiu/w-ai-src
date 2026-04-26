import { NextRequest, NextResponse } from 'next/server';

export async function GET() {
  return NextResponse.json({
    enabled: process.env.TELEGRAM_ENABLED === 'true',
    botToken: process.env.TELEGRAM_BOT_TOKEN ? '***' + process.env.TELEGRAM_BOT_TOKEN.slice(-5) : null,
    adminIds: process.env.TELEGRAM_ADMIN_IDS || '',
  });
}

export async function POST(request: NextRequest) {
  try {
    const { botToken, adminIds, enabled } = await request.json();

    return NextResponse.json({ success: true });
  } catch {
    return NextResponse.json({ success: false, error: 'Server error' }, { status: 500 });
  }
}
