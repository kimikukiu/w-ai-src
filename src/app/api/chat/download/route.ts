import { NextRequest, NextResponse } from 'next/server';
import { existsSync, createReadStream, readFileSync } from 'fs';
import { join } from 'path';

const CHAT_FILES_DIR = join(process.cwd(), 'chat_uploads');

// GET — Download a file by name
export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const fileName = searchParams.get('file');
    if (!fileName) return NextResponse.json({ error: 'File name required' }, { status: 400 });

    // Sanitize filename
    const safeName = fileName.replace(/[^a-zA-Z0-9._-]/g, '');
    const filePath = join(CHAT_FILES_DIR, safeName);

    if (!existsSync(filePath)) {
      return NextResponse.json({ error: 'File not found' }, { status: 404 });
    }

    // Check if text file — return content as JSON
    const textExts = ['.txt', '.py', '.js', '.ts', '.json', '.md', '.html', '.css', '.csv', '.yml', '.yaml', '.xml', '.sql', '.sh', '.toml', '.ini', '.cfg', '.conf', '.rb', '.php', '.go', '.rs', '.tsx', '.jsx', '.c', '.cpp', '.java'];
    const ext = fileName.split('.').pop()?.toLowerCase() || '';

    if (textExts.includes('.' + ext)) {
      try {
        const content = readFileSync(filePath, 'utf-8');
        return NextResponse.json({ success: true, name: safeName, content, type: 'text' });
      } catch {
        // Fall through to binary download
      }
    }

    // Binary file — return as downloadable
    const fileBuffer = readFileSync(filePath);
    const mimeType = getMimeType(ext);

    return new NextResponse(fileBuffer, {
      headers: {
        'Content-Type': mimeType,
        'Content-Disposition': `attachment; filename="${safeName}"`,
        'Content-Length': String(fileBuffer.length),
      },
    });
  } catch (error: any) {
    return NextResponse.json({ error: 'Download failed', details: error.message }, { status: 500 });
  }
}

function getMimeType(ext: string): string {
  const types: Record<string, string> = {
    pdf: 'application/pdf', png: 'image/png', jpg: 'image/jpeg', jpeg: 'image/jpeg',
    gif: 'image/gif', webp: 'image/webp', svg: 'image/svg+xml', zip: 'application/zip',
    '7z': 'application/x-7z-compressed', tar: 'application/x-tar', gz: 'application/gzip',
    mp4: 'video/mp4', mp3: 'audio/mpeg', wav: 'audio/wav', exe: 'application/octet-stream',
    bin: 'application/octet-stream', apk: 'application/vnd.android.package-archive',
    doc: 'application/msword', docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    xls: 'application/vnd.ms-excel', xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ppt: 'application/vnd.ms-powerpoint', pptx: 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  };
  return types[ext] || 'application/octet-stream';
}
