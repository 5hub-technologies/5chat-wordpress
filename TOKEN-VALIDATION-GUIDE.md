# 5chat Token Validation System

The 5chat WordPress plugin now includes **real-time token validation** to ensure users always have working chat widgets.

## 🎯 **How It Works**

### **Real-Time Validation**
- **As you type**: Token is validated automatically 800ms after you stop typing
- **API Check**: Plugin sends request to `https://5chat.io/widget/{token}`
- **Instant Feedback**: Green check for valid tokens, red error for invalid ones
- **Form Protection**: Prevents saving invalid tokens

### **Visual Indicators**

| Status | Icon | Message |
|--------|------|---------|
| **Loading** | 🔄 Spinner | "Validating token..." |
| **Valid** | ✅ Green Check | "Token is valid! Chat widget will load successfully." |
| **Invalid** | ❌ Red X | Specific error message based on issue |

## 🔍 **Validation Process**

### **1. Format Check**
- Must contain only letters, numbers, hyphens, and underscores
- Cannot be empty (unless clearing the field)
- Basic pattern: `/^[a-zA-Z0-9_-]+$/`

### **2. API Verification**
- HTTP GET request to `https://5chat.io/widget/{token}`
- **HTTP 200**: Token is valid ✅
- **HTTP 404**: Token not found ❌
- **Other codes**: Service error ⚠️

### **3. Caching System**
- Valid/invalid status cached for 1 hour
- Prevents excessive API calls
- Cache cleared when token changes

## 💡 **User Experience**

### **Settings Page Experience**
1. **Enter Token**: Start typing your website token
2. **See Validation**: Watch real-time feedback as you type
3. **Get Confirmation**: Green check confirms token works
4. **Save Settings**: Only valid tokens can be saved
5. **Instant Activation**: Chat widget appears immediately

### **Error Scenarios**
- **Empty Token**: No validation (allows clearing)
- **Invalid Format**: "Invalid token format" message
- **Token Not Found**: "Invalid token. Please check your Website Token"
- **Network Error**: "Unable to connect to 5chat" message
- **Service Error**: HTTP status code with suggestion to contact support

## 🛡️ **Security Features**

### **AJAX Security**
- WordPress nonce verification for all AJAX requests
- Capability checks (`manage_options` required)
- Input sanitization and validation
- Secure endpoint authentication

### **API Safety**
- 10-second timeout on HTTP requests
- Proper User-Agent header identification
- Error handling for network issues
- No sensitive data transmitted

## 🚀 **Performance Optimizations**

### **Debounced Validation**
- 800ms delay after user stops typing
- Prevents API spam while typing quickly
- Only validates when input actually changes

### **Smart Caching**
- Token validation results cached for 1 hour
- Reduces API calls for repeated checks
- Cache automatically cleared on token changes

### **Efficient Processing**
- Non-blocking AJAX requests
- Minimal server resources used
- Fast response times for user feedback

## 🔧 **Developer Information**

### **AJAX Endpoint**
```php
// Action: fivechat_validate_token
// Method: POST
// Nonce: fivechat_validate_token
// Required: token parameter
```

### **Response Format**
```javascript
// Success Response
{
    "success": true,
    "data": {
        "message": "Token is valid! Chat widget will load successfully.",
        "token": "abc123def456"
    }
}

// Error Response
{
    "success": false,
    "data": {
        "message": "Invalid token. Please check your Website Token in your 5chat dashboard."
    }
}
```

### **Cache Keys**
```php
// Transient key format
$cache_key = 'fivechat_token_valid_' . md5($token);

// Values: 'valid' or 'invalid'
// Expiry: 1 hour (HOUR_IN_SECONDS)
```

## 📋 **Testing Scenarios**

### **Valid Token Test**
1. Enter a working 5chat token
2. Should see green check with success message
3. Form should save successfully
4. Widget should appear on frontend

### **Invalid Token Test**
1. Enter a non-existent token (e.g., "invalid-test-token")
2. Should see red error with "Invalid token" message
3. Form should not save
4. Previous token should be preserved

### **Format Error Test**
1. Enter invalid characters (e.g., "token@#$%")
2. Should see immediate format error
3. No API call should be made
4. Form should not save

### **Network Error Test**
1. Disconnect internet or block 5chat.io
2. Should see connection error message
3. Form should not save
4. User gets helpful error message

## 🎉 **Benefits**

### **For Users**
- **Immediate Feedback**: Know instantly if token works
- **Error Prevention**: Can't save broken configurations
- **Professional Experience**: Smooth, modern interface
- **Time Saving**: No need to test on frontend first

### **For Administrators**
- **Reduced Support**: Fewer "widget not working" tickets
- **Quality Assurance**: Ensures only working tokens are saved
- **User Confidence**: Users trust the configuration process
- **Professional Image**: Plugin feels polished and reliable

## 🔄 **Admin Notices**

The plugin also shows smart admin notices:

### **Missing Token**
- Shows when plugin is active but no token configured
- Appears on dashboard and plugins page
- Direct link to settings page

### **Invalid Token**  
- Shows when saved token becomes invalid
- Cached check (1 hour) to avoid constant API calls
- Encourages user to update token

### **Notice Behavior**
- Disappears automatically when issues are resolved
- Only shows to users with admin capabilities
- Hidden on settings page itself (to avoid redundancy)

Your users will love the professional validation experience! ✨ 