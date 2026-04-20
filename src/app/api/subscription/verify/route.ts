import { NextRequest, NextResponse } from 'next/server';
import { isValidSubscriber, isOwnerToken, getRemainingRequests, type SubscriptionStatus } from '@/lib/subscription-manager';
import { loadConfig } from '@/lib/config';

function getTokenFromRequest(request: NextRequest): string | null {
  const headerToken = request.headers.get('x-subscription-token');
  if (headerToken) return headerToken;

  const authHeader = request.headers.get('authorization');
  if (authHeader?.startsWith('Bearer ')) {
    return authHeader.slice(7);
  }

  return null;
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json().catch(() => ({}));
    const token = body.token || getTokenFromRequest(request);

    if (!token) {
      return NextResponse.json<SubscriptionStatus>({
        valid: false,
        role: 'invalid',
        message: 'Token lipseste. Trimite x-subscription-token header sau {token} in body.',
      }, { status: 401 });
    }

    const status = isValidSubscriber(token);

    if (status.valid && status.role !== 'admin') {
      const remaining = getRemainingRequests(token);
      if (remaining <= 0) {
        return NextResponse.json<SubscriptionStatus>({
          ...status,
          valid: false,
          role: 'expired',
          message: 'Ai atins limita de request-uri. Upgrade la un plan superior.',
        }, { status: 403 });
      }
    }

    return NextResponse.json({
      ...status,
      remainingRequests: status.valid ? getRemainingRequests(token) : 0,
    });
  } catch (e: any) {
    return NextResponse.json({
      valid: false,
      role: 'invalid',
      error: e.message,
    }, { status: 500 });
  }
}

export async function GET(request: NextRequest) {
  const token = getTokenFromRequest(request);
  if (!token) {
    const config = loadConfig();
    return NextResponse.json({
      authenticated: false,
      ownerId: !!config.owner_id,
      plans: ['demo', 'pro', 'enterprise'],
      demoRequests: 50,
      proRequests: 5000,
      enterpriseRequests: 'Infinite',
    });
  }

  const status = isValidSubscriber(token);
  return NextResponse.json({
    authenticated: status.valid,
    ...status,
    remainingRequests: status.valid ? getRemainingRequests(token) : 0,
  });
}
