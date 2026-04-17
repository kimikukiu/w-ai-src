import { NextRequest, NextResponse } from 'next/server';

const DEMO_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

function genToken() {
  const seg = (n: number) => Array.from({ length: n }, () => DEMO_CHARS[Math.floor(Math.random() * DEMO_CHARS.length)]).join('');
  return `WSEC-DEMO-${seg(4)}-${seg(4)}-${seg(4)}`;
}

export async function POST() {
  try {
    const token = genToken();
    return NextResponse.json({
      success: true,
      token,
      role: 'demo',
      requests: 10,
      duration: '1 oră',
      expires_at: new Date(Date.now() + 3600000).toISOString(),
    });
  } catch {
    return NextResponse.json({ success: false, error: 'Eroare la generare token' });
  }
}
