@echo off
echo Running PHP script to set up the database...
"C:\xampp\php\php.exe" "%~dp0setup_database.php"
echo Database setup complete.
pause