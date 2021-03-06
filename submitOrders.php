<?php

$data = file_get_contents('php://input');
$application = getenv("VCAP_APPLICATION");
$application_json = json_decode($application, true);
$applicationName = $application_json["name"];
if (substr($applicationName, -3) === "-ui") { // if suffixed with "-ui", remove trailing "-ui"
    $ordersAppName = substr($applicationName, 0, -3)  . "-orders-api";
} else {
    $ordersAppName = $applicationName . "-orders-api";
}
$applicationURI = $application_json["application_uris"][0];
$ordersHost = substr_replace($applicationURI, $ordersAppName, 0, strlen($applicationName));
$ordersRoute = "http://" . $ordersHost;
$ordersURL = $ordersRoute . "/rest/orders";

function httpPost($data,$url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, true);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$output = curl_exec ($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close ($ch);
	return $code;
}

echo json_encode(array("httpCode" => httpPost($data,$ordersURL), "ordersURL" => $ordersURL));

?>
