import { SkillFormat } from './types.js';
import * as fs from 'fs';
import * as path from 'path';

export function detectFormat(skillPath: string, content: string): SkillFormat {
  const dir = fs.statSync(skillPath).isDirectory() ? skillPath : path.dirname(skillPath);
  const files = fs.existsSync(dir) ? fs.readdirSync(dir) : [];
  
  // Check for format-specific markers
  if (files.includes('SKILL.md') || content.includes('# SKILL.md')) {
    return 'openclaw';
  }
  
  if (files.includes('manifest.json')) {
    try {
      const manifest = JSON.parse(fs.readFileSync(path.join(dir, 'manifest.json'), 'utf-8'));
      if (manifest.claude || manifest.commands) return 'claude';
      if (manifest.codex) return 'codex';
    } catch {}
  }
  
  if (files.includes('skill.yaml') || files.includes('skill.yml')) {
    return 'codex';
  }
  
  if (files.includes('opencode.json') || files.includes('.opencode')) {
    return 'opencode';
  }
  
  // Content-based detection
  if (content.includes('$ARGUMENTS') || content.includes('/command')) {
    return 'claude';
  }
  
  if (content.includes('## Instructions') && content.includes('## Scripts')) {
    return 'openclaw';
  }
  
  if (content.includes('agent_id') || content.includes('skill_id')) {
    return 'codex';
  }
  
  return 'unknown';
}

export function getSkillName(skillPath: string, content: string): string {
  const dir = fs.statSync(skillPath).isDirectory() ? skillPath : path.dirname(skillPath);
  
  // Try to extract from content
  const nameMatch = content.match(/^#\s+(.+?)(?:\s+-|$)/m);
  if (nameMatch) return nameMatch[1].trim();
  
  const titleMatch = content.match(/name:\s*["']?([^"'\n]+)/i);
  if (titleMatch) return titleMatch[1].trim();
  
  // Fall back to directory name
  return path.basename(dir);
}
