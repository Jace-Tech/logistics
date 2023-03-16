<?php
session_start();
require_once("../db/config.php");
require_once("../utils/helpers.php");


if (isset($_POST['add-timeline'])) {
  $parcel = sanitizer($_POST['parcel']);
  $message = sanitizer($_POST['message']);
  $date = sanitizer($_POST['date']);
  $location = sanitizer($_POST['location']);

  $summary = $_POST['summary'];
  $final = $_POST['final'];

  try {
    // Check if is Final
    if ($final == "1") {
      // Check if there's a final before
      $resCheck = $connect->prepare("SELECT * FROM timeline WHERE parcel = ? AND is_delivered = ?");
      $resCheck->execute([$parcel, 1]);

      if ($resCheck->rowCount()) {
        $pastFinalId = $resCheck->fetch()['id'];

        // Update the prev final
        $updateRes = $connect->prepare("UPDATE timeline SET is_delivered = ? WHERE id = ? ");
        $updateRes->execute([0, $pastFinalId]);
      }
    }

    // ADD TIMELINE
    $result = $connect->prepare("INSERT INTO timeline(id, parcel, message, location, date, is_summary, is_delivered) VALUES (:id, :parcel, :message, :location, :date, :isSummary, :isFinal)");
    $result->execute([
      "id" => uniqid("TML_"),
      "parcel" => $parcel,
      "message" => $message,
      "location" => $location,
      "date" => $date,
      "isSummary" => $summary,
      "isFinal" => 0
    ]);

    if (!$result->rowCount()) throw new Exception("Failed to create timeline");
    setAlert("Timeline created!", "success");
    redirect($_SERVER['HTTP_REFERER'] ?? "../create-timeline");

  } catch (Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../create-timeline");
  }
} 
elseif(isset($_POST['edit-timeline'])){
  $id = $_POST['edit-timeline'];
  $message = sanitizer($_POST['message']);
  $location = sanitizer($_POST['location']);
  $date = $_POST['date'];
  $final = $_POST['final'] ?? 0;
  $summary = $_POST['summary'] ?? 0;
  $parcelID = $_POST['parcel_id'];

  try {
    $query = "UPDATE timeline SET message = :message, location = :location, date = :date, is_delivered = :final, is_summary = :summary WHERE id = :id";
    $result = $connect->prepare($query);
    $result->execute([
      "message" => $message,
      "location" => $location,
      "date" => $date,
      "final" => $final,
      "summary" => $summary,
      "id" => $id
    ]);

    if(!$result->rowCount()) throw new Exception("Failed update timeline");

    setAlert("Timeline updated!", "success");
    redirect("../view-timeline?id=$parcelID");

  } catch (Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../edit-timeline?timeline_id=$id");
  }
}
else {
  redirect($_REQUEST['HTTP_REFERER'] ?? "../view-timeline");
}
