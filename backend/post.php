<?php
$data = $_POST['data'];
if (empty($data)) {
    echo "";
}
else{
    echo file_put_contents("data.txt", $data);
}
?>