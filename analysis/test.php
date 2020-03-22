<?php
$request = xmlrpc_encode_request('method', array(1, 2, 3));
$context = stream_context_create(array('http' => array(
	'method' => "POST",
	'header' => "Content-Type: text/xml",
	'content' => $request
)));
$file = file_get_contents("/RPC2", false, $context);
$response = xmlrpc_decode($file);
if ($response && xmlrpc_is_fault($response)) {
	trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
} else {
	print_r($response);
}