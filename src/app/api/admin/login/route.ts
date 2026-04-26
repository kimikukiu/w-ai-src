import { NextRequest, NextResponse } from 'next/server';
import crypto from 'crypto';

const ADMIN_HASH = '4a4b3c2d1e0f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a';
const SECRET_KEY = 'whoamisec-encryption-key-2024';

export async function POST(request: NextRequest) {
  try {
    const { password } = await request.json();

    const hash = crypto.createHash('sha256').update(password + SECRET_KEY).digest('hex');

    if (hash === ADMIN_HASH) {
      const token = crypto.randomBytes(32).toString('hex');
      return NextResponse.json({ success: true, token });
    }

    return NextResponse.json({ success: false, error: 'Invalid password' }, { status: 401 });
  } catch {
    return NextResponse.json({ success: false, error: 'Server error' }, { status: 500 });
  }
}
