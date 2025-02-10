# Changelog

## [Unreleased]

### Breaking Changes
- Moved documentation from `doc/` to `docs/` directory
- Updated all documentation references to use new paths
- Restructured routing paths for better organization

### Added
- New CertificateForm implementation
- New RequirementForm with improved UI
- RequirementSetEditForm for better UX
- Progress administration interface
- New CSS files for admin and form styling:
  - progress-admin.css
  - requirement-set-form.css
- Vertical tabs templates for requirements
- Progress tracking JavaScript
- Certificate progress menu task plugin

### Changed
- Split documentation into technical.md and concepts.md
- Simplified certificate dashboard interface
- Enhanced progress tracking UI
- Updated entity form handlers
- Reorganized routing paths for clarity
- Improved monitor interface styling
- Updated block configurations
- Enhanced permission definitions

### Fixed
- Cancel button rendering in requirement forms
- Progress manager service references
- Template list builder class loading
- Structure monitoring reliability
- Form action handling

### Removed
- Legacy documentation structure
- Redundant enrollment routes
- DDEV configuration (now managed separately)