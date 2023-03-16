<?php 

define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "");
define("DB_NAME", "logistics");

try {
  $dsn = "mysql:host=" . HOST . ";dbname=" . DB_NAME;
  $connect = new PDO($dsn, USER, PASSWORD);
  
  $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(Exception $e) {
  die("<p>Can't connect to database; Please contact the developer 🙃</p>");
}