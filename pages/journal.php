<?php
if ($myId >= 1) {
    $whichJ = filter_input(INPUT_GET, 'viewJ', FILTER_SANITIZE_STRING);
    $editJ = (filter_input(INPUT_GET, 'editJ', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'editJ', FILTER_SANITIZE_NUMBER_INT) : "new";
    $table = $myId . "Journal";

    $checkCount = $db->prepare("SELECT COUNT(*) FROM $table");
    $checkCount->execute();
    $CCR = $checkCount->fetch();
    $numOfEntries = $CCR[0];

    $whichJ = ($numOfEntries >= 1) ? $whichJ : "new";

    $delMsg = "";

    if (filter_input(INPUT_POST, 'delJ', FILTER_SANITIZE_NUMBER_INT)) {
        $delJ = filter_input(INPUT_POST, 'delJ', FILTER_SANITIZE_NUMBER_INT);
        $delMsg = "<form action='index.php?page=journal&viewJ=new&editJ=$delJ' method='post'>You are about to delete this dream. Are you sure you want to do that?&nbsp;&nbsp;&nbsp;NO <input type='radio' name='delJFinal' value='0' checked>&nbsp;&nbsp;&nbsp;YES <input type='radio' name='delJFinal' value='1'> <input type='submit' value=' Update '></form>";
    }

    if (filter_input(INPUT_POST, 'delJFinal', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $delJF = filter_input(INPUT_POST, 'delJFinal',
                FILTER_SANITIZE_NUMBER_INT);
        $del = $db->prepare("DELETE FROM $table WHERE id = ?");
        $del->execute(array(
                $editJ
        ));
        $editJ = "new";
    }

    if (filter_input(INPUT_POST, 'iUp', FILTER_SANITIZE_NUMBER_INT)) {
        $iUp = filter_input(INPUT_POST, 'iUp', FILTER_SANITIZE_NUMBER_INT);
        $interpretation = htmlentities(
                filter_input(INPUT_POST, 'interpretation',
                        FILTER_SANITIZE_STRING), ENT_QUOTES);
        $putI = $db->prepare(
                "UPDATE $table SET interpretation = ? WHERE id = ?");
        $putI->execute(array(
                $interpretation,
                $iUp
        ));
    }

    if (filter_input(INPUT_POST, 'dUp', FILTER_SANITIZE_STRING)) {
        $dUp = filter_input(INPUT_POST, 'dUp', FILTER_SANITIZE_STRING);
        $d1 = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT);
        $d2 = explode("-", $d1);
        $date = mktime(12, 0, 0, $d2[1], $d2[2], $d2[0]);
        $title = htmlentities(
                filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING),
                ENT_QUOTES);
        $dream = htmlentities(
                filter_input(INPUT_POST, 'dream', FILTER_SANITIZE_STRING),
                ENT_QUOTES);
        $delPic1 = (filter_input(INPUT_POST, 'delPic1',
                FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;
        $delPic2 = (filter_input(INPUT_POST, 'delPic2',
                FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;

        if ($delPic1 == 1) {
            $set = $db->prepare("UPDATE $table SET pic1 = ? WHERE id = ?");
            $set->execute(array(
                    'x.jpg',
                    $dUp
            ));
        }
        if ($delPic2 == 1) {
            $set = $db->prepare("UPDATE $table SET pic2 = ? WHERE id = ?");
            $set->execute(array(
                    'x.jpg',
                    $dUp
            ));
        }

        if ($dream != "" && $dream != " ") {
            if ($dUp == "new") {
                $put = $db->prepare(
                        "INSERT INTO $table VALUES" .
                        "(NULL, ?, ?, ?, '', 'x.jpg', 'x.jpg', '0', '0')");
                $put->execute(array(
                        $date,
                        $title,
                        $dream
                ));
                $getId = $db->prepare(
                        "SELECT id FROM $table WHERE date = ? AND title = ? AND dream = ? ORDER BY id DESC LIMIT 1");
                $getId->execute(array(
                        $date,
                        $title,
                        $dream
                ));
                $getIdR = $getId->fetch();
                $dUp = $getIdR[0];
            } else {
                $put = $db->prepare(
                        "UPDATE $table SET date = ?, title = ?, dream = ? WHERE id = ?");
                $put->execute(array(
                        $date,
                        $title,
                        $dream,
                        $dUp
                ));
            }
            if (! empty($_FILES["pic1"]["tmp_name"])) {
                $tmpFile = $_FILES["pic1"]["tmp_name"];
                list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                        $tmpFile) : null;
                if ($width != null && $height != null) {
                    $imageType = getPicType($_FILES["pic1"]['type']);
                    $imageName = $time . "." . $imageType;
                    processPic("cmPics/$myId", $imageName, $tmpFile, 800,
                            150);
                    $p1stmt = $db->prepare(
                            "UPDATE $table SET pic1 = ? WHERE id = ?");
                    $p1stmt->execute(array(
                            $imageName,
                            $dUp
                    ));
                }
            }
            if (! empty($_FILES["pic2"]["tmp_name"])) {
                $tmpFile = $_FILES["pic2"]["tmp_name"];
                list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                        $tmpFile) : null;
                if ($width != null && $height != null) {
                    $imageType = getPicType($_FILES["pic2"]['type']);
                    $imageName = $time . "." . $imageType;
                    processPic("cmPics/$myId", $imageName, $tmpFile, 800,
                            150);
                    $p2stmt = $db->prepare(
                            "UPDATE $table SET pic2 = ? WHERE id = ?");
                    $p2stmt->execute(array(
                            $imageName,
                            $dUp
                    ));
                }
            }
        }
    }

    if ($whichJ == "new") {
        echo "<form action='index.php?page=journal&viewJ=interpret' method='post' id='interpretJ'></form>";
        echo "<form action='index.php?page=journal&viewJ=review' method='post' id='reviewJ'></form>";
        echo "<table cellspacing='0' style='width:100%;'>\n";
        echo "<tr><td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor; color:$textColor;'>Record Your Dreams</td>\n";
        echo "<td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-bottom:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor;'>\n";
        echo "<div onclick='submitForm(\"interpretJ\")' style='cursor:pointer; color:$highlightColor;'>Interpret Your Dreams</div></td>";
        echo "<td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-bottom:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor;'>\n";
        echo "<div onclick='submitForm(\"reviewJ\")' style='cursor:pointer; color:$highlightColor;'>Review Your Dreams</div></td></tr>\n";
        echo "<tr><td colspan='3' style='border-bottom:1px solid $highlightColor; border-right:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px;'>\n";
        if ($delMsg != "") {
            echo "<div style='margin:20px auto; border:1px solid $highlightColor; padding:10px; text-align:center;'>$delMsg</div>";
        }
        echo "<form action='index.php?page=journal&viewJ=interpret' method='post' enctype='multipart/form-data' id='newJ'>";
        if ($editJ != 'new') {
            $get = $db->prepare("SELECT * FROM $table WHERE id = ?");
            $get->execute(array(
                    $editJ
            ));
            $getR = $get->fetch();
            $date = date("Y-m-d", $getR['date']);
            $title = html_entity_decode($getR['title'], ENT_QUOTES);
            $dream = html_entity_decode($getR['dream'], ENT_QUOTES);
            $interpretation = html_entity_decode($getR['interpretation'],
                    ENT_QUOTES);
            $pic1 = $getR['pic1'];
            $pic2 = $getR['pic2'];
        } else {
            $date = date("Y-m-d", $time);
            $title = "";
            $dream = "";
            $interpretation = "";
            $pic1 = "x.jpg";
            $pic2 = "x.jpg";
        }

        echo "<div style='padding:20px 0px;'><span style='font-weight:bold;'>Date:</span><br><input type='date' name='date' value='$date'></div>\n";
        echo "<div style='padding:20px 0px;'><span style='font-weight:bold;'>Title:</span><br><input type='text' name='title' value='$title'></div>\n";
        echo "<div style='padding:20px 0px;'><span style='font-weight:bold;'>Dream:</span><br><textarea name='dream' style='width:97%; height:100px;'>$dream</textarea></div>\n";
        echo "<div style='padding:20px 0px;'><span style='font-weight:bold;'>Picture:</span><br>";
        if (file_exists("cmPics/$myId/$pic1")) {
            echo "<image src='cmPics/$myId/thumb/$pic1' style='' alt=''><br><input type='checkbox' name='delPic1' value='1' /><br>\n";
            echo "Delete this image: <input type='hidden' name='delPic1' value='0'><br>\n";
        }
        echo "Upload a new picture: <input type='file' name='pic1' value='$pic1'></div>\n";
        echo "<div style='padding:20px 0px;'><span style='font-weight:bold;'>Picture:</span><br>\n";
        if (file_exists("cmPics/$myId/$pic2")) {
            echo "<image src='cmPics/$myId/thumb/$pic2' style='' alt=''><br><input type='checkbox' name='delPic2' value='1' /><br>\n";
            echo "Delete this image: <input type='hidden' name='delPic2' value='0'><br>\n";
        }
        echo "Upload a new picture: <input type='file' name='pic2' value='$pic2'></div>\n";
        echo "<div style='padding:20px 0px;'><input type='hidden' name='dUp' value='$editJ'><input type='submit' value=' Save '></div>\n";
        echo "</form></td></tr>\n";
        echo "</table>\n";
        if ($editJ != 'new') {
            echo "<form action='index.php?page=journal&viewJ=new&editJ=$editJ' method='post'><div style='font-weight:bold; margin-top:20px;'>If you would like to delete this entry entirely, click here: <input type='hidden' name='delJ' value='$editJ'><input type='submit' value=' Delete Entry '> This cannot be undone.</div></form>";
        }
    } elseif ($whichJ == 'interpret') {
        $year = (filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
                INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) : date("Y", $time);
        $month = (filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
                INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) : date("m",
                $time);
        echo "<form action='index.php?page=journal&viewJ=new' method='post' id='newJ'></form>";
        echo "<form action='index.php?page=journal&viewJ=review' method='post' id='reviewJ'></form>";
        echo "<table cellspacing='0' style='width:100%;'>\n";
        echo "<tr><td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-bottom:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor;'>\n";
        echo "<div onclick='submitForm(\"newJ\")' style='cursor:pointer; color:$highlightColor;'>Record Your Dreams</div></td>\n";
        echo "<td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor; color:$textColor;'>Interpret Your Dreams</td>";
        echo "<td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-bottom:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor;'>\n";
        echo "<div onclick='submitForm(\"reviewJ\")' style='cursor:pointer; color:$highlightColor;'>Review Your Dreams</div></td></tr>\n";
        echo "<tr><td colspan='3' style='border-bottom:1px solid $highlightColor; border-right:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px;'>\n";

        $getDates1 = $db->prepare(
                "SELECT date FROM $table ORDER BY DATE LIMIT 1");
        $getDates1->execute();
        $getDates1R = $getDates1->fetch();
        $YStart = date("Y", $getDates1R['date']);
        $getDates2 = $db->prepare(
                "SELECT date FROM $table ORDER BY date DESC LIMIT 1");
        $getDates2->execute();
        $getDates2R = $getDates2->fetch();
        $YEnd = date("Y", $getDates2R['date']);
        $yCount = 0;
        $years = array();
        for ($y = $YStart; $y <= $YEnd; ++ $y) {
            $ys = mktime(0, 0, 0, 1, 1, $y);
            $ye = mktime(23, 59, 59, 12, 31, $y);
            $getDates3 = $db->prepare(
                    "SELECT COUNT(*) FROM $table WHERE date >= ? AND date <= ?");
            $getDates3->execute(array(
                    $ys,
                    $ye
            ));
            $getDates3R = $getDates3->fetch();
            $YExists = $getDates3R[0];
            if ($YExists >= 1) {
                $yCount ++;
                $years[] = $y;
            }
        }
        if ($yCount >= 1) {
            echo "<table cellspacing='0px' style='width:100%;'><tr>\n";
            $percentY = (100 / $yCount);
            foreach ($years as $y) {
                echo "<td style='width:$percentY%; text-align:center; padding:10px; color:$linkColor; cursor:pointer; font-weight:bold; font-size:1.25em;' onclick='toggleview(\"showMonth$y\")'>$y</td>\n";
            }
            echo "</tr></table>\n";
            foreach ($years as $y) {
                $months = array();
                for ($m = 1; $m <= 12; ++ $m) {
                    $sm = mktime(0, 0, 0, $m, 1, $y);
                    $em = mktime(0, 0, 0, $m + 1, 1, $y);
                    $getDates4 = $db->prepare(
                            "SELECT COUNT(*) FROM $table WHERE date >= ? AND date < ?");
                    $getDates4->execute(array(
                            $sm,
                            $em
                    ));
                    $getDates4R = $getDates4->fetch();
                    $mExists = $getDates4R[0];
                    if ($mExists >= 1) {
                        $months[] = $m;
                    }
                }
                echo "<div id='showMonth$y' style='display:none; width:100%; text-align:center;'>\n";
                foreach ($months as $m) {
                    echo "<a href='index.php?page=journal&viewJ=interpret&year=$y&month=$m' style='color:$linkColor; font-weight:bold; font-size:1.25em; padding:0px 10px;'>" .
                            $MONTHS[$m] . "</a>\n";
                }
                echo "</div>\n";
            }
        }

        $symbolArray = array();
        $stmt = $db->prepare(
                "SELECT id, symbol FROM symbolDictionary ORDER BY length DESC");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $symbolId = $row['id'];
            $symbolArray[] = array(
                    $symbolId,
                    trim($row['symbol'])
            );
        }

        $SKIPWORDS = array();
        $getSkip = $db->prepare("SELECT word FROM skipWords");
        $getSkip->execute();
        while ($getSkipR = $getSkip->fetch()) {
            $SKIPWORDS[] = $getSkipR['word'];
        }

        $showS = mktime(0, 0, 0, $month, 1, $year);
        $showE = mktime(0, 0, 0, $month + 1, 1, $year);
        $show = $db->prepare(
                "SELECT * FROM $table WHERE date >= ? AND date < ? ORDER BY date");
        $show->execute(array(
                $showS,
                $showE
        ));
        while ($showR = $show->fetch()) {
            $id = $showR['id'];
            $date = date("Y-m-d", $showR['date']);
            $title = html_entity_decode(trim($showR['title']), ENT_QUOTES);
            $dream = html_entity_decode(trim($showR['dream']), ENT_QUOTES);
            $interpretation = html_entity_decode(
                    trim($showR['interpretation']), ENT_QUOTES);
            $pic1 = $showR['pic1'];
            $pic2 = $showR['pic2'];

            foreach ($symbolArray as $v) {
                if (str_word_count($v[1]) >= 2) {
                    $dream = str_ireplace($v[1], "[" . $v[0] . "]", $dream);
                }
            }

            $dreamList = explode(" ", $dream);

            foreach ($dreamList as $kd => &$vd) {
                $block = array(
                        " ",
                        ",",
                        ".",
                        "!",
                        "?",
                        "-",
                        ";",
                        ":",
                        "/",
                        "\"",
                        "\'",
                        "(",
                        ")",
                        "{",
                        "}",
                        "*",
                        "#",
                        "~",
                        "`"
                );
                $vdTrimmed = str_ireplace($block, "", $vd);
                if (! in_array(strtolower($vdTrimmed), $SKIPWORDS)) {
                    foreach ($symbolArray as $vs) {
                        if (str_contains($vd, $vs[1]) ||
                                str_contains($vd, strtolower($vs[1]))) {
                            $vd = str_ireplace($vs[1], "[" . $vs[0] . "]", $vd);
                            break;
                        }
                    }
                }
            }
            $dream = implode(" ", $dreamList);

            while (preg_match('/\[([1-9][0-9]*)\]/', $dream, $match)) {
                $symId = $match[1];
                $geti4 = $db->prepare(
                        "SELECT symbol, definition, source FROM symbolDictionary WHERE id = ?");
                $geti4->execute(array(
                        $symId
                ));
                $gi4 = $geti4->fetch();
                $gSymbol = html_entity_decode($gi4['symbol'], ENT_QUOTES);
                $gDefinition = html_entity_decode($gi4['definition'], ENT_QUOTES);
                $gS = $gi4['source'];

                $geti5 = $db->prepare("SELECT * FROM sources WHERE id = ?");
                $geti5->execute(array(
                        $gS
                ));
                $gi5 = $geti5->fetch();
                $gSource = html_entity_decode($gi5['source'], ENT_QUOTES);
                $gWebAddress = html_entity_decode($gi5['webAddress'], ENT_QUOTES);
                $gCitation = html_entity_decode($gi5['citation'], ENT_QUOTES);

                $replace = "<span style='color:$linkColor; cursor:pointer;' class='flip'>" .
                        strtoupper($gSymbol) . "</span>";
                $replace .= "<div style='display:none; margin:10px; border:1px solid $highlightColor; padding:10px;'>";
                $replace .= "<span style='color:$highlightColor;'>$gSymbol</span><br>";
                $replace .= "$gDefinition<br><br>";
                $replace .= "<span style='font-size:.75em;'>Source:<br>$gSource $gWebAddress<br>$gCitation</span></div>";
                $dream = str_ireplace("[" . $symId . "]", $replace, $dream);
            }
            $displayDream = str_replace("\n", "<br><br>",
                    make_links_clickable($dream, $linkColor));
            echo "<div style='margin-bottom: 5px; font-weight:bold; font-size:1.5em;'>$date</div>\n";
            echo "<div style='margin-bottom: 20px; font-size:.75em;'><a href='index.php?page=journal&viewJ=new&editJ=$id'>Edit Dream</a></div>\n";
            echo "<div style='margin-bottom: 10px; font-weight:bold; font-size:1.25em; color:$highlightColor;'>$title</div>\n";
            echo "<div style='margin-bottom: 10px;'><span style='font-weight:bold;'>Dream:</span><br>$displayDream</div>\n";
            echo "<div style='margin-bottom: 10px;'><span style='font-weight:bold;'>Interpretation:</span><br>";
            echo "<form action='index.php?page=journal&viewJ=review' method='post'>";
            echo "<textarea name='interpretation'  style='width:97%; height:100px;'>$interpretation</textarea><br>";
            echo "<input type='hidden' name='iUp' value='$id'><input type='submit' value=' Update '></form></div>\n";
            if (file_exists("cmPics/$myId/$pic1")) {
                echo "<div style='margin-bottom: 10px;'><image src='cmPics/$myId/$pic1' alt='' style='border:1px solid $highlightColor; padding:5px;' /></div>\n";
            }
            if (file_exists("cmPics/$myId/$pic2")) {
                echo "<div style='margin-bottom: 10px;'><image src='cmPics/$myId/$pic2' alt='' style='border:1px solid $highlightColor; padding:5px;' /></div>\n";
            }
            echo "<div style='height:20px;'>&nbsp;</div>\n";
        }
        echo "</td></tr>\n";
        echo "</table>\n";
    } elseif ($whichJ == 'review') {
        $year = (filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
                INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) : date("Y", $time);
        $month = (filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
                INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) : date("m",
                $time);
        echo "<form action='index.php?page=journal&viewJ=new' method='post' id='newJ'></form>";
        echo "<form action='index.php?page=journal&viewJ=interpret' method='post' id='interpretJ'></form>";
        echo "<table cellspacing='0' style='width:100%;'>\n";
        echo "<tr><td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-bottom:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor;'>\n";
        echo "<div onclick='submitForm(\"newJ\")' style='cursor:pointer; color:$highlightColor;'>Record Your Dreams</div></td>\n";
        echo "<td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-bottom:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor;'>\n";
        echo "<div onclick='submitForm(\"interpretJ\")' style='cursor:pointer; color:$highlightColor;'>Intepret Your Dreams</div></td>\n";
        echo "<td style='width:33%; border-top:1px solid $highlightColor; border-right:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px; text-align:center; font-weight:bold; background-color:$backgroundColor; color:$textColor;'>Review Your Dreams</td></tr>\n";

        echo "<tr><td colspan='3' style='border-bottom:1px solid $highlightColor; border-right:1px solid $highlightColor; border-left:1px solid $highlightColor; padding: 10px;'>\n";

        $getDates1 = $db->prepare(
                "SELECT date FROM $table ORDER BY DATE LIMIT 1");
        $getDates1->execute();
        $getDates1R = $getDates1->fetch();
        $YStart = date("Y", $getDates1R['date']);
        $getDates2 = $db->prepare(
                "SELECT date FROM $table ORDER BY date DESC LIMIT 1");
        $getDates2->execute();
        $getDates2R = $getDates2->fetch();
        $YEnd = date("Y", $getDates2R['date']);
        $yCount = 0;
        $years = array();
        for ($y = $YStart; $y <= $YEnd; ++ $y) {
            $ys = mktime(0, 0, 0, 1, 1, $y);
            $ye = mktime(23, 59, 59, 12, 31, $y);
            $getDates3 = $db->prepare(
                    "SELECT COUNT(*) FROM $table WHERE date >= ? AND date <= ?");
            $getDates3->execute(array(
                    $ys,
                    $ye
            ));
            $getDates3R = $getDates3->fetch();
            $YExists = $getDates3R[0];
            if ($YExists >= 1) {
                $yCount ++;
                $years[] = $y;
            }
        }
        if ($yCount >= 1) {
            echo "<table cellspacing='0px' style='width:100%;'><tr>\n";
            $percentY = (100 / $yCount);
            foreach ($years as $y) {
                echo "<td style='width:$percentY%; text-align:center; padding:10px; color:$linkColor; cursor:pointer; font-weight:bold; font-size:1.25em;' onclick='toggleview(\"showMonth$y\")'>$y</td>\n";
            }
            echo "</tr></table>\n";
            foreach ($years as $y) {
                $months = array();
                for ($m = 1; $m <= 12; ++ $m) {
                    $sm = mktime(0, 0, 0, $m, 1, $y);
                    $em = mktime(0, 0, 0, $m + 1, 1, $y);
                    $getDates4 = $db->prepare(
                            "SELECT COUNT(*) FROM $table WHERE date >= ? AND date < ?");
                    $getDates4->execute(array(
                            $sm,
                            $em
                    ));
                    $getDates4R = $getDates4->fetch();
                    $mExists = $getDates4R[0];
                    if ($mExists >= 1) {
                        $months[] = $m;
                    }
                }
                echo "<div id='showMonth$y' style='display:none; width:100%; text-align:center;'>\n";
                foreach ($months as $m) {
                    echo "<a href='index.php?page=journal&viewJ=review&year=$y&month=$m' style='color:$linkColor; font-weight:bold; font-size:1.25em; padding:0px 10px;'>" .
                            $MONTHS[$m] . "</a>\n";
                }
                echo "</div>\n";
            }
        }

        $showS = mktime(0, 0, 0, $month, 1, $year);
        $showE = mktime(0, 0, 0, $month + 1, 1, $year);
        $show = $db->prepare(
                "SELECT * FROM $table WHERE date >= ? AND date < ? ORDER BY date");
        $show->execute(array(
                $showS,
                $showE
        ));
        while ($showR = $show->fetch()) {
            $id = $showR['id'];
            $date = date("Y-m-d", $showR['date']);
            $title = html_entity_decode(trim($showR['title']), ENT_QUOTES);
            $dream = make_links_clickable(
                    html_entity_decode(trim($showR['dream']), ENT_QUOTES),
                    $linkColor);
            $interpretation = html_entity_decode(
                    trim($showR['interpretation']), ENT_QUOTES);
            $pic1 = $showR['pic1'];
            $pic2 = $showR['pic2'];

            echo "<div style='margin-bottom: 5px; font-weight:bold; font-size:1.5em;'>$date</div>\n";
            echo "<div style='margin-bottom: 20px; font-size:.75em;'><a href='index.php?page=journal&viewJ=new&editJ=$id'>Edit Dream</a></div>\n";
            echo "<div style='margin-bottom: 10px; font-weight:bold; font-size:1.25em; color:$highlightColor;'>$title</div>\n";
            echo "<div style='margin-bottom: 10px;'><span style='font-weight:bold;'>Dream:</span><br>" .
                    nl2br($dream) . "</div>\n";
            echo "<div style='margin-bottom: 10px;'><span style='font-weight:bold;'>Interpretation:</span><br>" .
                    nl2br($interpretation) . "</div>\n";
            if (file_exists("cmPics/$myId/$pic1")) {
                echo "<div style='margin-bottom: 10px;'><image src='cmPics/$myId/$pic1' alt='' style='border:1px solid $highlightColor; padding:5px;' /></div>\n";
            }
            if (file_exists("cmPics/$myId/$pic2")) {
                echo "<div style='margin-bottom: 10px;'><image src='cmPics/$myId/$pic2' alt='' style='border:1px solid $highlightColor; padding:5px;' /></div>\n";
            }
            echo "<div style='height:20px;'>&nbsp;</div>\n";
        }
        echo "</td></tr>\n";
        echo "</table>\n";
    }
} else {
    echo "<div style='text-align:center;'>Please <a href='index.php?page=login'>sign in or register</a> to use this page.</div>";
}
?>
