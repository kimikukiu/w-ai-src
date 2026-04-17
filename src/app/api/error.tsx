import { NextResponse } from 'next/server';

// ═══════════════════════════════════════════════
// GLOBAL API ERROR HANDLER — Returns JSON instead of HTML
// Prevents "Unexpected token '<'" crashes on the frontend
// ═══════════════════════════════════════════════
export default function errorHandler(error: Error & { digest?: string }) {
  console.error('[API Error Handler]', error);

  // Check if the error looks like it came from a client API request
  const url = error.message || '';
  const isApiRoute = true; // This file is in the API directory tree

  return NextResponse.json(
    {
      success: false,
      error: 'Internal server error. Please try again.',
      details: error.message?.substring(0, 200) || 'Unknown error',
    },
    { status: 500 }
  );
}
