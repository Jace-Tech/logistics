<?php


function listAllParcels()
{
  global $connect;
  try {
    $result = $connect->query("SELECT p.*, psd.id as psd_id, psd.service, psd.terms, psd.special_handling_section, psd.estimated_date, psd.type FROM parcel p INNER JOIN parcel_shipment_details psd ON p.id = psd.parcel");
    $result->execute();
    return $result->fetchAll();
  } catch (Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}

function listUndeliveredParcels()
{
  global $connect;
  try {
    $result = $connect->query("SELECT * FROM parcel WHERE status LIKE 'not delivered'");
    $result->execute();
    return $result->fetchAll();
  } catch (Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}

function listFewParcels()
{
  global $connect;
  try {
    $result = $connect->query("SELECT p.*, psd.id as psd_id, psd.service, psd.terms, psd.special_handling_section, psd.estimated_date FROM parcel p INNER JOIN parcel_shipment_details psd ON p.id = psd.parcel ORDER BY p.date DESC LIMIT 5");
    $result->execute();
    return $result->fetchAll();
  } catch (Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}

function getParcel(string $id)
{
  global $connect;
  try {
    $result = $connect->prepare("SELECT p.*, psd.id as psd_id, psd.service, psd.terms, psd.special_handling_section, psd.estimated_date, psd.type FROM parcel p INNER JOIN parcel_shipment_details psd ON p.id = psd.parcel WHERE p.id = ?");
    $result->execute([$id]);
    return $result->fetch();
  } catch (Exception $e) {
    setAlert($e->getMessage());
    return [];
  }
}
