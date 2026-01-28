# Changelog

All notable changes to the Product Showcase Cards plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2.0.0] - 2025-01-09

### 🎉 Major Release - Security, Accessibility & UX Overhaul

Version 2.0.0 represents a complete security audit and major enhancement of the plugin with significant improvements across all areas.

### 🔒 Security

#### Fixed
- **Critical:** Added post type validation in `save_meta_boxes()` to prevent metadata injection attacks (product-showcase-cards.php:407)
- **Critical:** Fixed XSS vulnerability in admin image preview by using safe jQuery methods (js/admin.js:70-74)
- **High:** Enhanced font family sanitization with CSS injection prevention (product-showcase-cards.php:393-399)
- **Medium:** Improved input validation throughout save operations

#### Added
- Comprehensive sanitization for all user inputs
- Nonce verification for all form submissions
- Proper escaping in all output contexts
- Field validation to prevent empty/invalid data

### ♿ Accessibility (WCAG 2.1 AA Compliance)

#### Added
- **ARIA roles and labels** throughout interface (product-showcase-cards.php:661-669)
  - `role="tablist"` for category tabs container
  - `role="tab"` for individual tabs
  - `aria-selected` states
  - `aria-label` for screen reader context
- **Full keyboard navigation** for category tabs (js/frontend.js:50-77)
  - Arrow Left/Right for tab navigation
  - Home/End keys for first/last tab
  - Proper tabindex management
- **Screen reader optimizations** with semantic HTML
- **Focus indicators** for all interactive elements (css/frontend.css:98-101)
- **Reduced motion support** respecting user preferences (css/frontend.css:344-361)

### 🎨 User Experience

#### Added
- **Visual copy button** for shortcodes with success feedback (product-showcase-cards.php:393-396, js/admin.js:146-199)
  - Modern clipboard API with automatic fallback
  - Green success state with checkmark icon
  - 2-second confirmation feedback
- **Required field indicators** with red asterisks and validation messages (product-showcase-cards.php:322-339)
  - Clear visual marking of required fields
  - Descriptive help text
  - Red border accent for required fields
- **Item numbering** for easy reference (product-showcase-cards.php:316-324, css/admin.css:76-83)
  - "Item 1", "Item 2", etc. display
  - Auto-updates when items are reordered
  - Improves admin organization
- **Improved admin layout** (css/admin.css:67-91)
  - Card-style item headers
  - Better visual hierarchy
  - Cleaner sectioning
  - Professional appearance

#### Changed
- Moved drag handle to item header for better usability
- Relocated remove button to item header
- Improved visual feedback throughout admin
- Better spacing and organization

### ⚡ Performance

#### Added
- **Lazy loading** for all frontend images (product-showcase-cards.php:701)
  - Native browser lazy loading with `loading="lazy"`
  - Improves initial page load time
  - Reduces bandwidth usage
- **Responsive images with srcset** (product-showcase-cards.php:680-681, 694-699)
  - Automatic srcset generation
  - Proper sizes attribute
  - Optimal image delivery for all devices
- **Optimized CSS delivery** with better organization
- **Modern JavaScript APIs** with progressive enhancement

#### Changed
- Refactored default values into class constants for DRY code (product-showcase-cards.php:30-41)
- Improved code organization and documentation
- Better error handling throughout

### 🔧 Code Quality

#### Added
- Comprehensive PHPDoc blocks
- Class constants for all default values:
  - `DEFAULT_ITEMS_PER_ROW = 3`
  - `DEFAULT_BG_COLOR = '#d4e5a3'`
  - `DEFAULT_TEXT_COLOR = '#2E2E2E'`
  - `DEFAULT_HOVER_EFFECT = 'lift'`
  - Plus 8 more constants
- `sanitize_font_family()` method for safe font handling
- `uninstall.php` for proper cleanup on plugin deletion

#### Changed
- Updated to modern clipboard API (from deprecated `document.execCommand`)
- Improved CSS specificity (reduced !important usage where possible)
- Better variable naming and code organization
- WordPress coding standards compliance

