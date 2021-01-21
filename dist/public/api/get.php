<?php
header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = file_get_contents('../content.html');
http_response_code(200);
echo json_encode($data);
?>