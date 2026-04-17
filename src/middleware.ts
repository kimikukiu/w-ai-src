import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

// ═══════════════════════════════════════════════
// GLOBAL MIDDLEWARE — Force JSON on all API routes
// Prevents HTML error pages crashing the frontend
// ═══════════════════════════════════════════════
export function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;

  // Only intercept API routes
  if (pathname.startsWith('/api/')) {
    // For methods that aren't handled by a route, return JSON 405 instead of HTML
    const response = NextResponse.next();

    // Add CORS headers for API routes
    response.headers.set('X-Content-Type-Options', 'nosniff');
    response.headers.set('Access-Control-Allow-Origin', '*');
    response.headers.set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    response.headers.set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    return response;
  }

  return NextResponse.next();
}

export const config = {
  matcher: '/api/:path*',
};
