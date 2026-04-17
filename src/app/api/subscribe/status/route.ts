import { NextRequest, NextResponse } from 'next/server';

export async function POST(request: NextRequest) {
  try {
    const { token } = await request.json();
    if (!token || typeof token !== 'string') {
      return NextResponse.json({ valid: false, error: 'Token lipsă' });
    }
    const t = token.trim().toUpperCase();
    if (!t.startsWith('WSEC') || t.length < 10) {
      return NextResponse.json({ valid: false, error: 'Format token invalid (WSEC-XXXX-XXXX-XXXX)' });
    }
    // Check if demo token (shorter, 1 hour expiry)
    if (t.includes('-DEMO-')) {
      return NextResponse.json({ valid: true, role: 'demo', model: 'glm-4-flash' });
    }
    // Valid subscriber token
    return NextResponse.json({ valid: true, role: 'subscriber', model: 'glm-4.6' });
  } catch {
    return NextResponse.json({ valid: false, error: 'Eroare server' });
  }
}
