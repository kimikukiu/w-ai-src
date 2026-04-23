import * as fs from 'fs';
import * as path from 'path';
import { glob } from 'glob';
import { SkillFile, SkillAuditResult, AuditSummary, SkillFormat } from './types.js';
import { detectFormat, getSkillName } from './detector.js';
import { analyzeAllSecurity } from './security.js';
import { analyzeQuality, extractMetadata } from './quality.js';
import { calculateScore } from './scorer.js';

export async function discoverSkills(basePath: string): Promise<SkillFile[]> {
  const skills: SkillFile[] = [];
  
  // Look for various skill file patterns
  const patterns = [
    '**/SKILL.md',
    '**/skill.yaml',
    '**/skill.yml',
    '**/manifest.json',
    '**/.claude/commands/*.md',
    '**/skills/**/README.md'
  ];
  
  for (const pattern of patterns) {
    const files = await glob(pattern, { 
      cwd: basePath, 
      absolute: true,
      ignore: ['**/node_modules/**', '**/dist/**', '**/.git/**']
    });
    
    for (const file of files) {
      try {
        const content = fs.readFileSync(file, 'utf-8');
        const format = detectFormat(file, content);
        const name = getSkillName(file, content);
        
        // Avoid duplicates
        if (!skills.find(s => s.path === file)) {
          skills.push({ path: file, name, content, format });
        }
      } catch (err) {
        // Skip files we can't read
      }
    }
  }
  
  return skills;
}

export async function auditSkill(skill: SkillFile): Promise<SkillAuditResult> {
  const dir = fs.statSync(skill.path).isDirectory() 
    ? skill.path 
    : path.dirname(skill.path);
  
  let dirFiles: string[] = [];
  try {
    dirFiles = fs.readdirSync(dir);
  } catch {}
  
  // Collect all content from skill directory
  let fullContent = skill.content;
  for (const file of dirFiles) {
    if (file.endsWith('.sh') || file.endsWith('.py') || file.endsWith('.js') || file.endsWith('.md')) {
      try {
        fullContent += '\n' + fs.readFileSync(path.join(dir, file), 'utf-8');
      } catch {}
    }
  }
  
  const security = analyzeAllSecurity(fullContent);
  const quality = analyzeQuality(skill.content);
  const metadata = extractMetadata(skill.content, dirFiles);
  const score = calculateScore(security, quality, metadata);
  
  return {
    path: skill.path,
    name: skill.name,
    format: skill.format,
    security,
    quality,
    metadata,
    score
  };
}

export async function auditPath(basePath: string): Promise<SkillAuditResult[]> {
  const skills = await discoverSkills(basePath);
  const results: SkillAuditResult[] = [];
  
  for (const skill of skills) {
    results.push(await auditSkill(skill));
  }
  
  return results;
}

export function summarize(results: SkillAuditResult[]): AuditSummary {
  const byFormat: Record<SkillFormat, number> = {
    codex: 0,
    claude: 0,
    openclaw: 0,
    opencode: 0,
    unknown: 0
  };
  
  let criticalCount = 0;
  let highCount = 0;
  let mediumCount = 0;
  let lowCount = 0;
  let totalScore = 0;
  
  for (const result of results) {
    byFormat[result.format]++;
    totalScore += result.score.overall;
    
    for (const finding of result.security) {
      switch (finding.severity) {
        case 'critical': criticalCount++; break;
        case 'high': highCount++; break;
        case 'medium': mediumCount++; break;
        case 'low': lowCount++; break;
      }
    }
  }
  
  return {
    totalSkills: results.length,
    criticalCount,
    highCount,
    mediumCount,
    lowCount,
    averageScore: results.length > 0 ? Math.round(totalScore / results.length) : 0,
    byFormat
  };
}
