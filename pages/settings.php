<?php
if ($myId >= 1) {
	if (filter_input ( INPUT_POST, 'sourceUp', FILTER_SANITIZE_STRING )) {
		$sUp = filter_input ( INPUT_POST, 'sourceUp', FILTER_SANITIZE_STRING );
		$sSource = filter_input ( INPUT_POST, 'source', FILTER_SANITIZE_STRING );
		$sWebAddress = filter_input ( INPUT_POST, 'webAddress', FILTER_SANITIZE_STRING );
		$sCitation = filter_input ( INPUT_POST, 'citation', FILTER_SANITIZE_STRING );
		$sDel = (filter_input ( INPUT_POST, 'delSource', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;

		if ($sDel == 1) {
			$del = $db->prepare ( "DELETE FROM sources WHERE id = ?" );
			$del->execute ( array (
					$sDel
			) );
		} else {
			if ($sUp == 'new') {
				$newS = $db->prepare ( "INSERT INTO sources VALUES(NULL,?,?,?,'0','0')" );
				$newS->execute ( array (
						$sSource,
						$sWebAddress,
						$sCitation
				) );
			} else {
				$updateS = $db->prepare ( "UPDATE sources SET source = ?, webAddress = ?, citation = ? WHERE id = ?" );
				$updateS->execute ( array (
						$sSource,
						$sWebAddress,
						$sCitation,
						$sUp
				) );
			}
		}
	}

	if (filter_input ( INPUT_GET, 'setLen', FILTER_SANITIZE_NUMBER_INT ) == '1') {
		$get = $db->prepare ( "SELECT id, symbol FROM symbolDictionary" );
		$get->execute ();
		while ( $getR = $get->fetch () ) {
			$id = $getR ['id'];
			$symbol = html_entity_decode ( $getR ['symbol'], ENT_QUOTES );
			$sLen = strlen ( $symbol );

			$put = $db->prepare ( "UPDATE symbolDictionary SET length = ? WHERE id = ?" );
			$put->execute ( array (
					$sLen,
					$id
			) );
		}
	}

	if (filter_input ( INPUT_POST, 'dfUp', FILTER_SANITIZE_STRING )) {
		$dfUp = filter_input ( INPUT_POST, 'dfUp', FILTER_SANITIZE_STRING );
		$dfDone = (filter_input ( INPUT_POST, 'done', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;
		$dfTic = filter_input ( INPUT_POST, 'dfTic', FILTER_SANITIZE_NUMBER_INT );
		$df = $db->prepare ( "UPDATE dictFeedback SET done = ? WHERE id = ?" );
		$df->execute ( array (
				$dfDone,
				$dfTic
		) );
		if ($dfDone == 1) {
			$dfSymbol = htmlentities ( filter_input ( INPUT_POST, 'symbol', FILTER_SANITIZE_STRING ), ENT_QUOTES );
			$dfDefinition = htmlentities ( filter_input ( INPUT_POST, 'definition', FILTER_SANITIZE_STRING ), ENT_QUOTES );
			$dfSource = filter_input ( INPUT_POST, 'source', FILTER_SANITIZE_NUMBER_INT );
			$len = strlen ( $dfSymbol );
			if ($dfUp == 'new') {
				$start = $db->prepare ( "INSERT INTO symbolDictionary VALUES(NULL, ?, ?, ?, ?, '0')" );
				$start->execute ( array (
						$dfSymbol,
						$dfDefinition,
						$dfSource,
						$len
				) );
			} else {
				$dfUpdate = $db->prepare ( "UPDATE symbolDictionary SET symbol = ?, definition = ?, source = ?, length = ? WHERE id = ?" );
				$dfUpdate->execute ( array (
						$dfSymbol,
						$dfDefinition,
						$dfSource,
						$len,
						$dfUp
				) );
			}
		}
	}

	if (filter_input ( INPUT_POST, 'fUp', FILTER_SANITIZE_NUMBER_INT )) {
		$fUp = filter_input ( INPUT_POST, 'fUp', FILTER_SANITIZE_NUMBER_INT );
		$fDone = filter_input ( INPUT_POST, 'done', FILTER_SANITIZE_NUMBER_INT );
		$f = $db->prepare ( "UPDATE feedback SET done = ? WHERE id = ?" );
		$f->execute ( array (
				$fDone,
				$fUp
		) );
	}

	if (filter_input ( INPUT_POST, 'settingsUp', FILTER_SANITIZE_NUMBER_INT ) == 1) {
		$fn = htmlEntities ( filter_input ( INPUT_POST, 'firstName', FILTER_SANITIZE_STRING ), ENT_QUOTES );
		$ln = htmlEntities ( filter_input ( INPUT_POST, 'lastName', FILTER_SANITIZE_STRING ), ENT_QUOTES );
		$sn = htmlEntities ( filter_input ( INPUT_POST, 'screenName', FILTER_SANITIZE_STRING ), ENT_QUOTES );
		$em = filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$pwd1 = filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING );
		$pwd2 = filter_input ( INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING );

		if ($pwd1 != "" && $pwd1 != " " && $pwd1 === $pwd2) {
			$salt = mt_rand ( 100000, 999999 );
			$hidepwd = hash ( 'sha512', ($salt . $pwd1), FALSE );
			$stmt1 = $db->prepare ( "UPDATE users SET password = ?, salt = ? WHERE id = ?" );
			$stmt1->execute ( array (
					$hidepwd,
					$myId
			) );
		}

		$check = $db->prepare ( "SELECT COUNT(*) FROM users WHERE screenName = ?" );
		$check->execute ( array (
				$sn
		) );
		$checkR = $check->fetch ();
		$count = $checkR [0];
		if ($count == 0) {
			$stmt2 = $db->prepare ( "UPDATE users SET screenName = ? WHERE id = ?" );
			$stmt2->execute ( array (
					$sn,
					$myId
			) );
		}

		$stmt3 = $db->prepare ( "UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE id = ?" );
		$stmt3->execute ( array (
				$fn,
				$ln,
				$em,
				$myId
		) );
	}

	if (filter_input ( INPUT_POST, 'backup', FILTER_SANITIZE_NUMBER_INT ) == 1) {
		$folder = "cmPics/$myId/backups";
		if (! is_dir ( "$folder" )) {
			mkdir ( "$folder", 0777, true );
		}
		$fileName = "MyDreamJournalBackup" . date ( "Y-m-d", $time ) . ".txt";
		$myfile = fopen ( "$folder/$fileName", "w" ) or die ( "Unable to open file!" );
		$table = $myId . "Journal";
		$getJ = $db->prepare ( "SELECT date, title, dream, interpretation FROM $table ORDER BY date" );
		$getJ->execute ();
		while ( $getJR = $getJ->fetch () ) {
			$date = date ( "Y-m-d", $getJR ['date'] );
			$title = html_entity_decode ( $getJR ['title'], ENT_QUOTES );
			$entry = html_entity_decode ( $getJR ['dream'], ENT_QUOTES );
			$interpretation = html_entity_decode ( $getJR ['interpretation'], ENT_QUOTES );
			$txt = "$date\n\n" . "$title\n\n" . "Entry:\n" . "$entry\n\n" . "Interpretation:\n" . "$interpretation\n\n\n";
			fwrite ( $myfile, $txt );
		}
		fclose ( $myfile );
	}

	if (filter_input ( INPUT_POST, 'referUp', FILTER_SANITIZE_NUMBER_INT ) == 1) {
		$rMsg = filter_input ( INPUT_POST, 'screenName', FILTER_SANITIZE_STRING );
		foreach ( $_POST as $key => $val ) {
			if (preg_match ( "/^refer([1-5])$/", $key, $match )) {
				$rEmail = (filter_input ( INPUT_POST, $key, FILTER_VALIDATE_EMAIL )) ? filter_input ( INPUT_POST, $key, FILTER_SANITIZE_EMAIL ) : 'x';
				if ($rEmail != 'x') {
					$rCheck = $db->prepare ( "SELECT COUNT(*) FROM referals WHERE email = ?" );
					$rCheck->execute ( array (
							$rEmail
					) );
					$rCR = $rCheck->fetch ();
					$rCount = $rCR [0];
					if ($rCount == 0) {
						$uCheck = $db->prepare ( "SELECT COUNT(*) FROM users WHERE email = ?" );
						$uCheck->execute ( array (
								$rEmail
						) );
						$uCR = $uCheck->fetch ();
						$uCount = $uCR [0];
						if ($uCount == 0) {
							sendReferalEmail ( $rMsg, $rEmail, $firstName, $lastName );
							$rUp = $db->prepare ( "INSERT INTO referals VALUES(NULL,?,?,?,'0','0','0')" );
							$rUp->execute ( array (
									$rEmail,
									$myId,
									$time
							) );
						}
					}
				}
			}
		}
	}
	?>
	<div style="margin:20px;"><span style="font-weight:bold;">Account 'born on' date</span><br>
	<?php
	echo date ( "l jS \of F Y h:i:s A", $startDate );
	?></div>
	<div style="margin:20px;">
		We hope My Dream Journal is helping you record, analyze, and understand your dreams.<br><br>
		Through your contribution to this site we can continue to make it a free service for those that need it.<br><br>
        <form action="https://www.paypal.com/donate" method="post" target="_top">
<input type="hidden" name="hosted_button_id" value="ARFLYDEMWR9QC" />
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>
</div>
<div style="font-weight:bold; font-size:1.25em; margin:20px; cursor:pointer;" class="flip">Refer a friend</div>
	<div style="margin:20px; display:none;" class="panel" >
	Enter the email addresses of the friends you would like to refer to this website.<br>
	We will send an email to the addresses you enter, letting them know about the site, and that you sent them the referal.<br><br>
	<form action="index.php?page=settings" method="post">
	Personal Msg to add to the email.<br>
	<textarea name='referMsg' style='height:40px; width:75%;'></textarea><br>
	Email 1 <input type="email" name='refer1' value=''><br>
	Email 2 <input type="email" name='refer2' value=''><br>
	Email 3 <input type="email" name='refer3' value=''><br>
	Email 4 <input type="email" name='refer4' value=''><br>
	Email 5 <input type="email" name='refer5' value=''><br>
	<input type="hidden" name='referUp' value='1'><input type="submit" value=' SEND '><br><br>
	</form>
	<div style="font-weight:bold;">Referal history</div>
	<?php
	$getR = $db->prepare ( "SELECT * FROM referals WHERE senderId = ? ORDER BY claimed" );
	$getR->execute ( array (
			$myId
	) );
	while ( $getRR = $getR->fetch () ) {
		$e = $getRR ['email'];
		$s = $getRR ['sentDate'];
		$c = $getRR ['claimed'];
		echo "$e || Sent: " . date ( "Y-m-d", $s ) . " || Claimed: ";
		echo ($c >= 1) ? date ( "Y-m-d", $c ) : "No";
		echo "<br>";
	}
	?>
	</div>
<br>
	</div>
	<div style="text-align:center; font-weight:bold; font-size:1.25em; margin:20px;">My Settings</div>
	<div style="margin:20px;">
	<form action="index.php?page=settings" method="post">
	<div style="font-weight:bold; margin-bottom:20px;">First Name<br><input type="text" name="firstName" value="<?php
	echo $firstName;
	?>" maxlength="50" size="30" required></div>
	<div style="font-weight:bold; margin-bottom:20px;">Last Name<br><input type="text" name="lastName" value="<?php
	echo $lastName;
	?>" maxlength="50" size="30" required></div>
	<div style="font-weight:bold; margin-bottom:20px;">Screen Name (Displayed name in the dream discussion)<br><input type="text" name="screenName" value="<?php
	echo $screenName;
	?>" maxlength="50" size="30" required onkeyup="screenNameCheck('validScreenName', <?php
	echo $myId;
	?>, this.value)"><br><div id="validScreenName" style="font-size:.75em;"></div></div>
	<div style="font-weight:bold; margin-bottom:20px;">Update Log In Info<br><br>Email<br><input type="email" name="email" value="<?php
	echo $email;
	?>" maxlength="100" size="30" required></div>
	<div style="font-weight:bold; margin-bottom:20px;">
	If you wish to change your password, enter it twice here. Else, leave blank.<br>
	<input type="password" name="pwd1" value="" maxlength="50" size="30"><br>
	<input type="password" name="pwd2" value="" maxlength="50" size="30">
	</div>
	<?php
	$getCm2 = $db->prepare ( "SELECT * FROM users WHERE id = ?" );
	$getCm2->execute ( array (
			$myId
	) );
	$getCmR2 = $getCm2->fetch ();
	$DBC = $getCmR ['backgroundColor'];
	$DTC = $getCmR ['textColor'];
	$DHC = $getCmR ['highlightColor'];
	$DLC = $getCmR ['linkColor'];
	?>
	<div style="font-weight:bold; margin-bottom:20px;">Background color<br><input type="color" name="backgroundColor" value="<?php
	echo $DBC;
	?>"></div>
	<div style="font-weight:bold; margin-bottom:20px;">Text color<br><input type="color" name="textColor" value="<?php
	echo $DTC;
	?>"></div>
	<div style="font-weight:bold; margin-bottom:20px;">Highlight color<br><input type="color" name="highlightColor" value="<?php
	echo $DHC;
	?>"></div>
    <div style="font-weight:bold; margin-bottom:20px;">Link color<br><input type="color" name="linkColor" value="<?php
	echo $DLC;
	?>"></div>
	<div style="font-weight:bold; margin-bottom:20px;">Page Theme (Except for default, this may override color selections)<br>
	<select name="theme" size="1">
	<?php
	$getThemes = $db->prepare ( "SELECT name FROM themes ORDER BY RAND()" );
	$getThemes->execute ();
	while ( $getThemesR = $getThemes->fetch () ) {
		$selectThemes = $getThemesR ['name'];
		echo "<option value='$selectThemes'";
		echo ($theme == $selectThemes) ? " selected" : "";
		echo ">$selectThemes</option>\n";
	}
	?>
	</select>
	</div>
	<div style="font-weight:bold; margin:10px 0px 40px 0px;"><input type="hidden" name="settingsUp" value="1"><input type="submit" value=" Save Settings "></div>
	</form>
	<?php
	if ($accessLevel == 2) {
		echo "<div class='flip' style='margin:10px; border:1px solid $highlightColor; padding:10px; color:$textColor; cursor:pointer;'>Edit Sources</div>\n";
		echo "<div class='panel' style='display:none;'>\n";
		echo "<form action='index.php?page=settings' method='post'><input type='text' name='source' value=''><input type='text' name='webAddress' value=''><input type='text' name='citation' value=''><input type='hidden' name='sourceUp' value='new'><input type='hidden' name='delSource' value='0'><input type='submit' value=' Add New '></form>\n";
		$getSources = $db->prepare ( "SELECT * FROM sources" );
		$getSources->execute ();
		while ( $gs = $getSources->fetch () ) {
			$gsId = $gs ['id'];
			$gsSource = $gs ['source'];
			$gsWebAddress = $gs ['webAddress'];
			$gsCitation = $gs ['citation'];
			echo "<form action='index.php?page=settings' method='post'><input type='text' name='source' value='$gsSource'><input type='text' name='webAddress' value='$gsWebAddress'><input type='text' name='citation' value='$gsCitation'><input type='hidden' name='sourceUp' value='$gsId'> Delete? <input type='checkbox' name='delSource' value='1'><input type='submit' value=' Update '></form>\n";
		}
		echo "</div>\n";

		echo "<form id='setLength' action='index.php?page=settings&setLen=1' method='post'><div style='margin:10px; border:1px solid $highlightColor; padding:10px; color:$textColor; cursor:pointer;' onclick='submitForm(\"setLength\")'>Set Symbol Length</div></form>\n";
		echo "<form id='defCorrect' action='index.php?page=defCorrect' method='post'><div style='margin:10px; border:1px solid $highlightColor; padding:10px; color:$textColor; cursor:pointer;' onclick='submitForm(\"defCorrect\")'>Edit Definitions</div></form>\n";
		$dictCount = $db->prepare ( "SELECT COUNT(*) FROM dictFeedback WHERE done = '0'" );
		$dictCount->execute ();
		$dictCountR = $dictCount->fetch ();
		$dCount = $dictCountR [0];
		echo "<div class='flip' style='margin:10px; border:1px solid $highlightColor; padding:10px; color:$textColor; cursor:pointer;'>Dictionary Feedback ($dCount)</div>\n";
		echo "<div class='panel' style='display:none;'>\n";
		$getdf = $db->prepare ( "SELECT * FROM dictFeedback ORDER BY done" );
		$getdf->execute ();
		while ( $df = $getdf->fetch () ) {
			$dfTic = $df ['id'];
			$dfSymbol = html_entity_decode ( $df ['symbol'], ENT_QUOTES );
			$dfNotes = html_entity_decode ( $df ['notes'], ENT_QUOTES );
			$dfDone = $df ['done'];
			$dfFromUser = $df ['fromUser'];
			$getUserd = $db->prepare ( "SELECT firstName, lastName FROM users WHERE id = ?" );
			$getUserd->execute ( array (
					$dfFromUser
			) );
			$getUserdR = $getUserd->fetch ();
			$dfFirstName = $getUserdR ['firstName'];
			$dfLastName = $getUserdR ['lastName'];
			echo "<div style='margin:20px 10px;'>From user: $dfFirstName $dfLastName<br><span style='font-weight:bold;'>$dfSymbol</span><br>" . nl2br ( $dfNotes ) . "</div>";
			$dfId = 'new';
			$sou = 2;
			$check3 = $db->prepare ( "SELECT COUNT(*) FROM symbolDictionary WHERE symbol = ? OR symbol = ?" );
			$check3->execute ( array (
					strtolower ( $dfSymbol ),
					ucfirst ( strtolower ( $dfSymbol ) )
			) );
			$check3R = $check3->fetch ();
			$dfExists = $check3R [0];
			if ($dfExists >= 1) {
				$check = $db->prepare ( "SELECT id, definition, source FROM symbolDictionary WHERE symbol = ? OR symbol = ?" );
				$check->execute ( array (
						strtolower ( $dfSymbol ),
						ucfirst ( strtolower ( $dfSymbol ) )
				) );
				$checkR = $check->fetch ();
				$dfId = $checkR ['id'];
				$def = nl2br ( html_entity_decode ( $checkR ['definition'], ENT_QUOTES ) );
				$sou = $checkR ['source'];
				$check2 = $db->prepare ( "SELECT source FROM sources WHERE id = ?" );
				$check2->execute ( array (
						$sou
				) );
				$check2R = $check2->fetch ();
				$souName = $check2R ['source'];
				echo "<div style='margin:20px 10px;'>From dictionary - source: $souName<br>$def</div>";
			}
			echo "<form action='index.php?page=settings' method='post'>Done: <input type='checkbox' name='done' value='1'";
			echo ($dfDone == 1) ? " checked" : "";
			echo "><br><br>";
			echo "Symbol:<br><input type='text' name='symbol' value='$dfSymbol'><br><br>\n";
			echo "Defintion:<br><textarea name='definition' style='width:97%; height:60px;'>$dfNotes</textarea><br><br>\n";
			echo "Source: <select name='source' size='1'>";
			$getSources = $db->prepare ( "SELECT id, source FROM sources" );
			$getSources->execute ();
			while ( $getSourcesR = $getSources->fetch () ) {
				$sId = $getSourcesR ['id'];
				$sSource = $getSourcesR ['source'];
				echo "<option value='$sId'";
				echo ($sId == $sou) ? " selected" : "";
				echo ">$sSource</option>\n";
			}
			echo "</select><br><br>\n";
			echo "<input type='hidden' name='dfUp' value='$dfId'><input type='hidden' name='dfTic' value='$dfTic'> <input type='submit' value=' Update '></form>\n";
		}
		echo "</div>\n";

		$feedCount = $db->prepare ( "SELECT COUNT(*) FROM feedback WHERE done = '0'" );
		$feedCount->execute ();
		$feedCountR = $feedCount->fetch ();
		$fCount = $feedCountR [0];
		echo "<div class='flip' style='margin:10px; border:1px solid $highlightColor; padding:10px; color:$textColor; cursor:pointer;'>Feedback ($fCount)</div>\n";
		echo "<div class='panel' style='display:none;'>\n";
		$getf = $db->prepare ( "SELECT * FROM feedback ORDER BY done" );
		$getf->execute ();
		while ( $f = $getf->fetch () ) {
			$fId = $f ['id'];
			$fSubject = html_entity_decode ( $f ['subject'], ENT_QUOTES );
			$fMessage = html_entity_decode ( $f ['message'], ENT_QUOTES );
			$fDone = $f ['done'];
			$fFromUser = $f ['userId'];
			$fTimeSent = $f ['timeSent'];
			$getUser = $db->prepare ( "SELECT firstName, lastName, email FROM users WHERE id = ?" );
			$getUser->execute ( array (
					$fFromUser
			) );
			$getUserR = $getUser->fetch ();
			$fFirstName = $getUserR ['firstName'];
			$fLastName = $getUserR ['lastName'];
			$fEmail = $getUserR ['email'];
			echo "<div style='margin:20px 10px;'>" . date ( "Y-m-d", $fTimeSent ) . "<br>From user: $fFirstName $fLastName<br><span style='font-weight:bold;'>$fSubject</span><br>$fMessage<br>Reply: <a href='mailto:$fEmail'>$fEmail</a><br><form action='index.php?page=settings' method='post'>Done: <input type='checkbox' name='done' value='1'";
			echo ($fDone == 1) ? " checked" : "";
			echo "><br><input type='hidden' name='fUp' value='$fId'> <input type='submit' value=' Update '></form></div>\n";
		}
		echo "</div>\n";

		echo "<div class='flip' style='margin:10px; border:1px solid $highlightColor; padding:10px; color:$textColor; cursor:pointer;'>View site as</div>\n";
		echo "<div class='panel' style='display:none;'>\n";
		echo "<div style='margin:20px 10px;'><form action='index.php?page=settings' method='post'>Select User: <select name='grabUser' size='1'><br>";
		$getu = $db->prepare ( "SELECT * FROM users ORDER BY lastName" );
		$getu->execute ();
		while ( $u = $getu->fetch () ) {
			$uId = $u ['id'];
			$ufirstName = $u ['firstName'];
			$ulastName = $u ['lastName'];
			$uemail = $u ['email'];

			echo "<option value='$uId'>$ufirstName $ulastName - $uemail</option>\n";
		}
		echo "</select> <input type='hidden' name='al' value='$accessLevel'><input type='submit' value=' Get User '></form></div>\n";
		echo "</div>\n";
	}
	?>

	<div style="font-weight:bold; margin-bottom:20px;">Create a backup of your journal that you can download <form action="index.php?page=settings" method="post"><input type="hidden" name="backup" value="1"><input type="submit" value=" Create Backup "></form></div>
	<div style="font-weight:bold; margin-bottom:20px;">Available backups<br>
	<?php
	foreach ( new DirectoryIterator ( "cmPics/$myId/backups" ) as $fileInfo ) {
		if ($fileInfo->isDot ())
			continue;
		$f = $fileInfo->getFilename ();
		echo "<a href='cmPics/$myId/backups/$f'>$f</a><br />";
	}
	?>
	</div>
	</div>
	<?php
} else {
	?><div style="text-align:center; font-size:1.25em;">Please log in to view this page</div>
	<?php
}
?>