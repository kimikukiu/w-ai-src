import chalk from 'chalk';
import { SkillAuditResult, AuditSummary, SecurityFinding } from './types.js';

function severityColor(severity: SecurityFinding['severity']): (text: string) => string {
  switch (severity) {
    case 'critical': return chalk.bgRed.white.bold;
    case 'high': return chalk.red;
    case 'medium': return chalk.yellow;
    case 'low': return chalk.blue;
    case 'info': return chalk.gray;
  }
}

function gradeColor(grade: string): (text: string) => string {
  switch (grade) {
    case 'A': return chalk.green.bold;
    case 'B': return chalk.green;
    case 'C': return chalk.yellow;
    case 'D': return chalk.red;
    case 'F': return chalk.bgRed.white.bold;
    default: return chalk.white;
  }
}

export function printResult(result: SkillAuditResult, verbose: boolean = false): void {
  const gradeStr = gradeColor(result.score.grade)(result.score.grade);
  const scoreStr = `${result.score.overall}/100`;
  
  console.log();
  console.log(chalk.bold(`${result.name}`) + chalk.dim(` (${result.format})`));
  console.log(chalk.dim(result.path));
  console.log(`  Grade: ${gradeStr}  Score: ${scoreStr}`);
  console.log(`  Security: ${result.score.security}/100  Quality: ${result.score.quality}/100`);
  
  // Security findings
  if (result.security.length > 0) {
    console.log();
    console.log(chalk.bold('  Security Findings:'));
    for (const finding of result.security) {
      const badge = severityColor(finding.severity)(` ${finding.severity.toUpperCase()} `);
      const line = finding.line ? chalk.dim(`:${finding.line}`) : '';
      console.log(`    ${badge} [${finding.category}] ${finding.message}${line}`);
    }
  }
  
  // Quality findings (verbose only or if no security issues)
  if (verbose && result.quality.length > 0) {
    console.log();
    console.log(chalk.bold('  Quality Suggestions:'));
    for (const finding of result.quality) {
      console.log(`    ${chalk.cyan('*')} [${finding.category}] ${finding.message}`);
      if (finding.suggestion) {
        console.log(chalk.dim(`      ${finding.suggestion}`));
      }
    }
  }
  
  // Metadata (verbose only)
  if (verbose) {
    console.log();
    console.log(chalk.bold('  Metadata:'));
    console.log(`    Lines: ${result.metadata.lineCount}`);
    console.log(`    Commands: ${result.metadata.commandCount}`);
    console.log(`    Scripts: ${result.metadata.scriptCount}`);
    console.log(`    Has description: ${result.metadata.hasDescription ? chalk.green('yes') : chalk.red('no')}`);
    console.log(`    Has examples: ${result.metadata.hasExamples ? chalk.green('yes') : chalk.red('no')}`);
  }
}

export function printSummary(summary: AuditSummary): void {
  console.log();
  console.log(chalk.bold.underline('Audit Summary'));
  console.log();
  console.log(`  Skills audited: ${chalk.bold(summary.totalSkills.toString())}`);
  console.log(`  Average score: ${chalk.bold(summary.averageScore.toString())}/100`);
  console.log();
  
  // Findings breakdown
  console.log(chalk.bold('  Findings:'));
  if (summary.criticalCount > 0) {
    console.log(`    ${chalk.bgRed.white.bold(' CRITICAL ')} ${summary.criticalCount}`);
  }
  if (summary.highCount > 0) {
    console.log(`    ${chalk.red('HIGH')}     ${summary.highCount}`);
  }
  if (summary.mediumCount > 0) {
    console.log(`    ${chalk.yellow('MEDIUM')}   ${summary.mediumCount}`);
  }
  if (summary.lowCount > 0) {
    console.log(`    ${chalk.blue('LOW')}      ${summary.lowCount}`);
  }
  
  if (summary.criticalCount === 0 && summary.highCount === 0 && 
      summary.mediumCount === 0 && summary.lowCount === 0) {
    console.log(`    ${chalk.green('No security issues found!')}`);
  }
  
  // Format breakdown
  console.log();
  console.log(chalk.bold('  By Format:'));
  for (const [format, count] of Object.entries(summary.byFormat)) {
    if (count > 0) {
      console.log(`    ${format}: ${count}`);
    }
  }
}

export function printJson(results: SkillAuditResult[], summary: AuditSummary): void {
  console.log(JSON.stringify({ results, summary }, null, 2));
}
