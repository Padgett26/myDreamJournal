<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
?>
<!DOCTYPE HTML>
<html manifest="includes/cache.appcache">
<head>
<?php
include "includes/head.php";
?>
</head>
<body>
<div style="text-align:center; padding:20px auto;">
<img src="images/MDJHeader.jpg" style="max-width:40%;" alt="">
</div>
<div style="text-align:center; font-weight:bold; font-size:2em; margin-bottom:30px;">
<?php
echo ($myId >= 1) ? $firstName . "'s " : "My ";
?>
Dream Journal
</div>
<div style="text-align:center;">
<a href="index.php?page=home">Home</a>
<?php
echo " | ";
if ($myId >= 1) {
	?>
<a href="index.php?page=journal&viewJ=new">Dream Journal</a>
<?php
	echo " | ";
	?>
<a href="index.php?page=discussion">Community Discussion</a>
<?php
	echo " | ";
	?>
<span class="link" id="flipFeedback">Add to or edit Dictionary</span>
<?php
	echo " | ";
	?>
<a href="index.php?page=feedback">Feedback</a>
<?php
	echo " | ";
	?>
<a href="index.php?page=settings">Settings</a>
<?php
	echo " | ";
	?>
<a href="index.php?logout=yep">Log Out</a>
<?php
} else {
	?>
<a href="index.php?page=login">Log In / Register</a>
<?php
}
?>
</div>
<?php
if ($myId >= 1) {
	?>
<div id="panelFeedback" style="display:none; text-align:center; margin:20px auto;">
<span style="color:<?php
	echo $highlightColor;
	?>">Is there a dream symbol you think should be added or edited?</span><br>
<form action="index.php?page=journal&viewJ=review" method="post"><input type="text" name="symbol" value="" size="20" placeholder="Symbol"><br><textarea name="notes" cols="60" rows="6" placeholder="Notes"></textarea><br><input type="submit" value=" Submit "><input type="hidden" name="dictFeedback" value="1"></form>
</div>
<?php
}
?>
<div style="padding:30px 10px 10px 10px;">
<?php
include "pages/$page.php";
?>
</div>
<footer>
<?php
include "../familyLinks.php";
?>
</footer>
<script type="module">

  // Import the functions you need from the SDKs you need

  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.6/firebase-app.js";

  // TODO: Add SDKs for Firebase products that you want to use

  // https://firebase.google.com/docs/web/setup#available-libraries


  // Your web app's Firebase configuration

  const firebaseConfig = {

    apiKey: "AIzaSyD7h49MhCR3vnq9cphFwR61Ece31HxsKjQ",

    authDomain: "mylocallife-2fd55.firebaseapp.com",

    databaseURL: "https://mylocallife-2fd55.firebaseio.com",

    projectId: "mylocallife-2fd55",

    storageBucket: "mylocallife-2fd55.appspot.com",

    messagingSenderId: "1025294629138",

    appId: "1:1025294629138:web:ff06aef54a66571ec8dbcb"

  };


  // Initialize Firebase

  const app = initializeApp(firebaseConfig);

</script>
</body>
</html>