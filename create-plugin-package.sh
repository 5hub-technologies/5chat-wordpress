#!/bin/bash

# 5chat WordPress Plugin Package Creator
# This script creates a distributable zip file of the plugin

PLUGIN_NAME="5chat-blazing-fast-live-chat"
VERSION="1.0.0"
PACKAGE_NAME="5chat-blazing-fast-live-chat"

echo "Creating package for ${PLUGIN_NAME} v${VERSION}..."

# Create a temporary directory for packaging
mkdir -p "temp-package"

# Copy plugin files to temp directory
cp -r "${PLUGIN_NAME}" "temp-package/"

# Create zip file
cd temp-package
zip -r "../${PACKAGE_NAME}.zip" "${PLUGIN_NAME}"
cd ..

# Clean up temp directory
rm -rf "temp-package"

echo "Package created: ${PACKAGE_NAME}.zip"
echo ""
echo "File contents:"
unzip -l "${PACKAGE_NAME}.zip"

echo ""
echo "📷 Plugin Images:"
if [ -d "${PLUGIN_NAME}/assets" ]; then
    echo "✅ Assets directory found - plugin images included"
    ls -la "${PLUGIN_NAME}/assets/"
else
    echo "⚠️  Assets directory not found"
    echo "   Add plugin images to ${PLUGIN_NAME}/assets/ for professional appearance"
    echo "   Required: icon-128x128.png, icon-256x256.png, banner-772x250.png, etc."
fi

echo ""
echo "Installation Instructions:"
echo "1. Upload ${PACKAGE_NAME}.zip to WordPress admin → Plugins → Add New → Upload Plugin"
echo "2. Or extract to wp-content/plugins/ directory"
echo "3. Activate the plugin"
echo "4. Go to Settings → 5chat to configure"
echo ""
echo "For WordPress.org submission:"
echo "- Upload plugin zip to WordPress.org"
echo "- Upload assets separately to SVN assets directory"
