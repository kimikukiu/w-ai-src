import { NextRequest, NextResponse } from 'next/server';
import { readdir, readFile } from 'fs/promises';
import { existsSync, readFileSync } from 'fs';
import { join, extname } from 'path';

const SWARM_MODELS_DIR = join(process.cwd(), 'swarm-models');
const SWARM_MODELS_DIR_S2 = join(SWARM_MODELS_DIR, 'section2-swarm-models');
const BYPASS_TOOLS_DIR = join(SWARM_MODELS_DIR, 'byp-all-tools');

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const category = searchParams.get('category') || 'all';
    const file = searchParams.get('file');
    const list = searchParams.get('list') === 'true';
    const tools = searchParams.get('tools') === 'true';

    if (file) {
      const safeFile = file.replace(/[^a-zA-Z0-9-_.]/g, '');
      const filePath = join(SWARM_MODELS_DIR, safeFile);
      const filePath2 = join(SWARM_MODELS_DIR_S2, safeFile);
      const filePath3 = join(BYPASS_TOOLS_DIR, safeFile);

      let content: string | null = null;
      if (existsSync(filePath3)) content = readFileSync(filePath3, 'utf-8');
      else if (existsSync(filePath2)) content = readFileSync(filePath2, 'utf-8');
      else if (existsSync(filePath)) content = readFileSync(filePath, 'utf-8');

      if (content !== null) {
        const ext = extname(safeFile).toLowerCase();
        const contentType = ext === '.py' ? 'text/x-python' : ext === '.rar' ? 'application/x-rar-compressed' : ext === '.js' ? 'text/javascript' : 'text/plain';
        return new NextResponse(content, {
          headers: { 'Content-Type': `${contentType}; charset=utf-8` },
        });
      }
      return NextResponse.json({ error: 'File not found' }, { status: 404 });
    }

    const rootFiles: { name: string; size: number; type: string }[] = [];
    const section2Files: { name: string; size: number; type: string }[] = [];
    const toolFiles: { name: string; size: number; type: string }[] = [];

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
    if (tools) {
      try {
        const toolEntries = await readdir(BYPASS_TOOLS_DIR, { withFileTypes: true });
        for (const entry of toolEntries) {
          if (entry.isFile()) {
            toolFiles.push({
              name: entry.name,
              size: 0,
              type: extname(entry.name).slice(1) || 'file',
            });
          }
        }
      } catch {}
      return NextResponse.json({ files: toolFiles, total: toolFiles.length });
    }

    return NextResponse.json({
      categories: ['root', 'section2', 'tools'],
      root: { files: rootFiles, count: rootFiles.length },
      section2: { files: section2Files, count: section2Files.length },
      tools: { count: toolFiles.length },
      total: rootFiles.length + section2Files.length,
    });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}
