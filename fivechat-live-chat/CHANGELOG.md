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
- Admin notice system to alert when token is missing
- Settings link on plugins page for easy access
- Proper input validation and sanitization
- Support for WordPress multisite installations
- Uninstall script for clean removal
- Internationalization support (i18n ready)

### Security
- Proper nonce verification for settings forms
- Input sanitization and validation
- Capability checks for admin functions
- Prevention of direct file access

### Performance
- Asynchronous loading of chat widget
- Minimal plugin overhead
- Efficient option storage and retrieval 