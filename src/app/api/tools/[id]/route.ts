import { NextRequest, NextResponse } from 'next/server';
import { readFileSync, existsSync } from 'fs';
import { join } from 'path';

const BYP_ALL_TOOLS = join(process.cwd(), 'swarm-models', 'byp-all-tools');

export async function GET(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const fileName = decodeURIComponent(params.id);
    const filePath = join(BYP_ALL_TOOLS, fileName);

    if (!existsSync(filePath)) {
      return new NextResponse('Tool not found', { status: 404 });
    }

    const content = readFileSync(filePath);
    const ext = fileName.split('.').pop()?.toLowerCase();

    const contentTypes: Record<string, string> = {
      'py': 'text/plain; charset=utf-8',
      'txt': 'text/plain; charset=utf-8',
      'html': 'text/html; charset=utf-8',
      'js': 'application/javascript; charset=utf-8',
      'json': 'application/json; charset=utf-8',
      'md': 'text/markdown; charset=utf-8',
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
