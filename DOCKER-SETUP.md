# Docker WordPress Testing Environment

Complete local WordPress environment for testing the 5chat plugin.

## 📦 What's Included

- **WordPress Latest**: Fresh WordPress installation
- **MySQL 8.0**: Database server
- **phpMyAdmin**: Database management interface
- **Plugin Auto-Mount**: Your 5chat plugin is automatically available
- **Debug Mode**: WordPress debug logging enabled

## 🚀 Quick Commands

```bash
# Start WordPress environment
./setup-wordpress-test.sh start

# Stop WordPress environment  
./setup-wordpress-test.sh stop

# Restart environment
./setup-wordpress-test.sh restart

# Reset (delete all data)
./setup-wordpress-test.sh reset

# View logs
./setup-wordpress-test.sh logs

# Check status
./setup-wordpress-test.sh status
```

## 🌐 Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| **WordPress** | http://localhost:8080 | Setup during installation |
| **phpMyAdmin** | http://localhost:8081 | root / rootpassword |

## 📁 Plugin Development

Your plugin is automatically mounted and available at:
- **WordPress Path**: `/wp-content/plugins/fivechat-live-chat`
- **Local Path**: `./fivechat-live-chat`

Any changes to your plugin files are immediately reflected in WordPress!

## 🔧 Configuration Files

- `docker-compose.yml` - Main Docker configuration
- `docker-config.env` - Environment variables
- `setup-wordpress-test.sh` - Management script
- `TESTING-GUIDE.md` - Comprehensive testing instructions

## 📋 Default Database Settings

- **Database**: wordpress
- **Username**: wordpress  
- **Password**: wordpress
- **Host**: db:3306

## 🎯 Testing Workflow

1. **Start Environment**: `./setup-wordpress-test.sh start`
2. **Setup WordPress**: Visit http://localhost:8080
3. **Activate Plugin**: Go to Plugins → Activate 5chat
4. **Test Functionality**: Follow TESTING-GUIDE.md
5. **Debug if Needed**: Check logs and database
6. **Stop When Done**: `./setup-wordpress-test.sh stop`

## 🛡️ Security Notes

This setup is for **testing only**:
- Uses default passwords
- Debug mode enabled
- Not suitable for production
- Designed for local development

## 📱 Port Configuration

Default ports:
- WordPress: 8080
- phpMyAdmin: 8081

If these ports are in use, edit `docker-compose.yml` to change:
```yaml
ports:
  - "8082:80"  # Change 8080 to 8082
```

## 🧹 Cleanup

**Stop containers** (preserves data):
```bash
./setup-wordpress-test.sh stop
```

**Complete reset** (deletes everything):
```bash
./setup-wordpress-test.sh reset
```

Your WordPress testing environment is ready! 🎉 