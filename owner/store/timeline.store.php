<?php

function listParcelTimelines(string $parcel, string $order = "ASC") {
  global $connect;
  try {
    $query = "SELECT * FROM timeline WHERE parcel = ? ORDER BY `date` ASC";
    if($order === "DESC") $query = "SELECT * FROM timeline WHERE parcel = ? ORDER BY `date` DESC";
    
    $result = $connect->prepare($query);
    $result->execute([$parcel]);
    return $result->fetchAll();
  } catch (Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}


function getTimeline(string $id, string $order = "ASC") {
  global $connect;
  try {
    $query = "SELECT * FROM timeline WHERE id = ? ORDER BY `date` ASC";
    if($order === "DESC") $query = "SELECT * FROM timeline WHERE id = ? ORDER BY `date` DESC";
    
    $result = $connect->prepare($query);
    $result->execute([$id]);
    return $result->fetch();
  } catch (Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}

