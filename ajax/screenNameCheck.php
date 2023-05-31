<?php
include "../cgi-bin/config.php";
$name = filter_input(INPUT_GET, 'checkName', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);
$check = $db->prepare("SELECT COUNT(*) FROM users WHERE screenName = ? AND id != ?");
$check->execute(array(
    $name,
    $id
));
$checkR = $check->fetch();
$count = $checkR[0];
echo ($count >= 1) ? "<span style='color:red;'>This screen name is already in use</span>" : "<span style='color:green;'>This screen name is available</span>";