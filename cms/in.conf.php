<?php
// This is the default in.conf.php file
// This file will be over-written during install
// Delete this to run a fresh install
// Leave this here, but the populated fields in install.php may need replacing
// Or run the SQL command from a BASH terminal to create these same credentials

// These values can be changed
$db_name = 'blog_db';
$db_user = 'blog_db_user';
$db_pass = 'blogdbpassword';
$db_host = 'localhost';
$blog_web_base = 'http://localhost'; // Probably wrong, will need changing

// This disables the installer; leave commented unless admin user has been created; uncomment to use install.php to create a new admin user and/or change SQL credentials
//DEFINE ('DB_CONFIGURED', true);

// Create the above database from CLI with:
// mariadb -e "
// CREATE DATABASE blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
// GRANT ALL PRIVILEGES ON blog_db.* TO 'blog_db_user'@'localhost' IDENTIFIED BY 'blogdbpassword';
// FLUSH PRIVILEGES;"