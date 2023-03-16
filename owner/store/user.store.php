<?php 

function listUsers() {
  global $connect;
  try {
    $result = $connect->query("SELECT * FROM users");
    $result->execute();
    return $result->fetchAll();
  }
  catch(Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}