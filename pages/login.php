<div style="padding:10px;">
    <?php
				$msg = "";
				$errorMsg = "";
				$name = "";
				$newEmail = "";
				if (filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING )) {
					$ver = filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING );
					$rId = filter_input ( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
					$stmt = $db->prepare ( "SELECT firstName, email, verifyCode FROM users WHERE id=?" );
					$stmt->execute ( array (
							$rId
					) );
					$row = $stmt->fetch ();
					$name = $row ['firstName'];
					$verifyCode = $row ['verifyCode'];
					$email = $row ['email'];
					$link = hash ( 'sha512', ($verifyCode . $name . $email), FALSE );
					if ($ver == $link) {
						$stmt2 = $db->prepare ( "UPDATE users SET verifyCode=?, accessLevel = ? WHERE id=?" );
						$stmt2->execute ( array (
								'0',
								"1",
								$rId
						) );
						$table = $rId . "Journal";
						createJournal ( $table, $db );
						if (! is_dir ( "cmPics/$rId" )) {
							mkdir ( "cmPics/$rId", 0777, true );
						}
						if (! is_dir ( "cmPics/$rId/thumb" )) {
							mkdir ( "cmPics/$rId/thumb", 0777, true );
						}
						if (! is_dir ( "cmPics/$rId/backups" )) {
							mkdir ( "cmPics/$rId/backups", 0777, true );
						}
						$msg = "Thank you for verifying your email address.<br /><br />Please sign in above.<br /><br />You can now use the site.";
					} else {
						$msg = "The link you followed in the verification email is no longer valid.  Please try again.";
					}
				}

				// My Information processing
				if (filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_STRING ) == "new") {
					$myInfoUp = filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_STRING );
					$firstName = htmlEntities ( filter_input ( INPUT_POST, 'firstName', FILTER_SANITIZE_STRING ), ENT_QUOTES );
					$lastName = htmlEntities ( filter_input ( INPUT_POST, 'lastName', FILTER_SANITIZE_STRING ), ENT_QUOTES );
					$screenName = htmlEntities ( filter_input ( INPUT_POST, 'screenName', FILTER_SANITIZE_STRING ), ENT_QUOTES );
					$pwd1 = filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING );
					$pwd2 = filter_input ( INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING );

					if (filter_input ( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL )) {
						$newEmail = strtolower ( filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL ) );
						$stmt = $db->prepare ( "SELECT COUNT(*) FROM users WHERE email=? && accessLevel >= ?" );
						$stmt->execute ( array (
								$newEmail,
								"1"
						) );
						$row = $stmt->fetch ();
						$email = ($row [0] >= 1) ? '0' : $newEmail;

						$check = $db->prepare ( "SELECT COUNT(*) FROM users WHERE screenName = ?" );
						$check->execute ( array (
								$screenName
						) );
						$checkR = $check->fetch ();
						$count = $checkR [0];

						if ($email == '0') {
							$errorMsg = "The email you entered seems to already be in use.";
						} elseif ($count >= 1) {
							$errorMsg = "The screen name you entered seems to already be in use.";
						} else {
							$cleanStmt = $db->prepare ( "DELETE FROM users WHERE email = ?" );
							$cleanStmt->execute ( array (
									$email
							) );

							if ($pwd1 != "" && $pwd1 != " " && $pwd1 === $pwd2) {
								$salt = mt_rand ( 100000, 999999 );
								$hidepwd = hash ( 'sha512', ($salt . $pwd1), FALSE );
								$stmt = $db->prepare ( "INSERT INTO users VALUES" . "(NULL, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?, 'default', '#ffffff', '#000000', '#bd4a11', '#bd4a11', ?, '0', '0')" );
								$stmt->execute ( array (
										$firstName,
										$lastName,
										$screenName,
										$hidepwd,
										$salt,
										$email,
										$time,
										$time,
										$time,
										$time
								) );
								$stmt2 = $db->prepare ( "SELECT id FROM users WHERE email=? && password=? ORDER BY id DESC LIMIT 1" );
								$stmt2->execute ( array (
										$email,
										$hidepwd
								) );
								$row2 = $stmt2->fetch ();
								$myInfoUp = $row2 ['id'];
								sendVerificationEmail ( $myInfoUp, $firstName, $email, $time );
								$msg = "A verification email has been sent to the address you provided - $email<br /><br />In it is a link for you to click on, this will verify your email address, and will allow you to use this site.";
							} else {
								$errorMsg = "There was either no password entered, or your passwords did not match.";
							}
						}
					} else {
						$errorMsg = "Please enter a valid email address.";
					}
				}
				?>
				<div style='text-align:center; background-color:<?php
				echo $backgroundColor;
				?>; color:<?php
				echo $textColor;
				?>; width:100%; padding:20px 0px;'>
    <form method='post' action='index.php'>
    <span style="font-size:1.25em;">Sign&nbsp;In</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span style='margin-top:20px;'>Email</span>&nbsp;&nbsp;
    <input name='email' value='' type='email' autocomplete='on' placeholder='Email' required style='margin-left:10px;' />&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
    <span style='margin-top:20px;'>Password</span>&nbsp;&nbsp;
    <input name='pwd' value='' type='password' placeholder='Password' required style='margin-left:10px; margin-top:10px;' />&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
    <input type='hidden' name='login' value='1' />
    <input type='submit' style='margin-top:10px;' value=' Sign in ' />
    </form></div><br /><br />
     <?php
					if ($msg != "") {
						echo "<div style='text-align:center; font-weight:bold; font-size:1em; margin-top:20px; color:$textColor;'>$msg</div>";
					}
					if ($errorMsg != "") {
						echo "<div style='text-align:center; font-weight:bold; font-size:1em; margin-top:20px; color:$textColor;'>$errorMsg</div>";
					}
					?>

    <div style="text-align:center;"><a href='index.php?page=PWReset'>Forgot your password?</a>&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
    <span class="flip" style="color: <?php
				echo $linkColor;
				?>; cursor:pointer;">Register</span>
    <div class="panel" style="display:none; text-align:center; padding-top:20px;">
    <header>
        Register for access
    </header>
    <article style="">
                <form action="index.php?page=login" method="post">
                    <table cellspacing='5px' style="margin:0px auto;">
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">First Name</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="firstName" value="<?php
																												echo $firstName;
																												?>" maxlength="50" size="30" required /></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Last Name</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="lastName" value="<?php
																												echo $lastName;
																												?>" maxlength="50" size="30" required /></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Screen Name (Name displayed in the community discussion)</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="screenName" value="<?php
																												echo $screenName;
																												?>" maxlength="50" size="30" required onkeyup="screenNameCheck('validScreenName', 0, this.value)" /><br><div id="validScreenName" style="font-size:.75em;"></div></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Email (used as your log in)</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="email" name="email" value="<?php
																												echo $newEmail;
																												?>" maxlength="50" size="30" style="" required /><br /></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Password</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="password" name="pwd1" value="" maxlength="50" style="" size="30" required /> Enter once<br /><br /><input type="password" name="pwd2" value="" maxlength="50" style="" size="30" required />and enter again</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;" colspan="2"><div style="text-align:center;"><input type="hidden" name="myInfoUp" value="new" /><input type="submit" value=" Save " /></div></td>
                        </tr>
                    </table>
                </form>
    </article>
    </div>
        </div>