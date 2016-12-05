<?php
function arrayToXML($output, &$xml) {
    foreach($output as $key => $value) {
        if(is_numeric($key)){
            $key = 'item'.$key;
        }
      if(is_array($value)) {
            $subnode = $xml->addChild($key);
            arrayToXML($value, $subnode);
        }  else {
            $xml->addChild($key, $value);
        }
     }
}

function outputXML($result, $root, $group = 'item', $additional = null) {
	$output = Array();
	$i = 0;
		while($row = mysqli_fetch_assoc($result)) {
			$output[$group.'_'.$i] = $row;
			$i++;
		}
		
	if(!is_null($additional))
		$output =  array_merge_recursive($additional, $output);

	$root = '<?xml version="1.0"?><'.$root.'></'.$root.'>';
	$xml = new SimpleXMLElement($root);
	arrayToXML($output, $xml);
	 
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($xml->asXML());
	$dom->save("results.xml");

	echo json_encode("results.xml");
}

$action = $_GET['action'];
$mysqli = mysqli_connect("192.168.101.145", "gfxcompl_l24", "", "gfxcompl_IS");
if (mysqli_connect_errno()) {
    echo "nie poszlo";
    exit();
}

$post = file_get_contents("php://input");
$args = json_decode($post);

switch($action) {
	case 'countMachinesByClass':
		$manufacturer = mysqli_real_escape_string($mysqli, $args[0]->mnf);
		$result = mysqli_query($mysqli, "CALL countMachinesByClass('$manufacturer')");
		
		if(!empty($manufacturer))
			$additional = array('manufacturer' => $manufacturer);
		outputXML($result, 'summary', 'class', $additional);
	break;
	
	case 'countMachinesByManufacturer':
		$year = (int) $args[0]->year;
		$result = mysqli_query($mysqli, "CALL countMachinesByManufacturer($year)");
		
		$additional = array('year' => $year);
		outputXML($result, 'summary', 'manufacturer', $additional);
	break;
	
	case 'get10MostExpensive':
		$eff_only = (int) $args[0]->eff_only;
		$result = mysqli_query($mysqli, "CALL get10MostExpensive($eff_only)");
		
		if($eff_only === 1)
			$additional = array('efficient_only' => '1');
		
		outputXML($result, 'summary', 'machine', $additional);
	break;
	
	case 'getAverageCost':
		$manufacturer = mysqli_real_escape_string($mysqli, $args[0]->mnf);
		$result = mysqli_query($mysqli, "CALL getAverageCost('$manufacturer')");
		
		outputXML($result, 'summary', 'cost_info');
	break;
	
	case 'getLastIssues':
		$date_from = mysqli_real_escape_string($mysqli, $args[0]->date_from);
		$date_to = mysqli_real_escape_string($mysqli, $args[0]->date_to);
		if(empty($date_from)) {
			$tmp_date = new DateTime('2000-01-01');
			$date_from = $tmp_date->format('Y-m-d');
		}
		
		if(empty($date_to)) {
			$tmp_date = new DateTime();
			$date_to = $tmp_date->format('Y-m-d');
		}
		
		$result = mysqli_query($mysqli, "CALL getLastIssues('$date_from', '$date_to')");
		
		$additional = array('date_from' => $date_from, 'date_to' => $date_to);
		outputXML($result, 'summary', 'issue', $additional);
	break;
	
	case 'getLastNonEfficient':
		$cnt = (int) $args[0]->cnt;
		$dp_name = mysqli_real_escape_string($mysqli, $args[1]->dp_name);
		$result = mysqli_query($mysqli, "CALL getLastNonEfficient($cnt, '$dp_name')");
		
		$additional = array('department_name' => $dp_name);
		outputXML($result, 'summary', 'machine', $additional);
	break;
	
	case 'getMicrosoftMachines':
		$eff_only = (int) $args[0]->eff_only;
		$min_fee = (int) $args[1]->min_fee;
		$result = mysqli_query($mysqli, "CALL getMicrosoftMachines('$min_fee', '$eff_only')");
		
		if($eff_only === 1)
			$additional = array('efficient_only' => '1');
		
		if($min_fee > 0) {
			if(!is_array($additional))
				$additional = array();
			
			$additional['minimum_fee'] = $min_fee;
		}
		
		outputXML($result, 'summary', 'machine', $additional);
	break;
	
	case 'getOwnersAndDescriptions':
		$eff_only = (int) $args[0]->eff_only;
		$min_class = (int) $args[1]->min_class;
		$dp_name = mysqli_real_escape_string($mysqli, $args[2]->dp_name);
		$result = mysqli_query($mysqli, "CALL getOwnersAndDescriptions('$eff_only', '$min_class', '$dp_name')");
		
		$additional = array('department_name' => $dp_name);
		
		if($eff_only === 1)
			$additional['efficient_only'] = '1';
		
		if($min_class > 0) {
			$additional['minimum_class'] = $min_class;
		}
		
		outputXML($result, 'summary', 'person', $additional);
	break;
	
	case 'getOwnersTop50':
		$dt_owned = mysqli_real_escape_string($mysqli, $args[0]->dt_owned);
		if(empty($dt_owned)) {
			$tmp_date = new DateTime();
			$dt_owned = $tmp_date->format('Y-m-d');
		}
		
		$result = mysqli_query($mysqli, "CALL getOwnersTop50('$dt_owned')");
		
		$additional = array('purchased_before' => $dt_owned);
		outputXML($result, 'summary', 'person', $additional);
		
	break;
	
	case 'getTotalCosts':
		$manufacturer = mysqli_real_escape_string($mysqli, $args[0]->mnf);
		$result = mysqli_query($mysqli, "CALL getTotalCosts('$manufacturer')");
		
		outputXML($result, 'summary', 'cost_info');
	break;
	
	default:
	
	break;
}


mysqli_close($mysqli);

?>