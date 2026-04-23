import { SecurityFinding, QualityFinding, SkillMetadata, AuditScore } from './types.js';

const SEVERITY_PENALTIES = {
  critical: 40,
  high: 20,
  medium: 10,
  low: 5,
  info: 0
};

export function calculateScore(
  security: SecurityFinding[],
  quality: QualityFinding[],
  metadata: SkillMetadata
): AuditScore {
  // Security score: start at 100, deduct for findings
  let securityScore = 100;
  for (const finding of security) {
    securityScore -= SEVERITY_PENALTIES[finding.severity];
  }
  securityScore = Math.max(0, securityScore);
  
  // Quality score: based on documentation and best practices
  let qualityScore = 100;
  
  // Deduct for missing documentation
  if (!metadata.hasDescription) qualityScore -= 15;
  if (!metadata.hasExamples) qualityScore -= 10;
  
  // Deduct for quality findings
  qualityScore -= quality.length * 5;
  qualityScore = Math.max(0, qualityScore);
  
  // Bonus for good practices
  if (metadata.hasManifest) qualityScore = Math.min(100, qualityScore + 5);
  if (metadata.hasScripts && metadata.scriptCount > 0) qualityScore = Math.min(100, qualityScore + 5);
  
  // Overall score: weighted average (security is more important)
  const overall = Math.round(securityScore * 0.6 + qualityScore * 0.4);
  
  // Letter grade
  let grade: AuditScore['grade'];
  if (overall >= 90) grade = 'A';
  else if (overall >= 80) grade = 'B';
  else if (overall >= 70) grade = 'C';
  else if (overall >= 60) grade = 'D';
  else grade = 'F';
  
  return {
    security: securityScore,
    quality: qualityScore,
    overall,
    grade
  };
}
