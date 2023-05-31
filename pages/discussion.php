<?php
if ($myId >= 1) {
	if (filter_input ( INPUT_POST, 'entry', FILTER_SANITIZE_STRING )) {
		$eId = (filter_input ( INPUT_POST, 'entry', FILTER_SANITIZE_STRING ) == "new") ? '0' : filter_input ( INPUT_POST, 'entry', FILTER_SANITIZE_NUMBER_INT );
		$header = htmlentities ( filter_input ( INPUT_POST, 'header', FILTER_SANITIZE_STRING ), ENT_QUOTES );
		$text = htmlentities ( filter_input ( INPUT_POST, 'text', FILTER_SANITIZE_STRING ), ENT_QUOTES );

		$up = $db->prepare ( "INSERT INTO discussion VALUES(NULL,?,?,?,?,?,'0','0')" );
		$up->execute ( array (
				$eId,
				$myId,
				$time,
				$header,
				$text
		) );
	}
	echo "<div class='flip' style='color:$linkColor; font-weight:bold; font-weight:1.25em; margin-bottom:10px; cursor:pointer;'>Start a new thread</div>\n";
	echo "<div class='panel' style='margin:10px; border:1px solid $highlightColor; padding:10px; display:none;'><form action='index.php?page=discussion' method='post'>\n";
	echo "<span style='font-weight:bold;'>Header</span><br>\n";
	echo "<input type='text' maxlength='100' size='40' name='header' value=''><br><br>\n";
	echo "<span style='font-weight:bold;'>Text</span><br>\n";
	echo "<textarea name='text' style='width:75%; height:20px;'></textarea><br><br>\n";
	echo "<input type='hidden' name='entry' value='new'><input type='submit' value=' Submit '><br>\n";
	echo "</form></div>\n";

	$get1 = $db->prepare ( "SELECT * FROM discussion WHERE replyTo = ? ORDER BY date DESC" );
	$get1->execute ( array (
			'0'
	) );
	while ( $get1R = $get1->fetch () ) {
		$id1 = $get1R ['id'];
		$a1 = $get1R ['author'];
		$date1 = date ( "Y-m-d", $get1R ['date'] );
		$header1 = html_entity_decode ( $get1R ['header'], ENT_QUOTES );
		$text1 = html_entity_decode ( $get1R ['text'], ENT_QUOTES );
		$getA1 = $db->prepare ( "SELECT screenName FROM users WHERE id = ?" );
		$getA1->execute ( array (
				$a1
		) );
		$getA1R = $getA1->fetch ();
		$author1 = $getA1R ['screenName'];

		echo "<div class='flip' style='padding:10px 0px 10px 10px; color:$linkColor; border:1px solid $highlightColor; cursor:pointer;'><span style='font-weight:bold;'>$header1</span><br>$author1 - $date1</div>\n";
		echo "<div class='panel' style='display:none; padding:10px 0px 10px 40px;'>\n";
		echo "$text1<br>\n";
		echo "Reply:<br>\n";
		echo "<form action='index.php?page=discussion' method='post'><input type='hidden' name='entry' value='$id1'><input type='hidden' name='header' value=''>";
		echo "<textarea name='text' style='width:75%; height:20px;'></textarea><br><input type='submit' value=' Reply '></form>";
		$get2 = $db->prepare ( "SELECT * FROM discussion WHERE replyTo = ? ORDER BY date DESC" );
		$get2->execute ( array (
				$id1
		) );
		while ( $get2R = $get2->fetch () ) {
			$id2 = $get2R ['id'];
			$a2 = $get2R ['author'];
			$date2 = date ( "Y-m-d", $get2R ['date'] );
			$text2 = html_entity_decode ( $get2R ['text'], ENT_QUOTES );
			$getA2 = $db->prepare ( "SELECT screenName FROM users WHERE id = ?" );
			$getA2->execute ( array (
					$a2
			) );
			$getA2R = $getA2->fetch ();
			$author2 = $getA2R ['screenName'];

			echo "<div style='padding:20px 0px 0px 40px; font-size:.75em;'>$author2 - $date2</div>\n";
			echo "<div style='padding:0px 0px 10px 40px;'>\n";
			echo "$text2<br>\n";
			echo "Reply:<br>\n";
			echo "<form action='index.php?page=discussion' method='post'><input type='hidden' name='entry' value='$id2'><input type='hidden' name='header' value=''>";
			echo "<textarea name='text' style='width:75%; height:20px;'></textarea><br><input type='submit' value=' Reply '></form>";
			$get3 = $db->prepare ( "SELECT * FROM discussion WHERE replyTo = ? ORDER BY date DESC" );
			$get3->execute ( array (
					$id2
			) );
			while ( $get3R = $get3->fetch () ) {
				$id3 = $get3R ['id'];
				$a3 = $get3R ['author'];
				$date3 = date ( "Y-m-d", $get3R ['date'] );
				$text3 = html_entity_decode ( $get3R ['text'], ENT_QUOTES );
				$getA3 = $db->prepare ( "SELECT screenName FROM users WHERE id = ?" );
				$getA3->execute ( array (
						$a3
				) );
				$getA3R = $getA3->fetch ();
				$author3 = $getA3R ['screenName'];

				echo "<div style='padding:20px 0px 0px 40px; font-size:.75em;'>$author3 - $date3</div>\n";
				echo "<div style='padding:0px 0px 10px 40px;'>\n";
				echo "$text3<br>\n";
				echo "</div>\n";
			}
			echo "</div>\n";
		}
		echo "</div>";
	}
} else {
	echo "<div style='text-align:center;'>Please <a href='index.php?page=login'>sign in or register</a> to use this page.</div>\n";
}
?>