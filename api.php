<?php

//Create Connection
$conn = new mysqli('localhost', 'root', '', 'plantvsundead');

// Check connection
if ($conn->connect_error) {
	$message = array("status"=>"error", "message"=>$conn->connect_error);
	echo json_encode($message);
	return;
}

// Check if plant_hash is set 
if(isset($_GET["plant_hash"]))
{
	$plant_hash = $_GET["plant_hash"];

	if(strlen($plant_hash) <= 0)
	{
		$message = array("status"=>"error", "message"=>"Plant ID not set");
		echo json_encode($message);
		return;
	}
	
	if(!ValidateID($plant_hash))
	{
		$message = array("status"=>"error", "message"=>"Invalid Plant ID");
		echo json_encode($message);
		return;
	}
}else
{
	$message = array("status"=>"error", "message"=>"Plant ID not set");
	echo json_encode($message);
	return;
}

// User-given Plant Data
$plant_id = substr($plant_hash, 3, 2);
$plant_id2 = substr($plant_hash, 5, 1);
$plant_id_full = substr($plant_hash, 3, 2) . "_" . substr($plant_hash, 5, 1);
$plant_rarity = substr($plant_hash, 6, 2);
$plant_rarity_name = GetRarityString($plant_rarity);
$plant_type = substr($plant_hash, 0, 3) == "100" ? "plant" : "mtree";
$plant_icon_URL = 'https://pvuresources.s3.ap-southeast-2.amazonaws.com/icon/'. $plant_type .'/'. $plant_id_full .'.png';

$plant_data_sql = "SELECT * FROM plants WHERE id = $plant_id";
$plant_data_result = $conn->query($plant_data_sql);

// Plant SQL Data
if ($plant_data_result->num_rows > 0) {
	$row = $plant_data_result->fetch_assoc();
	$id = $row["id"];
	$element = $row["element"];
	$duration = $row["duration"];
	$common_base_LE = $row["common_base_LE"];
	$uncommon_base_LE = $row["uncommon_base_LE"];
	$rare_base_LE = $row["rare_base_LE"];
	$mythic_base_LE = $row["mythic_base_LE"];
	$LE_by_rarity = $row["LE_by_rarity"];
	
	$data = array($id,$element,$duration,$common_base_LE,$uncommon_base_LE,$rare_base_LE,$mythic_base_LE,$LE_by_rarity);
	$message = array("status"=>"success","plant_id"=>$plant_id,"plant_id_full"=>$plant_id_full,"plant_type"=>$plant_type,"plant_icon_URL"=>$plant_icon_URL,"plant_rarity"=>$plant_rarity,"plant_rarity_name"=>$plant_rarity_name,"plant_id"=>$data[0],"plant_element"=>$data[1],"plant_duration"=>$data[2],"common_base_LE"=>$data[3],"uncommon_base_LE"=>$data[4],"rare_base_LE"=>$data[5],"mythic_base_LE"=>$data[6],"LE_by_rarity"=>$data[7]);
	echo json_encode($message);
} else {
	$message = array("status"=>"error","message"=>"Plant ID not found on our database.");
	echo json_encode($message);
}
$conn->close();


function ValidateID($id)
{
	if(strlen($id) == 10 && (substr($id, 0, 3) == "100" || substr($id, 0, 3) == "200"))
	{
		return true;
	}
	
	return false;
}

function GetRarityString($rarity)
{
	if($rarity >= 0 && $rarity <= 59) return "Common";
	if($rarity >= 60 && $rarity <= 88) return "Uncommon";
	if($rarity >= 89 && $rarity <= 98) return "Rare";
	if($rarity >= 99) return "Mythic";
}