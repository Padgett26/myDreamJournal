<!-- Beginning of Head -->

<link rel="shortcut icon" href="images/MDJIcon.gif" />
<meta http-equiv='Content-Type'     content='text/html; charset=UTF-8' />
<meta name="viewport"               content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1" />
<meta name="keywords"               content="dream, journal, dream journal, dream meaning, dream symbol, mydreamjournal, diary" />
<meta name="description"            content="A personal dream journal. Where you can record your dreams and related pictures, look up meaning behind the symbols in those dreams, record your own interpretations, and discuss dream symbolism with your peers." />
<meta http-equiv="X-UA-Compatible"  content="IE=edge" />
<meta property="fb:app_id"          content="539048382922093" />
<meta property="og:site_name"       content="My Dream Journal" />
<meta property="og:type"            content="article" />
<meta property="og:url"             content="https://mydreamjournal.net">
<meta property="og:title"           content="My Dream Journal">
<meta property='og:image'           content='https://mydreamjournal.net/images/MDJLogo.png'>
<meta property='og:image:width'     content='538'>
<meta property='og:image:height'    content='324'>
<meta property="og:description"     content="A personal dream journal. Where you can record your dreams and related pictures, look up meaning behind the symbols in those dreams, record your own interpretations, and discuss dream symbolism with your peers.">
<?php
echo "<title>My Dream Journal || $page</title>";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function(){
    $(".flip").click(function(){
        $(this).next().slideToggle("slow");
    });
});

$(document).ready(function () {
        $("#panelFeedback").slideUp();
    });
    $(document).ready(function () {
        $("#flipFeedback").click(function () {
            $("#panelFeedback").slideToggle("slow");
        });
    });
</script>
<script type="text/javascript">
    function toggleview(itm) {
        var itmx = document.getElementById(itm);
        if (itmx.style.display === "none") {
            itmx.style.display = "block";
        } else {
            itmx.style.display = "none";
        }
    }

    function screenNameCheck(linkId, myId, name) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById(linkId).innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "ajax/screenNameCheck.php?checkName=" + name + "&myId=" + myId, true);
        xmlhttp.send();
    }

    function dreamLimit(text, linkId) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById(linkId).innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "ajax/dreamLimit.php?text=" + text, true);
        xmlhttp.send();
    }

    function submitForm(Id) {
        document.getElementById(Id).submit();
    }
</script>

<style type="text/css">
    body {
        color: <?php
								echo $textColor;
								?>;
        background-color: <?php
								echo $backgroundColor;
								?>;
    }
    a {
        color: <?php
								echo $linkColor;
								?>;
        text-decoration: none;
        cursor: pointer;
    }
    a:hover {
        color: #444444;
        text-decoration: underline;
        cursor: pointer;
    }
    span.link {
        color: <?php
								echo $linkColor;
								?>;
        text-decoration: none;
        cursor: pointer;
    }
    span.link:hover {
        color: #444444;
        text-decoration: underline;
        cursor: pointer;
    }
    th, td {
        vertical-align: top;
        padding:5px;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #printArea, #printArea * {
            visibility: visible;
        }
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
        }
        footer {
        page-break-after: always;
        }
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    .main {
        width:100%;
    }

    input {
    background-color:#dddddd;
    color:#000000;
    }

    textarea {
    background-color:#dddddd;
    color:#000000;
    }
</style>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-S8MW68ZCPY"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-S8MW68ZCPY');
</script>

<!-- End of Head -->