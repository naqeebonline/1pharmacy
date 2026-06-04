# Hospital Backup System - Windows Task Scheduler Setup
# This script sets up automatic task scheduling for Laravel backups

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Hospital Backup System - Setup Script" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Define variables
$taskName = "Hospital-Laravel-Scheduler"
$scriptPath = "D:\wamp64\www\hospital\run_scheduler.bat"
$phpPath = "D:\wamp64\bin\php\php8.3.6\php.exe"
$projectPath = "D:\wamp64\www\hospital"

# Check if running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Please right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Press Enter to exit"
    exit
}

Write-Host "Checking if task already exists..." -ForegroundColor Yellow

# Check if task exists
$existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue

if ($existingTask) {
    Write-Host "Task '$taskName' already exists. Removing old task..." -ForegroundColor Yellow
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    Write-Host "Old task removed successfully." -ForegroundColor Green
}

Write-Host ""
Write-Host "Creating new scheduled task..." -ForegroundColor Yellow

# Create action to run the batch file
$action = New-ScheduledTaskAction -Execute $scriptPath

# Create trigger to run every minute
$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date).Date -RepetitionInterval (New-TimeSpan -Minutes 1)

# Create settings
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable:$false -DontStopOnIdleEnd

# Create principal (run as SYSTEM)
$principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

# Register the task
try {
    Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "Runs Laravel scheduler every minute for Hospital Backup System (backups at 6 AM, 2 PM, 9 PM)"
    Write-Host ""
    Write-Host "==================================================" -ForegroundColor Green
    Write-Host "SUCCESS! Task scheduled successfully!" -ForegroundColor Green
    Write-Host "==================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Task Details:" -ForegroundColor Cyan
    Write-Host "  - Task Name: $taskName" -ForegroundColor White
    Write-Host "  - Runs every: 1 minute" -ForegroundColor White
    Write-Host "  - Backup times: 6:00 AM, 2:00 PM, 9:00 PM" -ForegroundColor White
    Write-Host "  - Backup location: $projectPath\storage\app\backups" -ForegroundColor White
    Write-Host ""
    Write-Host "The scheduler is now active and will run automatically." -ForegroundColor Green
    Write-Host ""
    
    # Test the backup command
    Write-Host "Testing backup command..." -ForegroundColor Yellow
    Set-Location -Path $projectPath
    & $phpPath artisan backup:run --only-db
    
} catch {
    Write-Host ""
    Write-Host "ERROR: Failed to create scheduled task!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Additional Commands:" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "To manually run a backup:" -ForegroundColor Yellow
Write-Host "  php artisan backup:run --only-db" -ForegroundColor White
Write-Host ""
Write-Host "To check backup status:" -ForegroundColor Yellow
Write-Host "  php artisan backup:list" -ForegroundColor White
Write-Host ""
Write-Host "To clean old backups:" -ForegroundColor Yellow
Write-Host "  php artisan backup:clean" -ForegroundColor White
Write-Host ""
Write-Host "To view scheduled task:" -ForegroundColor Yellow
Write-Host "  Get-ScheduledTask -TaskName '$taskName'" -ForegroundColor White
Write-Host ""
Write-Host "To remove scheduled task:" -ForegroundColor Yellow
Write-Host "  Unregister-ScheduledTask -TaskName '$taskName' -Confirm:`$false" -ForegroundColor White
Write-Host ""

Read-Host "Press Enter to exit"
