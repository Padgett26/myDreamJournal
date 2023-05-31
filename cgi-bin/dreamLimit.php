<?php
if (filter_input ( INPUT_GET, 'text', FILTER_SANITIZE_STRING )) {
	$text = filter_input ( INPUT_GET, 'text', FILTER_SANITIZE_STRING );
	$chrCount = strlen ( $text );
	$chrRemaining = (500 - $chrCount);
	echo $chrRemaining . " characters remaining";
}