<?php


function redirect(string $url) {
  header("Location: $url");
}

function getGreeting() {
  $hour = date("H");

  if($hour >= 0 && $hour < 12) return "Morning";
  else if($hour >= 12 && $hour < 16) return "Afternoon";
  else return "Evening";
}

function generateParcelID() {
  return join("", explode(".", uniqid("OCP", true)));
}

function setAlert(string $message, string $type = "error") {
  $_SESSION['ADMIN_ALERT'] = json_encode([
    "message" => $message,
    "type" => $type
  ]);
  return true;
}

function sanitizer(string $string): string {
  return htmlspecialchars($string);
}
