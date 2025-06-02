# 5chat WordPress Plugin - Development Guide

## Architecture Overview

The plugin has been restructured into a modular, object-oriented architecture for better maintainability and code organization.

## File Structure

```
fivechat-live-chat/
├── 5chat.php                           # Main plugin file (entry point)
├── uninstall.php                       # Plugin uninstaller
├── readme.txt                          # WordPress plugin readme
├── assets/
│   ├── css/
│   │   └── admin.css                   # Admin styles
│   └── icon-128x128.png               # Plugin icon
├── includes/
│   ├── class-fivechat-token-validator.php  # Token validation & API
│   ├── class-fivechat-settings.php         # Settings page & forms
│   ├── class-fivechat-admin.php            # Admin functionality
│   └── class-fivechat-frontend.php         # Frontend widget
└── languages/                          # Translation files directory
```

## Class Structure

### Main Plugin Class (`FiveChat_Plugin`)
- **File**: `5chat.php`
- **Purpose**: Main entry point, dependency loading, component initialization
- **Pattern**: Singleton pattern
- **Dependencies**: All other classes

### Token Validator (`FiveChat_Token_Validator`)
- **File**: `includes/class-fivechat-token-validator.php`
- **Purpose**: Handle token validation with 5chat API
- **Features**: 
  - API communication
  - Caching mechanisms
  - Error handling
- **Dependencies**: None

### Settings Management (`FiveChat_Settings`)
- **File**: `includes/class-fivechat-settings.php`
- **Purpose**: Admin settings page, form handling, token sanitization
- **Features**:
  - Settings registration
  - Form rendering
  - Token sanitization
  - Admin styles enqueuing
- **Dependencies**: `FiveChat_Token_Validator`

### Admin Functionality (`FiveChat_Admin`)
- **File**: `includes/class-fivechat-admin.php`
- **Purpose**: Admin notices, plugin links, admin-only features
- **Features**:
  - Admin notices for missing/invalid tokens
  - Settings page links in plugin list
  - Token status utilities
- **Dependencies**: `FiveChat_Token_Validator`

### Frontend Widget (`FiveChat_Frontend`)
- **File**: `includes/class-fivechat-frontend.php`
- **Purpose**: Frontend widget script injection
- **Features**:
  - Script enqueuing
  - Async script loading
  - Display conditions
  - Widget configuration utilities
- **Dependencies**: None

## Component Communication

```
FiveChat_Plugin (Main)
├── Creates & manages all components
├── Passes dependencies between components
└── Handles activation/deactivation

FiveChat_Token_Validator
├── Used by Settings for validation
├── Used by Admin for status checks
└── Provides caching for all validation

FiveChat_Settings
├── Uses Token_Validator for API validation
└── Manages all settings functionality

FiveChat_Admin
├── Uses Token_Validator for status checks
└── Displays admin notices and links

FiveChat_Frontend
├── Independent frontend functionality
└── Handles widget display logic
```

## Development Guidelines

### Adding New Features

1. **New Settings**: Add to `FiveChat_Settings` class
2. **New Admin Features**: Add to `FiveChat_Admin` class
3. **New Frontend Features**: Add to `FiveChat_Frontend` class
4. **New API Endpoints**: Add to `FiveChat_Token_Validator` class

### Code Standards

- Follow WordPress Coding Standards
- Use proper PHPDoc comments
- Maintain consistent error handling
- Use WordPress functions over native PHP when available
- Escape all output appropriately
- Validate and sanitize all input

### Testing Components

Each class provides utility methods for debugging:
- `FiveChat_Admin::get_token_status()`
- `FiveChat_Frontend::get_widget_config()`
- `FiveChat_Token_Validator::get_cache_status()`

### Performance Considerations

- Token validation is cached for 1 hour
- Admin styles only load on settings page
- Frontend scripts only load when token is configured
- Database queries use WordPress functions

## WordPress Compliance

- ✅ Text domain: `fivechat-live-chat`
- ✅ Proper input validation and output escaping
- ✅ No direct database queries
- ✅ Proper nonce verification
- ✅ Capability checks
- ✅ WordPress coding standards
- ✅ Plugin Check compliance (100% pass rate)

## Future Improvements

- Consider adding WP-CLI commands for debugging
- Add unit tests for each class
- Consider adding more granular hooks/filters
- Add logging for debug purposes
- Consider adding settings import/export 