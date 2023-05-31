<?php
if ($myId == 1) {

	if (filter_input ( INPUT_GET, 'grab', FILTER_SANITIZE_STRING )) {
		$grab = filter_input ( INPUT_GET, 'grab', FILTER_SANITIZE_STRING );
	} else {
		$grab = 1;
	}
	if (filter_input ( INPUT_POST, 'up', FILTER_SANITIZE_STRING )) {
		$up = filter_input ( INPUT_POST, 'up', FILTER_SANITIZE_STRING );
		$source = filter_input ( INPUT_POST, 'source', FILTER_SANITIZE_NUMBER_INT );
		$delDef = filter_input ( INPUT_POST, 'delDef', FILTER_SANITIZE_NUMBER_INT );
		if ($delDef >= 1) {
			$del = $db->prepare ( "DELETE FROM symbolDictionary WHERE id = ?" );
			$del->execute ( array (
					$delDef
			) );
		} else {
			$symbol = htmlentities ( filter_input ( INPUT_POST, 'symbol', FILTER_SANITIZE_STRING ), ENT_QUOTES );
			$definition = htmlentities ( filter_input ( INPUT_POST, 'definition', FILTER_SANITIZE_STRING ), ENT_QUOTES );
			if ($symbol != "" && $symbol != " " && $definition != "" && $definition != " ") {
				if ($up == 'new') {
					$put1 = $db->prepare ( "INSERT INTO symbolDictionary VALUES(NULL,?,?,?,'0','0')" );
					$put1->execute ( array (
							$symbol,
							$definition,
							$source
					) );
				} else {
					$put = $db->prepare ( "UPDATE symbolDictionary SET symbol = ?, definition = ?, source = ? WHERE id = ?" );
					$put->execute ( array (
							$symbol,
							$definition,
							$source,
							$up
					) );
				}
			}
		}
	}
	$list = array ();
	$get = $db->prepare ( "SELECT id, symbol FROM symbolDictionary ORDER BY symbol" );
	$get->execute ();
	while ( $getR = $get->fetch () ) {
		$list [$getR ['id']] = $getR ['symbol'];
	}
	?>
	<table>
	<tr>
	<td>
	<?php
	echo "<a href='index.php?page=defCorrect&grab=new'>New</a><br>\n";
	foreach ( $list as $k => $v ) {
		echo "<a href='index.php?page=defCorrect&grab=$k'>$v</a><br>\n";
	}
	?>
	</td>
	<td>
	<?php
	$getS = $db->prepare ( "SELECT symbol, definition, source FROM symbolDictionary WHERE id = ?" );
	$getS->execute ( array (
			$grab
	) );
	$getSR = $getS->fetch ();
	$s = html_entity_decode ( $getSR ['symbol'], ENT_QUOTES );
	$d = html_entity_decode ( $getSR ['definition'], ENT_QUOTES );
	$source = $getSR ['source'];
	$getSource = $db->prepare ( "SELECT id, source FROM sources WHERE id = ?" );
	$getSource->execute ( array (
			$source
	) );
	$getSourceR = $getSource->fetch ();
	$si = $getSourceR ['id'];
	$ss = $getSourceR ['source'];

	echo "<form action='index.php?page=defCorrect&grab=" . ($grab + 1) . "' method='post'>\n";
	echo "$s<br><br>" . nl2br ( $d ) . "<br><br>";
	echo "<input type='text' name='symbol' value='$s'><br>\n";
	echo "<textarea cols='80' rows='20' name='definition'>$d</textarea><br>\n";
	echo "Source: <select name='source' size='1'>\n";
	$getSources = $db->prepare ( "SELECT id, source FROM sources" );
	$getSources->execute ();
	while ( $getSourcesR = $getSources->fetch () ) {
		$sId = $getSourcesR ['id'];
		$sSource = $getSourcesR ['source'];
		echo "<option value='$sId'";
		echo ($sId == $si) ? " selected" : "";
		echo ">$sSource</option>\n";
	}
	echo "</select><br>\n";
	echo "Delete this definition: <input type='checkbox' name='delDef' value='$grab'><br><br>\n";
	echo "<input type='hidden' name='up' value='$grab'><input type='submit' value=' Go '>\n";
	echo "</form>\n";
	?>
	</td>
	</tr>
	</table>
	<?php
}