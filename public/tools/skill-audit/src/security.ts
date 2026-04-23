import { SecurityFinding } from './types.js';

interface SecurityPattern {
  pattern: RegExp;
  severity: SecurityFinding['severity'];
  category: string;
  message: string;
}

const SECURITY_PATTERNS: SecurityPattern[] = [
  // Critical: Remote code execution
  {
    pattern: /eval\s*\(/gi,
    severity: 'critical',
    category: 'code-execution',
    message: 'eval() allows arbitrary code execution'
  },
  {
    pattern: /exec\s*\(\s*[`"'].*\$\{/gi,
    severity: 'critical',
    category: 'code-execution',
    message: 'Shell command with string interpolation - potential injection'
  },
  {
    pattern: /child_process|spawn|execSync|execFile/gi,
    severity: 'high',
    category: 'code-execution',
    message: 'Direct process spawning detected'
  },
  
  // High: File system access
  {
    pattern: /rm\s+-rf?\s+[\/~]/gi,
    severity: 'critical',
    category: 'filesystem',
    message: 'Recursive deletion of system paths'
  },
  {
    pattern: /fs\.(unlink|rmdir|rm)Sync?\s*\(/gi,
    severity: 'high',
    category: 'filesystem',
    message: 'File deletion operation'
  },
  {
    pattern: /writeFile|appendFile|createWriteStream/gi,
    severity: 'medium',
    category: 'filesystem',
    message: 'File write operation - ensure paths are validated'
  },
  {
    pattern: /\/etc\/passwd|\/etc\/shadow|\.ssh\/|\.env/gi,
    severity: 'critical',
    category: 'filesystem',
    message: 'Access to sensitive system files'
  },
  
  // High: Network access
  {
    pattern: /curl\s+.*\|\s*(sh|bash|zsh)/gi,
    severity: 'critical',
    category: 'network',
    message: 'Pipe curl to shell - extremely dangerous'
  },
  {
    pattern: /wget|curl\s+-O|fetch\s*\(/gi,
    severity: 'medium',
    category: 'network',
    message: 'Network download detected'
  },
  {
    pattern: /https?:\/\/[^\s"']+\.sh/gi,
    severity: 'high',
    category: 'network',
    message: 'Reference to remote shell script'
  },
  
  // Medium: Credential exposure
  {
    pattern: /api[_-]?key|secret[_-]?key|password|token\s*[=:]/gi,
    severity: 'medium',
    category: 'credentials',
    message: 'Potential credential in skill definition'
  },
  {
    pattern: /OPENAI_API_KEY|ANTHROPIC_API_KEY|AWS_SECRET/gi,
    severity: 'high',
    category: 'credentials',
    message: 'Hardcoded API key reference'
  },
  
  // Medium: Privilege escalation
  {
    pattern: /sudo\s+/gi,
    severity: 'high',
    category: 'privilege',
    message: 'sudo usage - requires elevated privileges'
  },
  {
    pattern: /chmod\s+[0-7]*7[0-7]*/gi,
    severity: 'medium',
    category: 'privilege',
    message: 'Permissive chmod detected'
  },
  
  // Low: Information disclosure
  {
    pattern: /console\.log|print\(.*password|print\(.*secret/gi,
    severity: 'low',
    category: 'disclosure',
    message: 'Potential sensitive data logging'
  },
  
  // Info: Best practices
  {
    pattern: /TODO:|FIXME:|HACK:/gi,
    severity: 'info',
    category: 'maintenance',
    message: 'Unresolved TODO/FIXME comment'
  }
];

export function analyzeSecurityPatterns(content: string): SecurityFinding[] {
  const findings: SecurityFinding[] = [];
  const lines = content.split('\n');
  
  for (const pattern of SECURITY_PATTERNS) {
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i];
      if (pattern.pattern.test(line)) {
        findings.push({
          severity: pattern.severity,
          category: pattern.category,
          message: pattern.message,
          line: i + 1,
          pattern: pattern.pattern.source
        });
        // Reset regex lastIndex
        pattern.pattern.lastIndex = 0;
      }
    }
    // Reset for next pattern
    pattern.pattern.lastIndex = 0;
  }
  
  return findings;
}

export function analyzeShellCommands(content: string): SecurityFinding[] {
  const findings: SecurityFinding[] = [];
  
  // Find shell code blocks
  const shellBlocks = content.matchAll(/```(?:bash|sh|shell|zsh)\n([\s\S]*?)```/gi);
  
  for (const block of shellBlocks) {
    const code = block[1];
    const startLine = content.substring(0, block.index).split('\n').length;
    
    // Check for dangerous patterns in shell blocks
    if (/\|\s*sh\b|\|\s*bash\b/.test(code)) {
      findings.push({
        severity: 'critical',
        category: 'shell',
        message: 'Piping to shell interpreter',
        line: startLine
      });
    }
    
    if (/>\s*\/dev\/sd[a-z]/.test(code)) {
      findings.push({
        severity: 'critical',
        category: 'shell',
        message: 'Direct write to block device',
        line: startLine
      });
    }
    
    if (/:(){ :|:& };:/.test(code) || /fork\s*bomb/i.test(code)) {
      findings.push({
        severity: 'critical',
        category: 'shell',
        message: 'Fork bomb detected',
        line: startLine
      });
    }
  }
  
  return findings;
}

export function analyzeAllSecurity(content: string): SecurityFinding[] {
  return [
    ...analyzeSecurityPatterns(content),
    ...analyzeShellCommands(content)
  ].sort((a, b) => {
    const severityOrder = { critical: 0, high: 1, medium: 2, low: 3, info: 4 };
    return severityOrder[a.severity] - severityOrder[b.severity];
  });
}
