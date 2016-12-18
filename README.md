# Project Description

Lighting Controls is a British based company focused on Lighting Controls modules that allow automation and computer controlled lighting structures and buildings.

The project consisted in implement a web based frontend for the existing TCP-IP modules, also called Area Controllers. Those Area Controllers act as an inter-floor or inter-zonal connection controller that offer excellent functionality and flexibility. They also provide interfaces to Fire Alarms, BMS systems and other control systems.

The frontend would allow the communication with the Area controllers through direct user interaction to allow for the sending of DALI commands, but also for the periodical retrieval of status information, triggered by cronjobs.

The information would be stored and reported via the frontend. Those reports would be via textual and graphical (charts),

# Installation
For development purposes, you would required to have a Xampp environment up and running. You would then need to point your root folder of the application to the www folder.

You will also need to create a database using the provided intsys_v1.0.0.sql in the db folder.

You will then need to perform the migrations. To do that, click on http://www.yiiframework.com/doc/guide/1.1/en/database.migration to know how to perform Yii migrations.Configurations can be set in \www\protected\config\console.php and \www\protected\config\main.php

# Deployment
To perform automated deploy packages you will have to use Inno Setup. You can download it here http://jrsoftware.org/isdl.php

After that you can create the installer bu opening the file \installer\LC_Headend_Setup.iss and perform the compile command.

The installer will not provide the xampp package needed to run the application on the production environment, so it would have to be installed manually, prior to install the application.