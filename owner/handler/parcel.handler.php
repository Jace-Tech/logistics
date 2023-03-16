<?php 
session_start();
require_once("../db/config.php");
require_once("../utils/helpers.php");


if(isset($_POST['add-parcel'])) {
  $id = sanitizer($_POST['id']);
  $title = sanitizer($_POST['title']);
  $weight = sanitizer($_POST['weight']);
  $totalPieces = sanitizer($_POST['totalPieces']);
  $dimensions = sanitizer($_POST['dimensions']);
  $packaging = sanitizer($_POST['packaging']) ?? NULL;
  $date = sanitizer($_POST['date']);

  $service = sanitizer($_POST['service']);
  $terms = sanitizer($_POST['terms']);
  $specialHandlingSection = sanitizer($_POST['specialHandlingSection']);
  $estimatedDate = sanitizer($_POST['estimatedDate']);

  try {
    if(!$id || !$weight || !$totalPieces || !$dimensions || !$date || !$service || !$terms || !$specialHandlingSection || !$estimatedDate) throw new Exception("All fields marked with (*) are required!");

    // Add the new parcel
    $query = "INSERT INTO parcel(id, title, weight, total_pieces, dimensions, packaging, date) VALUES (:id, :title, :weight, :totalPieces, :dimensions, :packaging, :date)";
    $result = $connect->prepare($query);
    $result->execute([
      "id" => $id,
      "title" => $title,
      "weight" => $weight,
      "totalPieces" => $totalPieces,
      "dimensions" => $dimensions,
      "packaging" => $packaging,
      "date" => $date,
    ]);

    if(!$result->rowCount()) throw new Exception("Error adding parcel");

    // Add the service
    $query = "INSERT INTO parcel_shipment_details(id, parcel, service, terms, estimated_date, special_handling_section) VALUES (:serviceId, :parcelId, :service, :terms, :estimatedDate, :handling)";
    $result = $connect->prepare($query);
    $result->execute([
      "serviceId" => uniqid("PSRV_"),
      "parcelId" => $id,
      "service" => $service,
      "terms" => $terms,
      "estimatedDate" => $estimatedDate,
      "handling" => $specialHandlingSection
    ]);

    if(!$result->rowCount()) throw new Exception("Error adding parcel");
    
    setAlert("Parcel added!", "success");
    redirect("../view-parcel");
  }
  catch(Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../create-parcel");
  }


}
elseif(isset($_POST['edit-parcel'])) {
  $id = sanitizer($_POST['id']);
  $title = sanitizer($_POST['title']);
  $weight = sanitizer($_POST['weight']);
  $totalPieces = sanitizer($_POST['totalPieces']);
  $dimensions = sanitizer($_POST['dimensions']);
  $packaging = sanitizer($_POST['packaging']) ?? NULL;
  $date = sanitizer($_POST['date']);

  $service = sanitizer($_POST['service']);
  $terms = sanitizer($_POST['terms']);
  $specialHandlingSection = sanitizer($_POST['specialHandlingSection']);
  $estimatedDate = sanitizer($_POST['estimatedDate']);

  $editId = $_POST['edit-parcel'];

  try {
    $query = "UPDATE parcel SET id = :parcelId, title = :title, weight = :weight, total_pieces = :totalPieces, dimensions = :dimensions, user = :user, packaging = :packaging, date = :date WHERE id = :editId";
    $result = $connect->prepare($query);
    $result->execute([
      "parcelId" => $id,
      "title" => $title,
      "weight" => $weight,
      "totalPieces" => $totalPieces,
      "dimensions" => $dimensions,
      "user" => $user,
      "packaging" => $packaging,
      "date" => $date,
      "editId" => $editId,
    ]);
  
    if(!$result->rowCount()) throw new Exception("Failed to edit parcel");
  
    // Update the Service
    $query = "UPDATE parcel_shipment_details SET parcel = :parcelId, service = :service, terms = :terms, estimated_date = :estimatedDate,special_handling_section = :handlerSection WHERE parcel = :editId";
    $result = $connect->prepare($query);
    $result->execute([
      "parcelId" => $id,
      "service" => $service,
      "terms" => $terms,
      "estimatedDate" => $estimatedDate,
      "handlerSection" => $specialHandlingSection,
      "editId" => $editId,
    ]);
  
    if(!$result->rowCount()) throw new Exception("Failed to update parcel");

    setAlert("Parcel updated!", "success");
    redirect("../view-parcel");

  } catch (Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../edit-parcel?parcel_id=$editId");
  }

}
elseif(isset($_POST['delete-parcel'])) {
  $id = $_POST['delete-parcel'];
  try {
    $result = $connect->prepare("DELETE FROM parcel WHERE id = ?");
    $result->execute([$id]);

    if(!$result->rowCount()) throw new Exception("Failed to delete parcel");
    setAlert("Parcel deleted!", "success");
    redirect($_SERVER['HTTP_REFERER'] ?? "../view-parcel");
  }
  catch(Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../view-parcel");
  }
}
else {
  redirect($_SERVER['HTTP_REFERER'] ?? "../view-parcel");
}