import { QualityFinding, SkillMetadata } from './types.js';

export function analyzeQuality(content: string): QualityFinding[] {
  const findings: QualityFinding[] = [];
  
  // Check for description
  if (!content.includes('## Description') && !content.includes('# Description') && !content.match(/description:/i)) {
    findings.push({
      category: 'documentation',
      message: 'Missing description section',
      suggestion: 'Add a ## Description section explaining what this skill does'
    });
  }
  
  // Check for examples
  if (!content.includes('## Example') && !content.includes('# Example') && !content.includes('```')) {
    findings.push({
      category: 'documentation',
      message: 'No examples found',
      suggestion: 'Add code examples showing how to use this skill'
    });
  }
  
  // Check for usage instructions
  if (!content.includes('## Usage') && !content.includes('# Usage') && !content.includes('how to')) {
    findings.push({
      category: 'documentation',
      message: 'Missing usage instructions',
      suggestion: 'Add a ## Usage section with clear instructions'
    });
  }
  
  // Check line length
  const lines = content.split('\n');
  const longLines = lines.filter(l => l.length > 120).length;
  if (longLines > 5) {
    findings.push({
      category: 'readability',
      message: `${longLines} lines exceed 120 characters`,
      suggestion: 'Consider wrapping long lines for better readability'
    });
  }
  
  // Check for broken links
  const links = content.matchAll(/\[([^\]]+)\]\(([^)]+)\)/g);
  for (const link of links) {
    if (link[2].startsWith('#') && !content.includes(`# ${link[1]}`)) {
      findings.push({
        category: 'links',
        message: `Potentially broken anchor link: ${link[2]}`,
        suggestion: 'Verify the anchor target exists'
      });
    }
  }
  
  // Check for version pinning
  if (content.includes('npm install') || content.includes('pip install')) {
    if (!content.includes('@') && !content.includes('==')) {
      findings.push({
        category: 'dependencies',
        message: 'Dependencies not version-pinned',
        suggestion: 'Pin dependency versions for reproducibility'
      });
    }
  }
  
  // Check for error handling
  const hasShellScripts = /```(?:bash|sh)\n/i.test(content);
  if (hasShellScripts && !content.includes('set -e') && !content.includes('|| exit')) {
    findings.push({
      category: 'robustness',
      message: 'Shell scripts may not handle errors',
      suggestion: 'Add "set -e" or explicit error handling'
    });
  }
  
  // Check for input validation
  if ((content.includes('$1') || content.includes('$ARGUMENTS') || content.includes('input')) 
      && !content.includes('valid') && !content.includes('check')) {
    findings.push({
      category: 'robustness',
      message: 'No apparent input validation',
      suggestion: 'Consider validating user inputs before processing'
    });
  }
  
  return findings;
}

export function extractMetadata(content: string, dirFiles: string[] = []): SkillMetadata {
  const lines = content.split('\n');
  
  // Count shell/code commands
  const codeBlocks = content.matchAll(/```(?:bash|sh|shell|python|javascript|typescript)\n([\s\S]*?)```/gi);
  let commandCount = 0;
  for (const block of codeBlocks) {
    commandCount += block[1].split('\n').filter(l => l.trim() && !l.startsWith('#')).length;
  }
  
  return {
    hasDescription: /## Description|# Description|description:/i.test(content),
    hasExamples: /## Example|# Example|```/.test(content),
    hasManifest: dirFiles.includes('manifest.json') || dirFiles.includes('skill.yaml'),
    hasScripts: dirFiles.some(f => f.endsWith('.sh') || f.endsWith('.py') || f.endsWith('.js')),
    scriptCount: dirFiles.filter(f => f.endsWith('.sh') || f.endsWith('.py') || f.endsWith('.js')).length,
    lineCount: lines.length,
    commandCount
  };
}
