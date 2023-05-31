My Dream Journal is designed to be a simple means of recording your dreams and analyzing the symbolism of the imagry.  We have loaded an extensive dream symbolism dictionary, and with your input, the dictionary expands constantly.<br><br>
You can discuss your dreams with other members of the My Dream Journal community by starting or participating in the conversations on the Community Discussion page.<br><br>
Changing the site color scheme or theme customizes the site to fit your personality.<br><br>
<?php
if ($myId >= 1) {
	?>
The <a href="index.php?page=journal&viewJ=new">Dream Journal</a> page is where you can enter and analyze your dreams.<br>
Under the 'Record Your Dream' tab, you can select the date of your dream, enter a title if you wish, write down your dream, and add a couple of pictures if you have images that go with you dream.<br>
Under the 'Interpret Your Dream' tab you will find your entered dreams, sorted by date. In your write up, key words will be highlighted, and when you click on them you will be shown the meaning behind those images. You are also given space to write your own interpretation.<br>
Under the 'Review Your dream' tab you can read through your dreams and interpretations.<br><br>
The <a href="index.php?page=discussion">Community Discussion</a> page is where you open, or take part in discussions with the other users of My Dream Journal.<br><br>
The Dictionary Feedback link gives you a way of letting us know if you have a new dream symbol and definition you would like to be considered for addition to the dictionary. Or if you find a definition you want to update, you can let us know.<br><br>
The <a href="index.php?page=settings">Settings</a> page is where you can donate to this website so we can keep it running, change your personal information, and personalize the look of the site by changing the color scheme, or picking a theme.<br>
On the settings page you can also create a back up file of your entered dreams and personal interpretations, which can be viewed as a text file, or downloaded for your personal storage.<br><br>
Please let us know through the <a href="index.php?page=feedback">Feedback</a> page if there is anything we can do to improve your experience on My Dream Journal.<br><br>
<?php
}
?>
We hope you find this Dream Journal useful and easy to use.<br><br>
Current sources used to build the Dream Dictionary:<br>
<?php
$getS = $db->prepare ( "SELECT * FROM sources ORDER BY source" );
$getS->execute ();
while ( $getSR = $getS->fetch () ) {
	$s = html_entity_decode ( $getSR ['source'], ENT_QUOTES );
	$w = html_entity_decode ( $getSR ['webAddress'], ENT_QUOTES );
	$c = html_entity_decode ( $getSR ['citation'], ENT_QUOTES );
	echo $s . ";";
	echo ($w != "" && $w != " ") ? " <a href='$w' target='_blank'>$w</a>;" : "";
	echo " " . $c . "<br>";
}
?>
<br><br>