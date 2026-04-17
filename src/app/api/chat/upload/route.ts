import { NextRequest, NextResponse } from 'next/server';
import { writeFile, mkdir, readdir, stat, unlink } from 'fs';
import { join } from 'path';
import { readFileSync } from 'fs';
import path from 'path';

const CHAT_FILES_DIR = join(process.cwd(), 'chat_uploads');

function ensureDir() {
  try { mkdir(CHAT_FILES_DIR, { recursive: true }); } catch {}
}

// POST — Upload file to chat
export async function POST(request: NextRequest) {
  try {
    ensureDir();
    const formData = await request.formData();
    const file = formData.get('file') as File | null;

    if (!file) {
      return NextResponse.json({ error: 'No file provided' }, { status: 400 });
    }

    const bytes = await file.arrayBuffer();
    const buffer = Buffer.from(bytes);

    // Sanitize filename
    const sanitizedName = file.name.replace(/[^a-zA-Z0-9._-]/g, '_');
    const uniqueName = `${Date.now()}_${sanitizedName}`;
    const filePath = join(CHAT_FILES_DIR, uniqueName);

    writeFile(filePath, buffer, (err) => {
      if (err) console.error('File write error:', err);
    });

    // Extract text preview for text files
    let contentPreview: string | null = null;
    const textExts = ['.txt', '.py', '.js', '.ts', '.json', '.md', '.html', '.css', '.csv', '.yml', '.yaml', '.xml', '.sql', '.log', '.env', '.java', '.c', '.cpp', '.cs', '.go', '.rs', '.tsx', '.jsx', '.sh', '.bash', '.toml', '.ini', '.cfg', '.conf', '.rb', '.php', '.swift', '.kt', '.r', '.lua', '.pl', '.ps1', '.dart', '.zig'];
    const ext = path.extname(sanitizedName).toLowerCase();
    if (textExts.includes(ext)) {
      try {
        contentPreview = buffer.toString('utf-8').substring(0, 5000);
      } catch {}
    }

    return NextResponse.json({
      success: true,
      file: {
        name: sanitizedName,
        originalName: file.name,
        size: buffer.length,
        type: file.type,
        path: uniqueName,
        contentPreview,
      },
    });
  } catch (error: any) {
    return NextResponse.json({ error: 'Upload failed', details: error.message }, { status: 500 });
  }
}

// GET — List uploaded files
export async function GET() {
  try {
    ensureDir();
    const files = readdir(CHAT_FILES_DIR)
      .filter(f => !f.startsWith('.'))
      .map(name => {
        try {
          const filePath = join(CHAT_FILES_DIR, name);
          const s = stat(filePath);
          const ext = path.extname(name).toLowerCase();
          let contentPreview: string | null = null;
          const textExts = ['.txt', '.py', '.js', '.ts', '.json', '.md', '.html', '.css', '.csv', '.yml', '.yaml', '.xml', '.sql', '.sh', '.toml', '.ini', '.cfg', '.conf', '.rb', '.php', '.go', '.rs', '.tsx', '.jsx', '.c', '.cpp', '.java'];
          if (textExts.includes(ext)) {
            try { contentPreview = readFileSync(filePath, 'utf-8').substring(0, 2000); } catch {}
          }
          return {
            name,
            size: s.size,
            modified: s.mtime.toISOString(),
            type: ext,
            contentPreview,
          };
        } catch { return null; }
      })
      .filter(Boolean)
      .sort((a: any, b: any) => new Date(b.modified).getTime() - new Date(a.modified).getTime());

    return NextResponse.json({ success: true, files });
  } catch (error: any) {
    return NextResponse.json({ error: 'List failed', details: error.message }, { status: 500 });
  }
}

// DELETE — Remove a file
export async function DELETE(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const fileName = searchParams.get('file');
    if (!fileName) return NextResponse.json({ error: 'File name required' }, { status: 400 });

    // Sanitize: prevent path traversal
    const safeName = fileName.replace(/[^a-zA-Z0-9._-]/g, '');
    const filePath = join(CHAT_FILES_DIR, safeName);

    try { unlink(filePath); } catch {}
    return NextResponse.json({ success: true });
  } catch (error: any) {
    return NextResponse.json({ error: 'Delete failed', details: error.message }, { status: 500 });
  }
}
