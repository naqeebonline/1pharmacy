# Hospital Database Backup System

This project uses **Spatie Laravel Backup** package to automatically backup the database.

## 🔄 Automatic Backup Schedule

The system automatically backs up the database **3 times daily**:
- **6:00 AM** - Morning backup
- **2:00 PM** - Afternoon backup  
- **9:00 PM** - Evening backup

## ✅ Installation Status

✅ **Spatie Laravel Backup package** - Installed  
✅ **MySQL dump path** - Configured  
✅ **Backup schedule** - Configured in `app/Console/Kernel.php`  
⚠️ **Windows Task Scheduler** - Needs to be set up (see below)  

## 📁 Backup Location

Backups are stored in:
```
D:\wamp64\www\hospital\storage\app\backups\AMC HMIS\
```

Each backup is a ZIP file containing the database dump (.sql file).

## 🚀 Setup Instructions

### Step 1: Install Package (Already Done)
The Spatie Laravel Backup package has been installed via Composer.

### Step 2: Setup Windows Task Scheduler

**Run as Administrator:**
1. Right-click on PowerShell
2. Select "Run as Administrator"
3. Navigate to project folder:
   ```powershell
   cd D:\wamp64\www\hospital
   ```
4. Run the setup script:
   ```powershell
   .\setup_backup_scheduler.ps1
   ```

This will create a Windows scheduled task that runs every minute to check for scheduled Laravel commands.

### Alternative: Manual Task Scheduler Setup

If you prefer to set it up manually:

1. Open **Task Scheduler** (taskschd.msc)
2. Click "Create Task"
3. **General Tab:**
   - Name: `Hospital-Laravel-Scheduler`
   - Run whether user is logged on or not: ✓
   - Run with highest privileges: ✓
4. **Triggers Tab:**
   - New trigger: Daily, starting at 12:00 AM
   - Repeat task every: **1 minute**
   - For duration of: **Indefinitely**
5. **Actions Tab:**
   - Action: Start a program
   - Program: `D:\wamp64\www\hospital\run_scheduler.bat`
6. **Settings Tab:**
   - Allow task to run on demand: ✓
   - Start the task if it runs late: ✓

## 🔧 Manual Backup Commands

### Run Manual Backup
```bash
php artisan backup:run --only-db
```

### List All Backups
```bash
php artisan backup:list
```

### Clean Old Backups
```bash
php artisan backup:clean
```

## 📊 Backup Retention Policy

The system automatically manages old backups:
- **All backups**: Kept for 30 days
- **Daily backups**: Kept for 60 days
- **Weekly backups**: Kept for 12 weeks
- **Monthly backups**: Kept for 6 months
- **Yearly backups**: Kept for 3 years

## 🌐 Network Access

The backup files are accessible from the network at:
```
\\192.168.10.127\hospital\storage\app\backups\
```

To access from other systems (Pharmacy, Reception, Lab):
1. Map network drive to `\\192.168.10.127\hospital\`
2. Navigate to `storage\app\backups\`
3. Copy backup files as needed

## 🔍 Verify Backup Setup

### Check if scheduler is working:
```powershell
# Check last run time
Get-ScheduledTask -TaskName "Hospital-Laravel-Scheduler" | Get-ScheduledTaskInfo
```

### Check if backups are being created:
```bash
php artisan backup:list
```

### Test backup manually:
```bash
php artisan backup:run --only-db
```

## 📋 Configuration

Backup configuration is in `config/backup.php`

Key settings:
- **Backup Name**: `hospital-backup`
- **Database Only**: Yes (files excluded from auto backup)
- **Max Storage**: 10GB

## 🚨 Troubleshooting

### Backups not running?
1. Check if Task Scheduler task exists:
   ```powershell
   Get-ScheduledTask -TaskName "Hospital-Laravel-Scheduler"
   ```

2. Check task is enabled and running:
   ```powershell
   Get-ScheduledTask -TaskName "Hospital-Laravel-Scheduler" | Get-ScheduledTaskInfo
   ```

3. Run manual backup to test:
   ```bash
   php artisan backup:run --only-db
   ```

### Permission issues?
- Ensure `storage/app/backups/` folder is writable
- Run Task Scheduler as SYSTEM account

### Can't access backups from network?
- Ensure Windows file sharing is enabled
- Check firewall settings for port 445 (SMB)
- Verify folder permissions

## 📧 Notifications (Optional)

To enable email notifications when backups complete:
1. Configure mail settings in `.env`
2. Uncomment notification settings in `config/backup.php`

## 🔐 Security

- Backups contain sensitive data
- Restrict network access to authorized systems only
- Consider encrypting backups by setting `BACKUP_ARCHIVE_PASSWORD` in `.env`

## ⚡ Quick Commands Reference

| Command | Description |
|---------|-------------|
| `php artisan backup:run --only-db` | Run database backup now |
| `php artisan backup:list` | List all backups |
| `php artisan backup:clean` | Remove old backups |
| `php artisan backup:monitor` | Check backup health |

---

**Server IP**: 192.168.10.127  
**Backup Schedule**: 6 AM, 2 PM, 9 PM daily  
**Retention**: 30 days minimum  
