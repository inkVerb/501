<?php

// MySQLi Connection
DEFINE ('DB_NAME', 'food_db');
DEFINE ('DB_USER', 'food_usr');
DEFINE ('DB_PASSWORD', 'foodpassword');
DEFINE ('DB_HOST', 'localhost');

// Database connection
$database = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Character seting
mysqli_set_charset($database, 'utf8mb4');
