export interface SkillFile {
  path: string;
  name: string;
  content: string;
  format: SkillFormat;
}

export type SkillFormat = 'codex' | 'claude' | 'openclaw' | 'opencode' | 'unknown';

export interface SecurityFinding {
  severity: 'critical' | 'high' | 'medium' | 'low' | 'info';
  category: string;
  message: string;
  line?: number;
  pattern?: string;
}

export interface QualityFinding {
  category: string;
  message: string;
  suggestion?: string;
}

export interface SkillAuditResult {
  path: string;
  name: string;
  format: SkillFormat;
  security: SecurityFinding[];
  quality: QualityFinding[];
  metadata: SkillMetadata;
  score: AuditScore;
}

export interface SkillMetadata {
  hasDescription: boolean;
  hasExamples: boolean;
  hasManifest: boolean;
  hasScripts: boolean;
  scriptCount: number;
  lineCount: number;
  commandCount: number;
}

export interface AuditScore {
  security: number;
  quality: number;
  overall: number;
  grade: 'A' | 'B' | 'C' | 'D' | 'F';
}

export interface AuditSummary {
  totalSkills: number;
  criticalCount: number;
  highCount: number;
  mediumCount: number;
  lowCount: number;
  averageScore: number;
  byFormat: Record<SkillFormat, number>;
}
