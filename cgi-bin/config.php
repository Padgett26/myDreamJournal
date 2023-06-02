<?php
session_start();

include "../globalFunctions.php";

$db = db_mdj();

$debugging = 0; // 1 for debug info showing, 0 for not showing
$beta = 1; // 1 for beta, 0 for complete

date_default_timezone_set('America/Chicago');
$time = time();
$domain = "mydreamjournal.net";

// *** Log out ***
if (filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING) == 'yep') {
    destroySession();
    setcookie("staySignedIn", '', $time - 1209600, "/", $domain, 0);
}

// *** Sign in ***
$loginErr = "x";
if (filter_input(INPUT_POST, 'login', FILTER_SANITIZE_NUMBER_INT) == "1") {
    $email = (filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) ? strtolower(
            filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) : '0';
    $login1stmt = $db->prepare("SELECT id,salt FROM users WHERE email = ?");
    $login1stmt->execute(array(
            $email
    ));
    $login1row = $login1stmt->fetch();
    $salt = ($login1row) ? $login1row['salt'] : 000000;
    $checkId = (isset($login1row['id']) && $login1row['id'] > 0) ? $login1row['id'] : '0';
    $pwd = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);
    $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
    $login2stmt = $db->prepare(
            "SELECT id FROM users WHERE email = ? AND password = ? && accessLevel >= ?");
    $login2stmt->execute(array(
            $email,
            $hidepwd,
            "1"
    ));
    $login2row = $login2stmt->fetch();
    if ($login2row) {
        if ($login2row['id']) {
            $x = $login2row['id'];
            $_SESSION['myId'] = $x;
            setcookie("staySignedIn", $_SESSION['myId'], $time + 1209600, "/",
                    $domain, 0); // set for 14 days
        } else {
            $loginErr = "Your email / password combination isn't correct, or you haven't verified your email address.";
        }
    }
}

// *** User settings ***
$myId = (isset($_SESSION['myId']) && ($_SESSION['myId'] >= '1')) ? $_SESSION['myId'] : '0'; // are
                                                                                            // they
                                                                                            // logged
                                                                                            // in
if ($myId == '0' &&
        (empty(filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING)))) {
    $myId = (filter_input(INPUT_COOKIE, 'staySignedIn',
            FILTER_SANITIZE_NUMBER_INT) >= '1') ? filter_input(INPUT_COOKIE,
            'staySignedIn', FILTER_SANITIZE_NUMBER_INT) : '0'; // are they
                                                               // logged in
}

if (filter_input(INPUT_POST, 'al', FILTER_SANITIZE_NUMBER_INT) == 2 &&
        filter_input(INPUT_POST, 'grabUser', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $newId = filter_input(INPUT_POST, 'grabUser', FILTER_SANITIZE_NUMBER_INT);
    $myId = $newId;
    $_SESSION['myId'] = $myId;
}

if ($myId >= 1) {
    if (filter_input(INPUT_POST, 'settingsUp', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $bc = filter_input(INPUT_POST, 'backgroundColor', FILTER_SANITIZE_STRING);
        $tc = filter_input(INPUT_POST, 'textColor', FILTER_SANITIZE_STRING);
        $hc = filter_input(INPUT_POST, 'highlightColor', FILTER_SANITIZE_STRING);
        $lc = filter_input(INPUT_POST, 'linkColor', FILTER_SANITIZE_STRING);
        $th = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_STRING);

        $stmt4 = $db->prepare(
                "UPDATE users SET backgroundColor = ?, textColor = ?, highlightColor = ?, linkColor = ?, theme = ? WHERE id = ?");
        $stmt4->execute(array(
                $bc,
                $tc,
                $hc,
                $lc,
                $th,
                $myId
        ));
    }

    $getCm = $db->prepare("SELECT * FROM users WHERE id = ?");
    $getCm->execute(array(
            $myId
    ));
    $getCmR = $getCm->fetch();
    $firstName = html_entity_decode($getCmR['firstName'], ENT_QUOTES);
    $lastName = html_entity_decode($getCmR['lastName'], ENT_QUOTES);
    $screenName = html_entity_decode($getCmR['screenName'], ENT_QUOTES);
    $email = $getCmR['email'];
    $startDate = $getCmR['startDate'];
    $subscriptionDate = $getCmR['subscriptionDate'];
    $accessLevel = $getCmR['accessLevel'];
    $theme = $getCmR['theme'];
    $backgroundColor = $getCmR['backgroundColor'];
    $textColor = $getCmR['textColor'];
    $highlightColor = $getCmR['highlightColor'];
    $linkColor = $getCmR['linkColor'];
    $subscriptionReminder = $getCmR['subscriptionReminder'];

    $subscribed = ($time <= $subscriptionDate) ? '1' : '0';
} else {
    $firstName = "";
    $lastName = "";
    $screenName = "";
    $accessLevel = 0;
    $theme = "default";
    $backgroundColor = "#ffffff";
    $textColor = "#000000";
    $highlightColor = "#bd4a11";
    $linkColor = "#bd4a11";
    $subscriptionReminder = 0;
    $subscribed = "0";
}

if ($theme != "Default") {
    $getT = $db->prepare("SELECT * FROM themes WHERE name = ?");
    $getT->execute(array(
            $theme
    ));
    $getTR = $getT->fetch();
    $backgroundColor = $getTR['backgroundColor'];
    $textColor = $getTR['textColor'];
    $highlightColor = $getTR['highlightColor'];
    $linkColor = $getTR['linkColor'];
}

// *** page settings ***
$page = (filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING)) ? filter_input(
        INPUT_GET, 'page', FILTER_SANITIZE_STRING) : "home";
if (! file_exists("pages/" . $page . ".php")) {
    $page = "home";
}

if (filter_input(INPUT_POST, 'sendFeedback', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $fromEmail = (filter_input(INPUT_POST, 'fromEmail', FILTER_VALIDATE_EMAIL)) ? filter_input(
            INPUT_POST, 'fromEmail', FILTER_SANITIZE_EMAIL) : 1;
    $emailBody = filter_input(INPUT_POST, 'emailBody', FILTER_SANITIZE_STRING);
    if ($fromEmail != 1) {
        $message = wordwrap($emailBody, 70);
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
        $headers .= "From: $fromEmail" . "\r\n";
        mail('admin@mydreamjournal.net',
                'Feedback from the Dream Journal website', $message, $headers);
    }
}

$WEEKDAYS = array(
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
);
$MONTHS = array(
        1 => "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
);

if (filter_input(INPUT_POST, 'dictFeedback', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $editSymbol = htmlentities(
            filter_input(INPUT_POST, 'symbol', FILTER_SANITIZE_STRING),
            ENT_QUOTES);
    $editNotes = htmlentities(
            filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING),
            ENT_QUOTES);
    $dfUp = $db->prepare(
            "INSERT INTO dictFeedback VALUES(NULL, ?, ?, '0', ?, '0', '0')");
    $dfUp->execute(array(
            $editSymbol,
            $editNotes,
            $myId
    ));
}
