# Changelog

All notable changes to the 5chat Live Chat WordPress plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-XX

### Added
- Initial release of the 5chat Live Chat plugin
- Simple token-based configuration system
- WordPress Settings page under Settings → 5chat
- Automatic injection of 5chat widget script in website head
- **Real-time token validation** against 5chat API
- **Live validation feedback** with green check/error indicators
- Smart admin notification system for missing or invalid tokens
- Settings link on plugins page for easy access
- Proper input validation and sanitization
- Support for WordPress multisite installations
- Uninstall script for clean removal
- Internationalization support (i18n ready)
- Professional plugin images and branding

### Security
- Proper nonce verification for settings forms and AJAX requests
- Input sanitization and validation with API verification
- Capability checks for admin functions
- Prevention of direct file access
- Secure AJAX endpoints with permission checks

### Performance
- Asynchronous loading of chat widget
- Minimal plugin overhead with efficient caching
- Debounced real-time validation (800ms delay)
- Cached token validation results (1 hour cache)
- Efficient option storage and retrieval

### User Experience
- **Real-time feedback** as users type their token
- **Visual validation indicators** (loading, success, error states)
- **Prevents invalid token submission** with client-side validation
- Smart admin notices that disappear when issues are resolved
- Professional UI matching WordPress design standards 