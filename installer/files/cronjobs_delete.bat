@echo off

:loop1
schtasks /query > doh
findstr /B /I /R /C:"LightCtrl[ ]Execute[ ]Queue[ ]Command[ ]Cron.*Running" doh 
if %errorlevel%==0  (
	timeout 5
	goto :loop1
)
schtasks /delete /tn "LightCtrl Execute Queue Command Cron" /f

:loop2
schtasks /query > doh
findstr /B /I /R /C:"LightCtrl[ ]AutoStatus[ ]Update[ ]Cron.*Running" doh 
if %errorlevel%==0  (
	timeout 5
	goto :loop2
)
schtasks /delete /tn "LightCtrl AutoStatus Update Cron" /f

:loop3
schtasks /query > doh
findstr /B /I /R /C:"LightCtrl[ ]Create[ ]Schedule[ ]Command[ ]Cron.*Running" doh 
if %errorlevel%==0  (
	timeout 5
	goto :loop3
)
schtasks /delete /tn "LightCtrl Create Schedule Command Cron" /f

:loop4
schtasks /query > doh
findstr /B /I /R /C:"LightCtrl[ ]DB[ ]Backup[ ]Tool.*Running" doh 
if %errorlevel%==0  (
	timeout 5
	goto :loop4
)
schtasks /delete /tn "LightCtrl DB Backup Tool" /f

:loop5
schtasks /query > doh
findstr /B /I /R /C:"LightCtrl[ ]Old[ ]Data[ ]Cleanup[ ]Cron.*Running" doh 
if %errorlevel%==0  (
	timeout 5
	goto :loop5
)
schtasks /delete /tn "LightCtrl Old Data Cleanup Cron" /f

del doh