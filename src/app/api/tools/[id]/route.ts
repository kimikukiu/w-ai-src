import { NextRequest, NextResponse } from 'next/server';
import { readFileSync, existsSync } from 'fs';
import { join } from 'path';

const BYP_ALL_TOOLS = join(process.cwd(), 'swarm-models', 'byp-all-tools');

export async function GET(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const toolId = decodeURIComponent(params.id);
    
    // Handle complete tools
    if (toolId === 'wormgpt-complete') {
      return NextResponse.redirect(new URL('/wormgpt-complete', request.url));
    }
    if (toolId === 'api-ob-complete') {
      return NextResponse.redirect(new URL('/api-ob-complete', request.url));
    }
    if (toolId === 'email-extractors-pro-complete') {
      return NextResponse.redirect(new URL('/email-extractors-pro-complete', request.url));
    }
    
    // Handle file-based tools
    const filePath = join(BYP_ALL_TOOLS, toolId);

    if (!existsSync(filePath)) {
      return new NextResponse('Tool not found', { status: 404 });
    }

    const content = readFileSync(filePath);
    const ext = toolId.split('.').pop()?.toLowerCase();

    const contentTypes: Record<string, string> = {
      'py': 'text/plain; charset=utf-8',
      'txt': 'text/plain; charset=utf-8',
      'html': 'text/html; charset=utf-8',
      'js': 'application/javascript; charset=utf-8',
      'json': 'application/json; charset=utf-8',
      'md': 'text/markdown; charset=utf-8',
      'php': 'text/plain; charset=utf-8',
      'zip': 'application/zip',
      'sql': 'text/plain; charset=utf-8',
      'css': 'text/css; charset=utf-8',
    };

    const contentType = contentTypes[ext || ''] || 'text/plain; charset=utf-8';

    return new NextResponse(content, {
      headers: {
        'Content-Type': contentType,
        'Content-Disposition': `inline; filename="${fileName}"`,
        'Cache-Control': 'public, max-age=3600',
      },
    });
  } catch (e: any) {
    return new NextResponse(`Error: ${e.message}`, { status: 500 });
  }
}
