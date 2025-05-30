# OpenFGA PHP SDK Tools

This directory contains comprehensive tooling for documentation quality, development, and code maintenance.

## 📊 Documentation Quality Suite

### Complete Documentation Workflow
```bash
# Full documentation quality check
composer docs:check

# Individual checks
composer docs:coverage    # API documentation coverage
composer docs:links       # Link validation  
composer docs:lint        # Style and grammar linting
composer docs:metrics     # Generate quality dashboard
```

### Documentation Coverage Analysis

`docs-coverage.php` - Analyzes PHP source code for documentation completeness.

**Features:**
- Scans all public methods and classes
- Identifies missing @param, @return, @throws tags
- Checks for example code in complex methods
- Validates @see references
- Generates coverage reports

**Usage:**
```bash
composer docs:coverage
php tools/docs-coverage.php --format=json --min-coverage=90
```

### Link Validation

`link-checker.php` - Validates all links in documentation files.

**Features:**
- Validates HTTP/HTTPS external links
- Checks internal file references and anchors
- Validates @see references in PHPDoc
- Parallel checking for performance

**Usage:**
```bash
composer docs:links
php tools/link-checker.php --external --timeout=30
```

### Documentation Metrics Dashboard

`docs-metrics.php` - Generates comprehensive documentation quality metrics.

**Features:**
- Documentation coverage metrics
- Content freshness analysis
- Link validation status  
- Style compliance scores
- Quality trends and insights

**Usage:**
```bash
composer docs:metrics
php tools/docs-metrics.php --format=html --output=dashboard.html
```

### Vale Documentation Linter

`docs-lint.php` - Lints documentation files for style consistency using Vale with Google and Microsoft style guides.

**Features:**
- Grammar and style checking
- Technical writing best practices
- Configurable rules for technical documentation
- Integration with popular style guides

**Usage:**
```bash
composer docs:lint
php tools/docs-lint.php docs/ src/
```

**Configuration:**
- Vale configuration: `.vale.ini`
- Style guides: `styles/` directory (Google, Microsoft)
- Focuses on important style issues while allowing technical terminology
- Minimum alert level: warning

## API Documentation Generation

### Documentation Generator

`docs/generate.php` - Generates API documentation from PHP docblocks.

**Usage:**
```bash
composer docs
php tools/docs/generate.php
```

## 🔒 Security Tools

### GitHub Workflow Security Audit

`workflow-security-audit.php` - Comprehensive security audit tool for GitHub Actions workflows.

**Features:**
- **Commit Hash Pinning**: Ensures all `uses` statements are pinned to specific commit hashes
- **Version Comments**: Validates that each pinned action includes a version comment
- **Update Detection**: Checks for newer versions and can automatically update workflows
- **Security Best Practices**: Prevents supply chain attacks through proper pinning

**Usage:**
```bash
# Audit workflows (read-only)
composer security:workflows

# Audit and automatically fix workflows 
composer security:workflows:fix

# Using GitHub CLI (recommended)
gh auth login
composer security:workflows:fix

# Manual usage with GitHub token
php tools/workflow-security-audit.php --fix --token=ghp_xxxxxxxxxxxx
```

**Security Rationale:**
Using commit hashes instead of version tags prevents:
- Supply chain attacks where malicious code is introduced
- Tag moving attacks where attackers change what a tag points to
- Dependency confusion with similarly named actions

**Options:**
- `--fix` - Automatically update workflows to latest pinned versions
- `--token=<token>` - GitHub personal access token for API calls
- `--help` - Show help message

**Authentication (in order of preference):**
1. `--token=<token>` - Command line token
2. `GITHUB_TOKEN` - Environment variable  
3. `gh` CLI - GitHub CLI tool (if installed and authenticated)
4. Unauthenticated - Limited to 60 requests/hour

## Wiki Synchronization

### Wiki Sync Script

`sync-wiki.sh` - Synchronizes generated documentation with the GitHub wiki.

**Usage:**
```bash
composer wiki
./tools/sync-wiki.sh
```

## Development Workflow

The tools integrate into the standard development workflow:

1. **Development**: Write code with proper documentation
2. **Security Check**: Run `composer security:workflows` to audit GitHub Actions
3. **Quality Check**: Run `composer lint` (includes documentation linting)
4. **Documentation**: Run `composer docs` to generate API docs
5. **Publication**: Run `composer wiki` to sync with GitHub wiki

## Configuration Files

- `.vale.ini` - Vale documentation linter configuration
- `tools/docs/composer.json` - Documentation generator dependencies
- `tools/docs/documentation.twig` - Documentation template

## Adding New Tools

When adding new tools:

1. Create the tool in the `tools/` directory
2. Make it executable with `chmod +x`
3. Add appropriate Composer scripts
4. Document usage in this README
5. Include in relevant workflow scripts (lint, test, etc.)