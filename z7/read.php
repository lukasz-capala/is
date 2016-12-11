<?php
function arrayToXML($output, &$xml, $group = 'item') {
    foreach($output as $key => $value) {
        if(is_numeric($key)){
            $key = $group;
        }
      if(is_array($value)) {
            $subnode = $xml->addChild($key);
            arrayToXML($value, $subnode);
        }  else {
            $xml->addChild($key, $value);
        }
     }
}

function outputData($result, $root, $group = 'item', $additional = null, $format = 'xml', $theme = 'default') {
	$output = Array();
	$i = 0;
		while($row = mysqli_fetch_assoc($result)) {
			$output[]= $row;
			$i++;
		}
		
	if(!is_null($additional))
		$output =  array_merge_recursive($additional, $output);
	
	switch($format) {
		case "xml":
			$root = '<?xml version="1.0"?><?xml-stylesheet type="text/css" href="xmls.css"?><'.$root.'></'.$root.'>';
			$xml = new SimpleXMLElement($root);
			arrayToXML($output, $xml, $group);
			 
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			$dom->save("results.xml");

			file_put_contents("theme.css", file_get_contents("css/".$theme.".css"));
			echo json_encode("output_xml.php");
		break;
		
		case "json":
			file_put_contents("results.json", json_encode($output, JSON_PRETTY_PRINT));
			
			echo json_encode("output_json.php");
		break;
		
		case "yaml":
			yaml_emit_file("results.yaml", $output);
			
			echo json_encode("output_yaml.php");
		break;
	}

}

$action = $_GET['action'];
$mysqli = mysqli_connect("localhost", "root", "", "gfxcompl_IS");
if (mysqli_connect_errno()) {
    echo "nie poszlo";
    exit();
}

$request_date = new DateTime();
$user_data = $request_date->format('Y-m-d')."\n";
$user_data .= 'Method: '.$_SERVER['REQUEST_METHOD']."\n";
$user_data .= 'Time: '.$_SERVER['REQUEST_TIME']."\n";
$user_data .= 'Query string: '.$_SERVER['QUERY_STRING']."\n";
$user_data .= 'User-agent: '.$_SERVER['HTTP_USER_AGENT']."\n";
$user_data .= 'IP: '.$_SERVER['REMOTE_ADDR']."\n";
/*** Uncomment below line when publish ***/
//$user_data .= 'Hostname: '.$_SERVER['REMOTE_HOST']."\n";
$user_data .= 'Port: '.$_SERVER['REMOTE_PORT']."\n";
$user_data .= 'File-name: '.$_SERVER['SCRIPT_FILENAME']."\n";
$user_data .= 'Request from: '.$_SERVER['REQUEST_URI'];
$user_data = mysqli_real_escape_string($mysqli, $user_data);

