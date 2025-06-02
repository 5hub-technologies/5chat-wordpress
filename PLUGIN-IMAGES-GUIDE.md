# 5chat WordPress Plugin - Images Guide

This guide covers all the images needed to make your plugin look professional in the WordPress directory.

## 📋 **Required Images Checklist**

### **1. Plugin Icon** ⭐ (ESSENTIAL)
- [ ] `icon-128x128.png` - Standard resolution icon
- [ ] `icon-256x256.png` - High resolution icon

**Specifications:**
- **Dimensions**: 128×128px and 256×256px
- **Format**: PNG with transparent background
- **Purpose**: Shows in WordPress admin and plugin directory
- **Design**: Simple, recognizable logo representing 5chat

### **2. Plugin Banner** 🎨 (HIGHLY RECOMMENDED)
- [ ] `banner-772x250.png` - Standard banner
- [ ] `banner-1544x500.png` - Retina/high-DPI banner

**Specifications:**
- **Dimensions**: 772×250px (1544×500px for retina)
- **Format**: PNG or JPG
- **Purpose**: Header image in WordPress.org directory
- **Design**: Professional banner with 5chat branding and "Live Chat" text

### **3. Screenshots** 📸 (IMPORTANT FOR USERS)
- [ ] `screenshot-1.png` - Settings page
- [ ] `screenshot-2.png` - Admin notice
- [ ] `screenshot-3.png` - Chat widget on frontend

**Specifications:**
- **Dimensions**: 1200×900px (4:3 ratio recommended)
- **Format**: PNG or JPG (PNG preferred for UI screenshots)
- **Purpose**: Show plugin functionality to potential users

## 📁 **File Structure**

```
fivechat-live-chat/
├── assets/                          # Plugin images directory
│   ├── icon-128x128.png            # Standard icon
│   ├── icon-256x256.png            # High-res icon
│   ├── banner-772x250.png          # Standard banner
│   ├── banner-1544x500.png         # Retina banner
│   ├── screenshot-1.png            # Settings page screenshot
│   ├── screenshot-2.png            # Admin notice screenshot
│   └── screenshot-3.png            # Frontend widget screenshot
├── 5chat.php                        # Main plugin file
├── readme.txt                       # Plugin readme
└── ...other plugin files
```

## 🎨 **Design Guidelines**

### **Plugin Icon Design**
- **Colors**: Use 5chat brand colors (blue/teal themes work well for chat)
- **Elements**: Chat bubble, speech balloon, or messaging icon
- **Style**: Clean, modern, flat design
- **Text**: Avoid small text - use symbols/icons
- **Background**: Transparent PNG

**Good icon examples:**
- Chat bubble with "5" inside
- Speech balloon with lightning bolt (fast chat)
- Modern messaging icon with 5chat colors

### **Banner Design**
- **Left side**: 5chat logo/icon
- **Center**: "5chat Live Chat" title
- **Right side**: "Fast • Simple • Powerful" tagline
- **Background**: Gradient or professional pattern
- **Colors**: Professional blue/teal/green tones

### **Screenshot Content**

#### Screenshot 1 - Settings Page
- Show the clean 5chat settings interface
- Highlight the token input field
- Include helpful text and instructions
- Clean WordPress admin appearance

#### Screenshot 2 - Admin Notice  
- Show the warning notice when token is missing
- Demonstrate user-friendly messaging
- Show the link to settings page

#### Screenshot 3 - Frontend Widget
- Mock website with 5chat widget visible
- Show the chat bubble in bottom corner
- Professional website design
- Widget should look integrated and professional

## 🛠 **Creating Images**

### **Tools You Can Use**
- **Canva** (easiest) - Professional templates available
- **Figma** (free) - Professional design tool
- **Adobe Photoshop** - Industry standard
- **GIMP** (free) - Open source alternative
- **Sketch** (Mac) - UI/UX focused

### **Quick Templates**

#### **Icon Template (128x128)**
```
Background: Transparent
Colors: #3B82F6 (blue) or #10B981 (green)
Element: Chat bubble or speech balloon
Text: "5" or "5chat" (if legible)
Style: Flat design with subtle shadow
```

#### **Banner Template (772x250)**
```
Background: Linear gradient (light to dark brand color)
Left (200px): 5chat icon/logo
Center (372px): "5chat Live Chat" title (bold, white)
Right (200px): "WordPress Integration" subtitle
Font: Sans-serif, professional
```

## 📱 **Taking Screenshots**

### **Settings Page Screenshot**
1. Start your Docker WordPress environment
2. Activate the 5chat plugin
3. Go to Settings → 5chat
4. Take a clean screenshot at 1200×900px
5. Crop to show just the settings content area

### **Admin Notice Screenshot** 
1. Activate plugin without setting token
2. Go to WordPress dashboard
3. Screenshot the admin notice at top
4. Crop to show notice and some context

### **Frontend Widget Screenshot**
1. Create a demo token in settings
2. Visit the frontend site
3. Use browser dev tools to simulate 5chat widget
4. Screenshot showing widget integration

## 🔧 **Technical Requirements**

### **For WordPress.org Submission**
All images must be placed in the plugin's `assets` directory and will be uploaded separately to WordPress.org SVN repository.

### **File Naming Convention**
- Icons: `icon-128x128.png`, `icon-256x256.png`
- Banners: `banner-772x250.png`, `banner-1544x500.png`  
- Screenshots: `screenshot-1.png`, `screenshot-2.png`, etc.

### **Quality Guidelines**
- **File size**: Keep under 1MB each
- **Compression**: Optimize for web without quality loss
- **Colors**: Use web-safe colors
- **Text**: Ensure readability at small sizes

## 🎯 **Image Specifications Summary**

| Image Type | Dimensions | Format | Background | Usage |
|------------|------------|--------|------------|--------|
| Icon Small | 128×128px | PNG | Transparent | Admin plugins page |
| Icon Large | 256×256px | PNG | Transparent | High-DPI displays |
| Banner Small | 772×250px | PNG/JPG | Solid/Gradient | Plugin directory |
| Banner Large | 1544×500px | PNG/JPG | Solid/Gradient | Retina displays |
| Screenshots | 1200×900px | PNG/JPG | N/A | Feature demonstration |

## 📤 **Where Images Go**

### **During Development**
- Store in `fivechat-live-chat/assets/` directory
- Include in your plugin zip file

### **For WordPress.org**
- Upload to separate `assets` directory in SVN
- Not included in plugin download
- Managed through WordPress.org dashboard

## ✨ **Pro Tips**

1. **Consistency**: Use same colors/style across all images
2. **Branding**: Incorporate 5chat visual identity
3. **Clarity**: Ensure images look good at small sizes
4. **Testing**: View images in WordPress admin to verify appearance
5. **Updates**: You can update images without updating plugin version

## 🎨 **Example Content Ideas**

### **Icon Concepts**
- Chat bubble with "5" number
- Lightning bolt + speech bubble (fast chat)
- Modern messaging icon in brand colors
- Simplified 5chat logo

### **Banner Text Ideas**
- "5chat Live Chat - Blazing Fast Customer Support"
- "Add Professional Live Chat to WordPress in Minutes"
- "5chat - The Fastest WordPress Chat Integration"

### **Screenshot Captions** (for readme.txt)
1. "Simple settings page - just paste your Website Token"
2. "Helpful admin notice guides you to complete setup"  
3. "Professional chat widget integrates seamlessly"

Your plugin will look professional and trustworthy with these images! 🚀 