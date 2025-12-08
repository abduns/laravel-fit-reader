# Release Process Guide

This document outlines the process for releasing new versions of Laravel Fit Reader following semantic versioning.

## Semantic Versioning

This project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html):

- **MAJOR** version (X.0.0): Incompatible API changes
- **MINOR** version (0.X.0): New functionality in a backward-compatible manner
- **PATCH** version (0.0.X): Backward-compatible bug fixes

### Version Examples
- `1.0.0` → `2.0.0`: Breaking changes (e.g., removing methods, changing signatures)
- `1.0.0` → `1.1.0`: New features (e.g., new methods, new properties, new capabilities)
- `1.0.0` → `1.0.1`: Bug fixes (e.g., fixing parsing errors, correcting calculations)

## Release Checklist

### 1. Prepare the Release

- [ ] Ensure all changes are merged to the `main` branch
- [ ] Update `CHANGELOG.md` with the new version and changes
  - Follow the [Keep a Changelog](https://keepachangelog.com/) format
  - Add the release date
  - Categorize changes: Added, Changed, Deprecated, Removed, Fixed, Security
  - Add version comparison links at the bottom
- [ ] Update `README.md` if there are new features or changes to usage
- [ ] Run the full test suite: `composer test`
- [ ] Verify all tests pass

### 2. Version the Release

```bash
# Ensure you're on the main branch
git checkout main
git pull origin main

# Create and push the version tag
git tag -a v1.2.0 -m "Release v1.2.0"
git push origin v1.2.0
```

**Important**: Tag format must be `vX.Y.Z` (with 'v' prefix, no dots between 'v' and version number)

### 3. Create GitHub Release

1. Go to https://github.com/abduns/laravel-fit-reader/releases/new
2. Select the tag you just pushed (e.g., `v1.2.0`)
3. Set the release title (e.g., `v1.2.0 - Activity Type Detection & Data Export`)
4. Add release notes:
   - Summarize the key changes
   - Include usage examples for new features
   - Reference the CHANGELOG for full details
   - Add upgrade instructions if needed
5. Select "Set as the latest release"
6. Click "Publish release"

### 4. Update Packagist

Packagist should automatically update within minutes via the GitHub webhook. Verify:

1. Visit https://packagist.org/packages/abduns/laravel-fit-reader
2. Check that the new version appears in the version list
3. Verify the "Latest Version" badge shows the new version

**Troubleshooting**: If Packagist doesn't update automatically:
- Go to https://packagist.org/packages/abduns/laravel-fit-reader
- Click "Update" button (requires Packagist login)
- Ensure the GitHub webhook is properly configured in repository settings

### 5. Post-Release

- [ ] Verify the package can be installed: `composer require abduns/laravel-fit-reader:^1.2.0`
- [ ] Update any dependent projects or documentation
- [ ] Announce the release (optional):
  - Social media
  - Laravel communities
  - Project README badges

## Release Notes Template

Create a `RELEASE_vX.Y.Z.md` file for each release with this structure:

```markdown
# Release Notes for vX.Y.Z

## 📦 Laravel Fit Reader vX.Y.Z

Brief description of what this release adds/fixes.

### ✨ What's New

- Feature 1 with description
- Feature 2 with description

### 🐛 Bug Fixes (if applicable)

- Fix 1 with description
- Fix 2 with description

### 📖 Usage Examples

\`\`\`php
// Code examples demonstrating new features
\`\`\`

### 🔄 Upgrade Guide

Instructions for upgrading, including:
- Breaking changes (for MAJOR versions)
- New requirements
- Migration steps

### 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 12.x

### 🔗 Links

- [Full Changelog](link)
- [Documentation](link)
```

## Packagist Best Practices

### Composer.json Requirements

Ensure `composer.json` includes:

```json
{
  "name": "abduns/laravel-fit-reader",
  "description": "Clear, concise description",
  "keywords": ["relevant", "keywords"],
  "license": "MIT",
  "homepage": "https://github.com/abduns/laravel-fit-reader",
  "support": {
    "issues": "https://github.com/abduns/laravel-fit-reader/issues",
    "source": "https://github.com/abduns/laravel-fit-reader"
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

### Version Constraints

When specifying dependencies, use semantic version constraints:
- `^12.0`: Major version 12, any minor/patch (12.0, 12.1, 12.2, etc.)
- `^8.2`: PHP 8.2 or higher (8.2, 8.3, 9.0, etc.)

### Auto-Discovery

Laravel packages should include auto-discovery configuration:

```json
{
  "extra": {
    "laravel": {
      "providers": [
        "Dunn\\FitReader\\FitReaderServiceProvider"
      ],
      "aliases": {
        "FitReader": "Dunn\\FitReader\\Facades\\FitReader"
      }
    }
  }
}
```

## GitHub Webhook Setup (One-time)

To enable automatic Packagist updates:

1. Go to https://packagist.org/packages/abduns/laravel-fit-reader
2. Click "Show API Token"
3. Copy the webhook URL
4. Go to GitHub repository Settings → Webhooks
5. Add webhook with the Packagist URL
6. Content type: `application/json`
7. Select "Just the push event"
8. Click "Add webhook"

## Emergency Procedures

### Yanking a Release

If a critical bug is found immediately after release:

1. **Do NOT delete the tag/release** (breaks existing installations)
2. Instead, release a patch version quickly (e.g., `1.2.0` → `1.2.1`)
3. Update CHANGELOG with the fix
4. Follow the normal release process

### Fixing Tag Naming Mistakes

If a tag is pushed with the wrong format (e.g., `v.1.1.0` instead of `v1.1.0`):

1. The tag cannot be changed on GitHub/Packagist (immutable)
2. Create a new properly-formatted tag for future releases
3. Document the inconsistency in CHANGELOG or release notes
4. Continue with correct format for all future releases

## Questions?

For questions about the release process, create an issue at:
https://github.com/abduns/laravel-fit-reader/issues
