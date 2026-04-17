import { NextRequest, NextResponse } from 'next/server';

export async function POST(request: NextRequest) {
  try {
    const { token } = await request.json();
    if (!token || typeof token !== 'string') {
      return NextResponse.json({ valid: false, error: 'Token lipsă' });
    }
    const t = token.trim().toUpperCase();
    if (!t.startsWith('WSEC') && t !== 'ADMIN-HERMES-V4') {
      return NextResponse.json({ valid: false, error: 'Format token invalid (WSEC-XXXX-XXXX-XXXX)' });
    }
    // Admin internal token
    if (t === 'ADMIN-HERMES-V4') {
      return NextResponse.json({ valid: true, role: 'admin', model: 'glm-4-plus' });
    }
    if (!t.startsWith('WSEC') || t.length < 10) {
      return NextResponse.json({ valid: false, error: 'Format token invalid (WSEC-XXXX-XXXX-XXXX)' });
    }
    // Check if demo token (shorter, 1 hour expiry)
    if (t.includes('-DEMO-')) {
      return NextResponse.json({ valid: true, role: 'demo', model: 'glm-4-flash' });
    }
    // Check pro token
    if (t.includes('-PRO-')) {
      return NextResponse.json({ valid: true, role: 'subscriber', model: 'glm-4.6' });
    }
    // Check enterprise token
    if (t.includes('-ENT-')) {
      return NextResponse.json({ valid: true, role: 'admin', model: 'hermes-4-70B' });
    }
    // Valid subscriber token
    return NextResponse.json({ valid: true, role: 'subscriber', model: 'glm-4.6' });
  } catch {
    return NextResponse.json({ valid: false, error: 'Eroare server' });
  }
}
