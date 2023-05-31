<div id='mainTableBox' style="padding:10px;">
    <?php
				if (NULL !== (filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING )) && filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING ) != "" && filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING ) != " " && filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING ) === filter_input ( INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING )) {
					$pwd1 = filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING );
					$rId = filter_input ( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
					$ver = filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING );
					$stmt = $db->prepare ( "SELECT firstame, email, verifyCode, salt FROM users WHERE id=?" );
					$stmt->execute ( array (
							$rId
					) );
					$row = $stmt->fetch ();
					$name = html_entity_decode ( $row ['firstame'], ENT_QUOTES );
					$email = $row ['email'];
					$verifyCode = $row ['verifyCode'];
					$salt = $row ['salt'];
					$link = hash ( 'sha512', ($verifyCode . $name . $email), FALSE );
					if ($ver == $link) {
						$hidepwd = hash ( 'sha512', ($salt . $pwd1), FALSE );
						$upstmt = $db->prepare ( "UPDATE users SET password=?, verifyCode=? WHERE id=?" );
						$upstmt->execute ( array (
								$hidepwd,
								'0',
								$rId
						) );
						?>
        <header class="heading">Let's get your password reset.</header>
        <article style="font-size: 1.25em; text-align:center;">Okay, we did it.  You can now log in to the site using your new password.  Make sure to make a note of your new password, in case you need it in the future.<br /><br />
        </article>
        <?php
					}
				} else {
					if (filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING )) {
						$rId = filter_input ( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
						$ver = filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING );
						$stmt = $db->prepare ( "SELECT firstName, email, verifyCode FROM users WHERE id=?" );
						$stmt->execute ( array (
								$rId
						) );
						$row = $stmt->fetch ();
						$name = html_entity_decode ( $row ['firstame'], ENT_QUOTES );
						$email = $row ['email'];
						$verifyCode = $row ['verifyCode'];
						$link = hash ( 'sha512', ($verifyCode . $name . $email), FALSE );
						if ($ver == $link) {
							?>
            <header style="font-weight:bold; font-size: 1.5em; text-align:center;">Let's get your password reset.</header>
            <article style="font-size: 1.25em; text-align:center;">Thank you for jumping through those hoops, I know it is a hassle, but it does help to protect your account.<br />
                Please enter your new password below (twice).<br /><br />
                <form action="index.php?page=PWReset&id=<?php

							echo $rId . "&ver=" . $ver;
							?>" method="post">
                    Your new password: <input type="password" name="pwd1" /><br />
                    And again: <input type="password" name="pwd2" /><br />
                    <input type="submit" value=" Go " />
                </form>
            </article>
            <?php
						}
					} else {
						if (filter_input ( INPUT_POST, 'resetEmail', FILTER_VALIDATE_EMAIL )) {
							$resetEmail = filter_input ( INPUT_POST, 'resetEmail', FILTER_SANITIZE_EMAIL );
							$stmt = $db->prepare ( "SELECT COUNT(*), id, firstName FROM users WHERE email=?" );
							$stmt->execute ( array (
									$resetEmail
							) );
							$row = $stmt->fetch ();
							$emailExists = ($row [0] >= 1) ? true : false;
							$toId = $row ['id'];
							$toName = html_entity_decode ( $row ['firstName'], ENT_QUOTES );
							if ($emailExists) {
								sendPWResetEmail ( $toId, $toName, $resetEmail, $time );
								$substmt = $db->prepare ( "UPDATE users SET verifyCode=? WHERE id=?" );
								$substmt->execute ( array (
										$time,
										$toId
								) );
								echo '<header style="font-weight:bold; font-size: 1.5em; text-align:center;">Let\'s get your password reset.</header><article style="font-size: 1.25em; text-align:center;">The email has been sent.  Please check your inbox, and click on the link allowing you to change your password.</article>';
							} else {
								?>
                <header style="font-weight:bold; font-size: 1.5em; text-align:center;">Let's get your password reset.</header>
                <article style="font-size: 1.25em; text-align:center;">I didn't find the email address you entered in the database.<br />
                    Please check your address and re-enter it below.<br /><br />
                    <form action="index.php?page=PWReset" method="post">Your email address: <input type="email" name="resetEmail" max-length="100" required />&nbsp;<input type="submit" value=" Go " /></form>
                </article>
                <?php
							}
						} else {
							?>
            <header style="font-weight:bold; font-size: 1.5em; text-align:center;">Let's get your password reset.</header>
            <article style="font-size: 1.25em; text-align:center;">First, enter your email address below.<br />
                I will check if the email address you enter is associated with an account on this website.<br />
                If it is, I will send you an email which will have a link that leads back to this page, and will allow you to enter a new password.<br />
                This is done to verify that you are who you say you are.  We don't want someone else getting on and changing your password out from under you.<br /><br />
                <form action="index.php?page=PWReset" method="post">Your email address: <input type="email" name="resetEmail" max-length="100" required />&nbsp;<input type="submit" value=" Go " /></form>
            </article>
            <?php
						}
					}
				}
				?>
        </div>