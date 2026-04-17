import { NextRequest, NextResponse } from 'next/server';

export async function POST(request: NextRequest) {
  try {
    const { token, action } = await request.json();
    if (!token || typeof token !== 'string') {
      return NextResponse.json({ success: false, error: 'Token lipsă' });
    }
    const t = token.trim().toUpperCase();
    if (!t.startsWith('WSEC')) {
      return NextResponse.json({ success: false, error: 'Format token invalid' });
    }
    if (action === 'check') {
      return NextResponse.json({ valid: true, token: t });
    }
    return NextResponse.json({ success: true, message: 'Token procesat' });
  } catch {
    return NextResponse.json({ success: false, error: 'Eroare server' });
  }
}