### 📦 Infrastructure

#### Added
- Comprehensive `README.md` with full documentation
- `CHANGELOG.md` (this file)
- `uninstall.php` for clean plugin removal
- WordPress plugin headers:
  - Requires at least: 5.8
  - Tested up to: 6.7
  - Requires PHP: 7.4

#### Changed
- Updated version to 2.0.0 throughout
- Improved file structure documentation
- Better inline code comments

### 📝 Documentation

#### Added
- Complete usage guide in README
- Troubleshooting section
- Keyboard navigation documentation
- Accessibility features documentation
- Security features documentation
- Performance features documentation
- Code examples and snippets

#### Changed
- Reorganized README for better clarity
- Added emoji section markers for easy scanning
- Expanded troubleshooting guide
- Better formatting and structure

### 🐛 Bug Fixes

- Fixed item numbering not updating after drag-and-drop reordering
- Fixed clipboard copy on non-HTTPS sites (added fallback)
- Fixed missing validation allowing empty items to be saved
- Corrected CSS selector specificity issues

---

## [1.3.1] - Previous Release

### Changed
- Minor bug fixes and improvements
- CSS adjustments

---

## [1.1.0] - Category Filtering Release

### Added
- Category filtering system with modern tab navigation
- Multi-category support for items
- "Display Categories" setting for customizing visible tabs
- Automatic hiding of empty categories
- Mobile-friendly horizontal scrolling tabs
- Frontend JavaScript for smooth filtering
- Modern underline tab design
- Tab color customization options:
  - Inactive, hover, and active text colors
  - Hover and active underline colors

### Changed
- Enhanced frontend user experience
- Improved mobile responsiveness

---

## [1.0.0] - Initial Release

### Added
- Custom post type for showcases
- Repeater fields for unlimited items
- Drag-and-drop item reordering
- Image upload integration with WordPress media library
- Flexible layout options (1-8 items per row)
- Per-item color customization
- Background and text color settings
- Multiple hover effects (None, Lift, Zoom, Lift+Zoom)
- Custom fonts support (title and body)
- Font weight controls
- Responsive design (desktop, tablet, mobile)
- Shortcode implementation
- Admin interface with meta boxes
- Color picker integration
- Professional frontend styling
- Print-friendly styles

---

## Upgrade Notes

### Upgrading to 2.0.0

**This is a major release with breaking changes to internal structure. Please review before upgrading in production.**

#### Required Actions:
1. **Backup your database** before upgrading
2. **Clear all caches** after upgrade (site cache, browser cache, CDN)
3. **Test thoroughly** on staging environment first
4. **Review custom CSS** if you've overridden plugin styles

#### Compatibility:
- ✅ **Fully backward compatible** with existing showcases
- ✅ **All shortcodes continue to work** unchanged
- ✅ **No database migration required**
- ✅ **Existing settings preserved**

#### New Requirements:
- WordPress 5.8+ (previously no minimum specified)
- PHP 7.4+ (previously no minimum specified)

#### Benefits:
- Significantly improved security
- Better accessibility for all users
- Faster page loads with lazy loading
- Modern admin interface
- Better mobile experience

---

## Development

### Git Repository
This plugin is actively maintained. Version history:
- 2.0.0: Major security and UX release
- 1.3.1: Bug fixes
- 1.1.0: Category filtering
- 1.0.0: Initial release

### Contribution Guidelines
Contributions are welcome! Please:
1. Follow WordPress coding standards
2. Include PHPDoc blocks for all functions
3. Test across multiple browsers
4. Ensure accessibility compliance
5. Add entries to this CHANGELOG

---

## Support

For bug reports, feature requests, or support:
- **Email:** cliff@cordesprinting.com
- **Website:** https://cordesprinting.com

---

**Legend:**
- `Added` for new features
- `Changed` for changes in existing functionality
- `Deprecated` for soon-to-be removed features
- `Removed` for now removed features
- `Fixed` for any bug fixes
- `Security` for vulnerability fixes
