# 5chat WordPress Plugin - Testing Guide

This guide explains how to test your 5chat WordPress plugin using the provided Docker environment.

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose installed
- Port 8080 and 8081 available on your system

### 1. Start the WordPress Environment

```bash
./setup-wordpress-test.sh start
```

### 2. Complete WordPress Installation

1. Open your browser and go to [http://localhost:8080](http://localhost:8080)
2. Follow the WordPress installation wizard:
   - **Site Title**: "5chat Plugin Testing"
   - **Username**: admin (or your choice)
   - **Password**: Create a strong password
   - **Email**: Your email address
3. Click "Install WordPress"
4. Log in to the WordPress admin

### 3. Test the 5chat Plugin

1. **Navigate to Plugins**:
   - Go to **Plugins → Installed Plugins**
   - You should see "5chat - Blazing fast live chat" listed

2. **Activate the Plugin**:
   - Click "Activate" under the 5chat plugin

3. **Test Admin Notice**:
   - After activation, you should see a warning notice at the top
   - The notice should say: "5chat Live Chat is active, but the Website Token is missing"
   - The notice should have a link to the settings page

4. **Access Settings Page**:
   - Click on **Settings → 5chat** in the WordPress admin menu
   - Or click the link in the admin notice
   - You should see the 5chat settings page with:
     - Title: "5chat Settings"
     - Description text
     - Website Token input field
     - "Save Settings" button
     - Help section with link to 5chat dashboard

## 🧪 Comprehensive Testing Checklist

### Plugin Installation & Activation
- [ ] Plugin appears in plugins list
- [ ] Plugin activates without errors
- [ ] No PHP errors in debug log
- [ ] Plugin shows in admin menu

### Settings Page Testing
- [ ] Settings page accessible via Settings → 5chat
- [ ] Page loads without errors
- [ ] Form displays correctly
- [ ] Input field is present and functional
- [ ] Help text is displayed

### Token Validation Testing

#### Test Empty Token
1. Leave token field empty
2. Click "Save Settings"
3. **Expected**: Settings save, but admin notice still appears

#### Test Invalid Token Format
1. Enter invalid characters: `test@#$%token!`
2. Click "Save Settings"  
3. **Expected**: Error message appears, token not saved

#### Test Valid Token
1. Enter a valid format token: `abc123def456`
2. Click "Save Settings"
3. **Expected**: Success message, settings saved, admin notice disappears

### Frontend Widget Testing

#### Test Without Token
1. Visit the frontend: [http://localhost:8080](http://localhost:8080)
2. View page source (Ctrl+U)
3. **Expected**: No 5chat script in the `<head>` section

#### Test With Valid Token
1. Set a valid token in settings: `test-token-123`
2. Save settings
3. Visit the frontend: [http://localhost:8080](http://localhost:8080)
4. View page source (Ctrl+U)
5. **Expected**: Should find this script in `<head>`:
   ```html
   <script src="https://5chat.io/widget/test-token-123" async></script>
   ```

### Admin Notice Testing
- [ ] Notice appears when plugin is active but token is empty
- [ ] Notice contains proper text and link
- [ ] Clicking notice link goes to settings page
- [ ] Notice disappears when valid token is set
- [ ] Notice only shows to users with admin capabilities

### Security Testing
- [ ] Settings page requires admin login
- [ ] Non-admin users cannot access settings
- [ ] Form includes proper nonce verification
- [ ] Input is properly sanitized
- [ ] Invalid tokens are rejected

### Plugin Management Testing

#### Deactivation
1. Go to **Plugins → Installed Plugins**
2. Click "Deactivate" under 5chat plugin
3. **Expected**: Plugin deactivates cleanly, no errors

#### Reactivation
1. Click "Activate" again
2. **Expected**: Plugin reactivates, settings preserved

#### Uninstall Testing
1. Deactivate the plugin
2. Click "Delete" under the plugin
3. **Expected**: Plugin files deleted, database options cleaned up

## 🔍 Debugging

### View WordPress Debug Logs
```bash
./setup-wordpress-test.sh logs
```

### Check Container Status
```bash
./setup-wordpress-test.sh status
```

### Access Database
1. Go to [http://localhost:8081](http://localhost:8081) (phpMyAdmin)
2. Login with:
   - **Username**: root
   - **Password**: rootpassword
3. Select `wordpress` database
4. Check `wp_options` table for `fivechat_website_token`

### Reset Environment
If you need to start fresh:
```bash
./setup-wordpress-test.sh reset
```

## 📱 Testing Different Scenarios

### Test with Different WordPress Themes
1. Go to **Appearance → Themes**
2. Activate different themes
3. Verify widget script still appears in each theme

### Test with Other Plugins
1. Install common plugins (contact forms, SEO plugins)
2. Verify no conflicts occur
3. Check that 5chat script still loads properly

### Test Multisite (Advanced)
For multisite testing, you'll need to modify the Docker setup to enable WordPress multisite functionality.

## 🛑 Common Issues & Solutions

### Port Already in Use
If port 8080 or 8081 is already in use:
1. Edit `docker-compose.yml`
2. Change port mappings (e.g., `8082:80` instead of `8080:80`)
3. Restart the environment

### Plugin Not Showing
If the plugin doesn't appear:
1. Check that the `fivechat-live-chat` folder is properly mounted
2. Verify folder permissions
3. Restart WordPress container:
   ```bash
   ./setup-wordpress-test.sh restart
   ```

### Database Connection Issues
If WordPress can't connect to database:
1. Wait a bit longer for MySQL to start up
2. Check container logs: `./setup-wordpress-test.sh logs`
3. Reset the environment: `./setup-wordpress-test.sh reset`

## ✅ Test Completion

Once you've completed all tests successfully:

1. **Document any issues found**
2. **Update plugin code if needed**
3. **Retest after fixes**
4. **Package the final version**
5. **Proceed with distribution**

Your plugin is ready for production when all tests pass! 🎉

## 🧹 Cleanup

When you're done testing:
```bash
./setup-wordpress-test.sh stop
```

To completely remove all data and containers:
```bash
./setup-wordpress-test.sh reset
``` 