# skill-audit

Audit agent skill definitions for security, completeness, and compatibility across different formats.

As AI agent skills become portable across platforms (Codex, Claude Code, OpenClaw, OpenCode), ensuring they're secure and well-documented is critical. skill-audit analyzes skill definitions to catch dangerous patterns before they cause problems.

## Installation

```bash
npm install -g skill-audit
```

Or run directly with npx:

```bash
npx skill-audit scan ./my-skills
```

## Quick Start

Scan a directory for skills and audit them:

```bash
skill-audit scan ./skills
```

Audit a single skill:

```bash
skill-audit check ./my-skill/SKILL.md
```

List discovered skills without auditing:

```bash
skill-audit list ./
```

## What It Detects

### Security Issues

**Critical**
- Remote code execution (eval, curl | sh)
- Access to sensitive files (/etc/passwd, .ssh, .env)
- Fork bombs and dangerous shell patterns
- Direct writes to block devices

**High**
- Process spawning (child_process, spawn, exec)
- sudo usage requiring elevated privileges
- Hardcoded API key references
- Remote shell script downloads

**Medium**
- File write operations without path validation
- Network downloads (wget, curl)
- Permissive chmod operations
- Unpinned credentials in definitions

**Low**
- Potential sensitive data logging
- Missing error handling in scripts

### Quality Checks

- Missing description section
- No usage examples
- Missing usage instructions
- Excessive line length
- Broken anchor links
- Unpinned dependencies
- Missing shell error handling (set -e)
- No input validation

## Supported Formats

skill-audit automatically detects and handles:

- **OpenClaw** - SKILL.md files
- **Codex** - skill.yaml and manifest.json
- **Claude Code** - .claude/commands/*.md
- **OpenCode** - opencode.json configuration

## Commands

### scan

Recursively scan a directory for skills and audit all found:

```bash
skill-audit scan [path] [options]

Options:
  -v, --verbose         Show detailed output including quality suggestions
  -j, --json            Output results as JSON
  --min-score <score>   Exit with error if any skill scores below threshold
  --no-summary          Skip the summary section
```

### check

Audit a single skill file or directory:

```bash
skill-audit check <path> [options]

Options:
  -v, --verbose    Show detailed output
  -j, --json       Output as JSON
```

Exits with code 1 if critical or high severity issues are found.

### list

Discover skills in a directory without running audits:

```bash
skill-audit list [path] [options]

Options:
  -j, --json    Output as JSON
```

## Scoring

Each skill receives three scores:

- **Security Score** (0-100): Starts at 100, deducted for each finding based on severity
- **Quality Score** (0-100): Based on documentation completeness and best practices
- **Overall Score**: Weighted average (60% security, 40% quality)

Letter grades:
- A: 90-100
- B: 80-89
- C: 70-79
- D: 60-69
- F: Below 60

## CI Integration

Use skill-audit in CI to enforce minimum standards:

```yaml
# GitHub Actions example
- name: Audit skills
  run: npx skill-audit scan ./skills --min-score 70
```

```bash
# Pre-commit hook
skill-audit check ./my-skill --min-score 80 || exit 1
```

## Programmatic Usage

```typescript
import { auditPath, summarize } from 'skill-audit';

const results = await auditPath('./skills');
const summary = summarize(results);

console.log(`Audited ${summary.totalSkills} skills`);
console.log(`Average score: ${summary.averageScore}`);
console.log(`Critical issues: ${summary.criticalCount}`);
```

## Examples

Audit the OpenClaw skills directory:

```bash
skill-audit scan ~/.npm-global/lib/node_modules/openclaw/skills -v
```

Check a specific skill with JSON output:

```bash
skill-audit check ./skills/my-tool/SKILL.md --json
```

Find all skills in a monorepo:

```bash
skill-audit list ./packages --json | jq '.[] | .name'
```

Fail CI if any critical issues:

```bash
skill-audit scan . --min-score 60
```

## Why This Exists

Agent skills are becoming the standard way to package reusable AI capabilities. With repos like openai/skills providing skill catalogs and tools like compound-engineering-plugin enabling cross-platform skill sharing, security and quality become paramount.

skill-audit provides:
- Early detection of dangerous patterns before production
- Consistent quality standards across skill libraries
- CI-friendly validation for skill repositories
- Cross-format compatibility analysis

## License

MIT
