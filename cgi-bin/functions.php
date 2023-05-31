<?php
function createJournal($table, $db) {
	$journal = $db->prepare ( "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
date INT(12) UNSIGNED DEFAULT 0,
title VARCHAR(100),
dream TEXT,
interpretation TEXT,
pic1 VARCHAR(18),
pic2 VARCHAR(18),
notUsed1 INT(2) DEFAULT 0,
notUsed2 INT(2) DEFAULT 0
)" );
	$journal->execute ();
	return true;
}
function sendVerificationEmail($toId, $firstName, $email, $verifyCode) {
	$link = hash ( 'sha512', ($verifyCode . $firstName . $email), FALSE );
	$mess = "$firstName,\n\n
        As a layer of security, we ask that you verify your email address before being allowed to post on the My Dream Journal webpage.  The easiest way to do this is to click on the link below, this will update your status on the webpage.  If clicking on the link doesn't work, usually because html isn't enabled in your email client, you can also highlight the link below, copy it (ctrl + c), and then paste it (ctrl + v) in the address field of your web browser, and then hit enter.\n\n
        https://mydreamjournal.net/index.php?page=login&id=$toId&ver=$link\n\n
        Thank you,\nAdmin\nMy Dream Journal";
	$headers [] = "MIME-Version: 1.0";
	$headers [] = 'Content-Type: text/plain; charset=utf-8';
	$headers [] = "To: $firstName <$email>";
	$headers [] = "From: My Dream Journal Admin <admin@mydreamjournal.net>";
	mail ( $email, 'Please verify your email address to access My Dream Journal', $mess, implode ( "\r\n", $headers ) );
}
function sendPWResetEmail($toId, $firstName, $email, $verifyCode) {
	$link = hash ( 'sha512', ($verifyCode . $firstName . $email), FALSE );
	$mess = "$firstName,\n\n
        There has been a request on the My Dream Journal website for a password reset for this account.  If you initiated this request, click the link below, and you will be sent to a page where you will be able enter a new password. If you did not initiate this password reset request, simple ignore this email, and your password will not be changed.\n\n
        https://mydreamjournal.net/index.php?page=PWReset&id=$toId&ver=$link\n\n
        Thank you,\nAdmin\nMy Dream Journal";
	$headers [] = "MIME-Version: 1.0";
	$headers [] = "Content-Type: text/plain; charset=utf-8";
	$headers [] = "To: $firstName <$email>";
	$headers [] = "From: My Dream Journal Admin <admin@mydreamjournal.net>";
	mail ( $email, 'My Dream Journal password reset request', $mess, implode ( "\r\n", $headers ) );
}
function sendReferalEmail($rMsg, $email, $firstName, $lastName) {
	$mess = "$firstName $lastName sent you a referal for the My Dream Journal website.\n\n
        $rMsg\n\n
		My Dream Journal is designed to be a simple means of recording your dreams and analyzing the symbolism of the imagry.  We have loaded an extensive dream symbolism dictionary, and with your input, the dictionary expands constantly.\n\n
		You can discuss your dreams with other members of the My Dream Journal community by starting or participating in the conversations on the Community Discussion page.\n\n
		Using the free version of the website gives you access to the dream journal where you can record, review, and download your dreams.\n\n
		When you purchase a subscription to use the My Dream Journal website ($25 /year), both you, and the person who referred you, will receive one month free subscription.\n\n
        https://mydreamjournal.net\n\n
        Thank you,\nAdmin\nMy Dream Journal";
	$headers [] = "MIME-Version: 1.0";
	$headers [] = "Content-Type: text/plain; charset=utf-8";
	$headers [] = "To: $firstName <$email>";
	$headers [] = "From: My Dream Journal Admin <admin@mydreamjournal.net>";
	mail ( $email, "$firstName sent a referal for My Dream Journal", $mess, implode ( "\r\n", $headers ) );
}
function getPicType($imageType) {
	switch ($imageType) {
		case "image/gif" :
			$picExt = "gif";
			break;
		case "image/jpeg" :
			$picExt = "jpg";
			break;
		case "image/pjpeg" :
			$picExt = "jpg";
			break;
		case "image/png" :
			$picExt = "png";
			break;
		default :
			$picExt = "xxx";
			break;
	}
	return $picExt;
}
function processPic($userId, $imageName, $tmpFile) {
	$folder = "cmPics/$userId";
	if (! is_dir ( "$folder" )) {
		mkdir ( "$folder", 0777, true );
	}

	$saveto = "$folder/$imageName";

	list ( $width, $height ) = (getimagesize ( $tmpFile ) != null) ? getimagesize ( $tmpFile ) : null;
	if ($width != null && $height != null) {
		$image = new Imagick ( $tmpFile );
		$image->thumbnailImage ( 800, 800, true );
		$image->writeImage ( $saveto );
	}
}
function processThumbPic($userId, $imageName, $tmpFile) {
	$folder = "cmPics/$userId/thumb";
	if (! is_dir ( "$folder" )) {
		mkdir ( "$folder", 0777, true );
	}

	$saveto = "$folder/$imageName";

	list ( $width, $height ) = (getimagesize ( $tmpFile ) != null) ? getimagesize ( $tmpFile ) : null;
	if ($width != null && $height != null) {
		$image = new Imagick ( $tmpFile );
		$image->thumbnailImage ( 150, 150, true );
		$image->writeImage ( $saveto );
	}
}
function make_links_clickable($text, $highlightColor) {
	return preg_replace ( '!(((f|ht)tp(s)?://)[-a-zA-Z()0-9@:%_+.~#?&;//=]+)!i', "<a href='$1' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$1</a>", $text );
}

if (! function_exists ( 'str_contains' )) {
	function str_contains($haystack, $needle) {
		return ($needle !== '' && mb_strpos ( $haystack, $needle ) !== false) ? true : false;
	}
}
function money($amt) {
	settype ( $amt, "float" );
	$fmt = new NumberFormatter ( 'en_US', NumberFormatter::CURRENCY );
	return $fmt->formatCurrency ( $amt, "USD" );
}