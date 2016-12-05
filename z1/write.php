<?php
$mysqli = mysqli_connect("192.168.101.145", "gfxcompl_l24", "", "gfxcompl_IS");
if (mysqli_connect_errno()) {
    echo "nie poszlo";
    exit();
}
//var_dump($mysqli);
$post = file_get_contents("php://input");

$computers = json_decode($post);
$sql = "INSERT INTO `computers`(`dep_name`, `owner_name`, `owner_surname`, `date_owned`, `is_efficient`, `serial_number`, `issue_date`, `issue_desc`, `repair_fee`, `mnf_name`, `class_`, `class_sm`, `activity`, `machine_desc`) VALUES ";

$val_list = Array();
foreach($computers as $row) {
	$dep_name = mysqli_real_escape_string($mysqli, $row->dep_name);
	$owner_name = mysqli_real_escape_string($mysqli, $row->owner_name);
	$owner_surname = mysqli_real_escape_string($mysqli, $row->owner_surname);
	$date_owned = mysqli_real_escape_string($mysqli, $row->date_owned);
	$is_efficient = (int) $row->is_efficient;
	$serial_number =  mysqli_real_escape_string($mysqli, $row->serial_number);
	$issue_date = mysqli_real_escape_string($mysqli, $row->issue_date);
	$issue_desc = mysqli_real_escape_string($mysqli, $row->issue_desc);
	$repair_fee = (float) $row->repair_fee;
	$mnf_name = mysqli_real_escape_string($mysqli, $row->mnf_name);
	$class_ =  (int) $row->class_;
	$class_sm =  mysqli_real_escape_string($mysqli, $row->class_sm);
	$activity =  mysqli_real_escape_string($mysqli, $row->activity);
	$machine_desc = mysqli_real_escape_string($mysqli, $row->machine_desc);

	$val_list[] = "('$dep_name', '$owner_name', '$owner_surname', '$date_owned', '$is_efficient', '$serial_number', '$issue_date', '$issue_desc', '$repair_fee', '$mnf_name', '$class_', '$class_sm', '$activity', '$machine_desc')";
}


$sql .= implode(',', $val_list);

mysqli_query($mysqli, $sql);
echo mysqli_affected_rows($mysqli);

mysqli_close($mysqli);

?>