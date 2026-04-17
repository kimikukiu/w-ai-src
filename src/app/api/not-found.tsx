import { NextResponse } from 'next/server';

// ═══════════════════════════════════════════════
// API 404 HANDLER — Returns JSON instead of HTML
// ═══════════════════════════════════════════════
export default function notFound() {
  return NextResponse.json(
    { success: false, error: 'API endpoint not found' },
    { status: 404 }
  );
}
