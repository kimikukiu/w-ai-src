#!/usr/bin/env node

import { Command } from 'commander';
import chalk from 'chalk';
import * as path from 'path';
import * as fs from 'fs';
import { auditPath, auditSkill, summarize, discoverSkills } from './auditor.js';
import { printResult, printSummary, printJson } from './reporter.js';
import { detectFormat, getSkillName } from './detector.js';

const program = new Command();

program
  .name('skill-audit')
  .description('Audit agent skill definitions for security, completeness, and compatibility')
  .version('1.0.0');

program
  .command('scan')
  .description('Scan a directory for agent skills and audit them')
  .argument('[path]', 'Path to scan', '.')
  .option('-v, --verbose', 'Show detailed output')
  .option('-j, --json', 'Output as JSON')
  .option('--min-score <score>', 'Fail if any skill scores below this', '0')
  .option('--no-summary', 'Skip summary output')
  .action(async (scanPath: string, options) => {
    const absolutePath = path.resolve(scanPath);
    
    if (!fs.existsSync(absolutePath)) {
      console.error(chalk.red(`Path not found: ${absolutePath}`));
      process.exit(1);
    }
    
    console.log(chalk.dim(`Scanning ${absolutePath}...`));
    
    const results = await auditPath(absolutePath);
    
    if (results.length === 0) {
      console.log(chalk.yellow('No skills found in this directory.'));
      process.exit(0);
    }
    
    const summary = summarize(results);
    
    if (options.json) {
      printJson(results, summary);
    } else {
      for (const result of results) {
        printResult(result, options.verbose);
      }
      
      if (options.summary !== false) {
        printSummary(summary);
      }
    }
    
    // Check minimum score
    const minScore = parseInt(options.minScore, 10);
    const failedSkills = results.filter(r => r.score.overall < minScore);
    if (failedSkills.length > 0) {
      console.log();
      console.error(chalk.red(`${failedSkills.length} skill(s) scored below ${minScore}`));
      process.exit(1);
    }
  });

program
  .command('check')
  .description('Audit a single skill file or directory')
  .argument('<path>', 'Path to skill file or directory')
  .option('-v, --verbose', 'Show detailed output')
  .option('-j, --json', 'Output as JSON')
  .action(async (checkPath: string, options) => {
    const absolutePath = path.resolve(checkPath);
    
    if (!fs.existsSync(absolutePath)) {
      console.error(chalk.red(`Path not found: ${absolutePath}`));
      process.exit(1);
    }
    
    let content: string;
    let skillPath = absolutePath;
    
    if (fs.statSync(absolutePath).isDirectory()) {
      // Look for main skill file
      const candidates = ['SKILL.md', 'skill.yaml', 'skill.yml', 'manifest.json', 'README.md'];
      for (const candidate of candidates) {
        const candidatePath = path.join(absolutePath, candidate);
        if (fs.existsSync(candidatePath)) {
          skillPath = candidatePath;
          break;
        }
      }
    }
    
    content = fs.readFileSync(skillPath, 'utf-8');
    const format = detectFormat(skillPath, content);
    const name = getSkillName(skillPath, content);
    
    const result = await auditSkill({ path: skillPath, name, content, format });
    
    if (options.json) {
      console.log(JSON.stringify(result, null, 2));
    } else {
      printResult(result, options.verbose);
    }
    
    // Exit with error if critical/high issues found
    const criticalOrHigh = result.security.filter(
      f => f.severity === 'critical' || f.severity === 'high'
    );
    if (criticalOrHigh.length > 0) {
      process.exit(1);
    }
  });

program
  .command('list')
  .description('List discovered skills without auditing')
  .argument('[path]', 'Path to scan', '.')
  .option('-j, --json', 'Output as JSON')
  .action(async (scanPath: string, options) => {
    const absolutePath = path.resolve(scanPath);
    
    if (!fs.existsSync(absolutePath)) {
      console.error(chalk.red(`Path not found: ${absolutePath}`));
      process.exit(1);
    }
    
    const skills = await discoverSkills(absolutePath);
    
    if (options.json) {
      console.log(JSON.stringify(skills.map(s => ({
        name: s.name,
        format: s.format,
        path: s.path
      })), null, 2));
    } else {
      if (skills.length === 0) {
        console.log(chalk.yellow('No skills found.'));
      } else {
        console.log(chalk.bold(`Found ${skills.length} skill(s):\n`));
        for (const skill of skills) {
          console.log(`  ${chalk.bold(skill.name)} ${chalk.dim(`(${skill.format})`)}`);
          console.log(chalk.dim(`    ${skill.path}`));
        }
      }
    }
  });

program.parse();
