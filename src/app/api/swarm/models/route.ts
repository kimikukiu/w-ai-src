import { NextRequest, NextResponse } from 'next/server';
import { readdir, readFile } from 'fs/promises';
import { join, extname } from 'path';

const SWARM_MODELS_DIR = join(process.cwd(), 'swarm-models');
const SWARM_MODELS_DIR_S2 = join(SWARM_MODELS_DIR, 'section2-swarm-models');

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const category = searchParams.get('category') || 'all';
    const file = searchParams.get('file');
    const list = searchParams.get('list') === 'true';

    if (file) {
      const safeFile = file.replace(/[^a-zA-Z0-9-_.]/g, '');
      const filePath = join(SWARM_MODELS_DIR, safeFile);
      const filePath2 = join(SWARM_MODELS_DIR_S2, safeFile);

      const tryPath = filePath2 || filePath;
      const content = await readFile(tryPath, 'utf-8').catch(() => null);
      if (!content) {
        return NextResponse.json({ error: 'File not found' }, { status: 404 });
      }
      return new NextResponse(content, {
        headers: { 'Content-Type': 'text/html; charset=utf-8' },
      });
    }

    const rootFiles: { name: string; size: number; type: string }[] = [];
    const section2Files: { name: string; size: number; type: string }[] = [];

    try {
      const rootEntries = await readdir(SWARM_MODELS_DIR, { withFileTypes: true });
      for (const entry of rootEntries) {
        if (entry.isFile()) {
          rootFiles.push({
            name: entry.name,
            size: 0,
            type: extname(entry.name).slice(1) || 'file',
          });
        }
      }
    } catch {}

    try {
      const s2Entries = await readdir(SWARM_MODELS_DIR_S2, { withFileTypes: true });
      for (const entry of s2Entries) {
        if (entry.isFile()) {
          section2Files.push({
            name: entry.name,
            size: 0,
            type: extname(entry.name).slice(1) || 'file',
          });
        }
      }
    } catch {}

    if (category === 'section2') {
      return NextResponse.json({ files: section2Files, total: section2Files.length });
    }
    if (category === 'root') {
      return NextResponse.json({ files: rootFiles, total: rootFiles.length });
    }

    return NextResponse.json({
      categories: ['root', 'section2'],
      root: { files: rootFiles, count: rootFiles.length },
      section2: { files: section2Files, count: section2Files.length },
      total: rootFiles.length + section2Files.length,
    });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}