// No direct access allowed
if($_SERVER['REQUEST_METHOD'] == "GET") {
	session_start();

	if (!isset($_SESSION['init'])) {
		session_regenerate_id();
		$_SESSION['init'] = true;
		$_SESSION['counter'] = 0;
		$_SESSION['machine'] = $_SERVER['REMOTE_ADDR'];
	}

	if($_SESSION['machine'] != $_SERVER['REMOTE_ADDR']) {
		session_destroy();
		die('hacking_attempt');	
	}
	
	$_SESSION['counter']++;
	if($_SESSION['counter'] > 3) {
		 mysqli_query($mysqli, "CALL Log('$user_data', 'Attempt for direct Access')");
	}
	
	header("Location: index.html");
}
else {
	// reCAPTHA in action
	$secret = "6Le-3QsUAAAAAKxUoPIonPlKr9Ceou3F5-awKZ4l";
	$post = file_get_contents("php://input");
	$args = json_decode($post);
	
	$captcha_verify = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$args->{'g-recaptcha-response'}."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);
	
	if(!isset($args->format)) {
		$format = "xml";
	} else { $format = $args->format; }		
	
	// Proceed to data download
	$additional = null;
	if ($captcha_verify["success"] != false) {
		$theme = mysqli_real_escape_string($mysqli, $args->theme);
		if(empty($theme))
			$theme = "default";
		
		switch($action) {
			case 'countMachinesByClass':
				$manufacturer = mysqli_real_escape_string($mysqli, $args->mnf);
				$result = mysqli_query($mysqli, "CALL countMachinesByClass('$manufacturer', '$user_data')");
				
				if(!empty($manufacturer))
					$additional = array('manufacturer' => $manufacturer);
				outputData($result, 'summary', 'class', $additional, $format, $theme);
			break;
			
			case 'countMachinesByManufacturer':
				$year = (int) $args->year;
				$result = mysqli_query($mysqli, "CALL countMachinesByManufacturer($year, '$user_data')");
				
				$additional = array('year' => $year);
				outputData($result, 'summary', 'manuf', $additional, $format, $theme);
			break;
			
			case 'get10MostExpensive':
				$eff_only = (int) $args->eff_only;
				$result = mysqli_query($mysqli, "CALL get10MostExpensive($eff_only, '$user_data')");
				
				if($eff_only === 1)
					$additional = array('efficient_only' => '1');
				
				outputData($result, 'summary', 'machine', $additional, $format, $theme);
			break;
			
			case 'getAverageCost':
				$manufacturer = mysqli_real_escape_string($mysqli, $args->mnf);
				$result = mysqli_query($mysqli, "CALL getAverageCost('$manufacturer', '$user_data')");
				
				outputData($result, 'summary', 'cost_info', null, $format, $theme);
			break;
			
			case 'getLastIssues':
				$date_from = mysqli_real_escape_string($mysqli, $args->date_from);
				$date_to = mysqli_real_escape_string($mysqli, $args->date_to);
				if(empty($date_from)) {
					$tmp_date = new DateTime('2000-01-01');
					$date_from = $tmp_date->format('Y-m-d');
				}
				
				if(empty($date_to)) {
					$tmp_date = new DateTime();
					$date_to = $tmp_date->format('Y-m-d');
				}
				
				$result = mysqli_query($mysqli, "CALL getLastIssues('$date_from', '$date_to', '$user_data')");
				
				$additional = array('date_from' => $date_from, 'date_to' => $date_to);
				outputData($result, 'summary', 'issue', $additional, $format, $theme);
			break;
			
			case 'getLastNonEfficient':
				$cnt = (int) $args->cnt;
				$dp_name = mysqli_real_escape_string($mysqli, $args->dp_name);
				$result = mysqli_query($mysqli, "CALL getLastNonEfficient($cnt, '$dp_name', '$user_data')");
				
				$additional = array('department_name' => $dp_name);
				outputData($result, 'summary', 'machine', $additional, $format, $theme);
			break;
			
			case 'getMicrosoftMachines':
				$eff_only = (int) $args->eff_only;
				$min_fee = (int) $args->min_fee;
				$result = mysqli_query($mysqli, "CALL getMicrosoftMachines('$min_fee', '$eff_only', '$user_data')");
				
				if($eff_only === 1)
					$additional = array('efficient_only' => '1');
				
				if($min_fee > 0) {
					if(!is_array($additional))
						$additional = array();
					
					$additional['minimum_fee'] = $min_fee;
				}
				
				outputData($result, 'summary', 'machine', $additional, $format, $theme);
			break;
			
			case 'getOwnersAndDescriptions':
				$eff_only = (int) $args->eff_only;
				$min_class = (int) $args->min_class;
				$dp_name = mysqli_real_escape_string($mysqli, $args->dp_name);
				$result = mysqli_query($mysqli, "CALL getOwnersAndDescriptions('$eff_only', '$min_class', '$dp_name', '$user_data')");
				
				$additional = array('department_name' => $dp_name);
				
				if($eff_only === 1)
					$additional['efficient_only'] = '1';
				
				if($min_class > 0) {
					$additional['minimum_class'] = $min_class;
				}
				
				outputData($result, 'summary', 'person', $additional, $format, $theme);
			break;
			
			case 'getOwnersTop50':
				$dt_owned = mysqli_real_escape_string($mysqli, $args->dt_owned);
				if(empty($dt_owned)) {
					$tmp_date = new DateTime();
					$dt_owned = $tmp_date->format('Y-m-d');
				}
				
				$result = mysqli_query($mysqli, "CALL getOwnersTop50('$dt_owned', '$user_data')");
				
				$additional = array('purchased_before' => $dt_owned);
				outputData($result, 'summary', 'person', $additional, $format, $theme);
				
			break;
			
			case 'getTotalCosts':
				$manufacturer = mysqli_real_escape_string($mysqli, $args->mnf);
				$result = mysqli_query($mysqli, "CALL getTotalCosts('$manufacturer', '$user_data')");
				
				outputData($result, 'summary', 'cost_info', null, $format, $theme);
			break;
			
			default:
				
			break;
		}
	} else {
		mysqli_query($mysqli, "CALL Log('$user_data', 'Attempt to access outside of index.html')");
		echo "0";
	}
}

mysqli_close($mysqli);

?>