:: -= Auto backup script tool for Mysql Databases =- ::

:: turn on if you are debugging
@echo off

:: SETTINGS AND PATHS 
:: Note: Do not put spaces before the equal signs or variables will fail

set year=%date:~6,4%
set day=%DATE:~0,2%
set mnt=%DATE:~3,2%

IF %day% LSS 10 SET day=0%day:~1,1%
IF %mnt% LSS 10 SET mnt=0%mnt:~1,1%

set backupdate=%year%-%mnt%-%day%
::echo %backupdate%

:: Name of the database name
set dbname=lcheadend

:: Name of the database user with rights to all tables
set dbuser=lcheadend_user

:: Password for the database user
set dbpass="a1a9998499c99f17caf84603c2977804"

:: local script path
set localPath=%~dp0

:: Error log path - Important in debugging your issues
set errorLogPath="%localPath%backupfiles\dumperrors.txt"

:: Backup Folder
set backupfldr=%localPath%backupfiles\

:: MySQL EXE Path
set mysqldumpexe="C:\xampp\mysql\bin\mysqldump.exe"

:: Path to zip executable
set zipper="%localPath%zip\7za.exe"

:: Number of days to retain .zip backup files 
set retaindays=30

:: DONE WITH SETTINGS


:: --------------------------------------------------------------------------------------------------------------------------------


:: BACKUP PROCEDURE

:: Do the backup
echo.
echo Perform the Database Backup to a sql file
%mysqldumpexe% --user=%dbuser% --password=%dbpass% --log-error=%errorLogPath% %dbname% > "%backupfldr%%dbname%.%backupdate%.sql"

:: Do the zip with all the sql files found in the backup folder 
echo.
echo Zipping all files ending in .sql in the folder
%zipper% a -tzip "%backupfldr%FullBackup.%backupdate%.zip" "%backupfldr%*.sql"

:: Cleaning sql files
echo.
echo Deleting all the files ending in .sql only
del "%backupfldr%*.sql"

:: Delete older backups
echo.
echo Deleting zip files older than 30 days now
Forfiles /p %backupfldr% /s /m *.* /d -%retaindays% /c "cmd /c del @path"


::TO FTP YOUR FILE UNCOMMENT THESE LINES AND UPDATE

::cd\[path to directory where your file is saved]
::@echo off
::echo user [here comes your ftp username]>ftpup.dat
::echo [here comes ftp password]>>ftpup.dat
::echo [optional line; you can put "cd" command to navigate through the folders on the ftp server; eg. cd\folder1\folder2]>>ftpup.dat
::echo binary>>ftpup.dat
::echo put [file name comes here; eg. FullBackup.%backupdate%.zip]>>ftpup.dat
::echo quit>>ftpup.dat
::ftp -n -s:ftpup.dat [insert ftp server here; eg. myserver.com]
::del ftpup.dat

echo.
echo Backup Tool complete successfuly
