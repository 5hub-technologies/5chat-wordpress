#!/bin/bash

# 5chat WordPress Plugin - Screenshot Helper Script

echo "📸 5chat Plugin Screenshot Helper"
echo "================================="
echo ""

# Check if WordPress is running
if ! docker ps | grep -q wordpress_site; then
    echo "❌ WordPress environment is not running."
    echo "Please start it first:"
    echo "   ./setup-wordpress-test.sh start"
    echo ""
    exit 1
fi

echo "✅ WordPress environment is running"
echo ""

echo "📋 Screenshot Instructions:"
echo ""

echo "1️⃣  SCREENSHOT 1 - Settings Page (1200x900px)"
echo "   🔗 URL: http://localhost:8080/wp-admin/options-general.php?page=fivechat-settings"
echo "   📝 Steps:"
echo "      - Log into WordPress admin"
echo "      - Go to Settings → 5chat"
echo "      - Take screenshot of settings page"
echo "      - Save as: fivechat-live-chat/assets/screenshot-1.png"
echo ""

echo "2️⃣  SCREENSHOT 2 - Admin Notice (1200x900px)"
echo "   🔗 URL: http://localhost:8080/wp-admin/"
echo "   📝 Steps:"
echo "      - Make sure plugin is active but token is empty"
echo "      - Go to WordPress dashboard"
echo "      - Take screenshot showing the admin notice"
echo "      - Save as: fivechat-live-chat/assets/screenshot-2.png"
echo ""

echo "3️⃣  SCREENSHOT 3 - Frontend Widget (1200x900px)"
echo "   🔗 URL: http://localhost:8080/"
echo "   📝 Steps:"
echo "      - Set a test token in plugin settings"
echo "      - Visit the frontend website"
echo "      - Use browser dev tools to simulate chat widget"
echo "      - Take screenshot showing website with widget"
echo "      - Save as: fivechat-live-chat/assets/screenshot-3.png"
echo ""

echo "🎨 For Icons and Banners:"
echo "   📄 Open: create-plugin-images.html in your browser"
echo "   📸 Screenshot each template at exact dimensions"
echo "   💾 Save in: fivechat-live-chat/assets/"
echo ""

echo "📁 Required Files:"
echo "   ├── icon-128x128.png        (128×128px, transparent PNG)"
echo "   ├── icon-256x256.png        (256×256px, transparent PNG)"
echo "   ├── banner-772x250.png      (772×250px, JPG/PNG)"
echo "   ├── banner-1544x500.png     (1544×500px, JPG/PNG)"
echo "   ├── screenshot-1.png        (1200×900px, PNG)"
echo "   ├── screenshot-2.png        (1200×900px, PNG)"
echo "   └── screenshot-3.png        (1200×900px, PNG)"
echo ""

echo "🔧 Screenshot Tips:"
echo "   - Use browser zoom to get exact dimensions"
echo "   - Take full-quality PNG screenshots"
echo "   - Ensure clean, professional appearance"
echo "   - Remove any test data or dummy content"
echo ""

echo "📋 Quick Commands:"
echo "   Check WordPress status: ./setup-wordpress-test.sh status"
echo "   View logs:             ./setup-wordpress-test.sh logs"
echo "   Open browser:          open http://localhost:8080"
echo ""

echo "Ready to take screenshots! 📸" 