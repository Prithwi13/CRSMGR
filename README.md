To install and run the Course Management (CrsMgr) application, follow these steps: 

Download and Install XAMPP: XAMPP is a free and open-source cross-platform web server solution stack package that includes Apache HTTP Server, MariaDB database, and interpreters for scripts written in the PHP and Perl programming languages. You can download it from the official website and follow the instructions to install it on your system. 

Clone the Repository: Clone the CrsMgr repository to your local machine. 

Move the Project Folder: Move the cloned CrsMgr project folder into the htdocs directory of your XAMPP installation. This directory is where Apache looks for files to serve on your localhost. 

Start XAMPP: Launch the XAMPP control panel and start the Apache and MySQL services. 

Create the Database: Open a web browser and go to http://localhost/phpmyadmin/ to access the phpMyAdmin interface. Create a new database named course-management. 

Import the Database Structure: In the course-management database, select the Import tab and import the course-management.sql file from the database directory of the CrsMgr project. This will create all the necessary tables for the application. 

Access the Application: You can now access the CrsMgr application by opening a web browser and navigating to http://localhost/CrsMgr. 
