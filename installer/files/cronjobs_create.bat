schtasks /create /tn "LightCtrl Execute Queue Command Cron" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\lcheadend\cli.php executeQueue" /F /ru System /sc minute /mo 1
schtasks /create /tn "LightCtrl AutoStatus Update Cron" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\lcheadend\cli.php autoStatusUpdate" /F /ru System /sc minute /mo 2
schtasks /create /tn "LightCtrl Create Schedule Command Cron" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\lcheadend\cli.php createSchedule" /F /ru System /sc minute /mo 1
schtasks /create /tn "LightCtrl Consolidate Consumptions Cron" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\lcheadend\cli.php consolidateConsumption" /F /ru System /sc daily /st 00:00
schtasks /create /tn "LightCtrl Old Data Cleanup Cron" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\lcheadend\cli.php cleanupData" /F /ru System /sc daily /st 01:30
schtasks /create /tn "LightCtrl DB Backup Tool" /tr "C:\xampp\htdocs\lcheadend\BackupTool\backup_tool.bat" /F /ru System /sc daily /st 01:00
