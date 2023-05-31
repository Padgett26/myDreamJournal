<?php
$t = filter_input(INPUT_GET, 'text', FILTER_SANITIZE_STRING);
echo (500 - strlen($t)) . " characters remaining";