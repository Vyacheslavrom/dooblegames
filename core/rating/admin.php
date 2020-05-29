<?php

session_start();
include('settings.php');
include("preview.php");
//if gen is set we are showing the code generator!!
if (isset($_GET["gen"])) {
    gen();
    exit;
}

//enabled??
if ($enable==1) {
//check for submitted page!!
    if ((isset($_POST['login'])) && ($_POST['login']=="login")) {
    //someones attempting to login!
        if (($_POST['username']==$username) && ($_POST['password']==$password)) {
            $_SESSION['pagerater_user']=$username;
            $_SESSION['pagerater_pass']=$password;
        } else {
        //wrong
            die("ERROR logging in, please go back and try again.");
        }
    } else {
        if ((isset($_SESSION['pagerater_user'])) && (isset($_SESSION['pagerater_pass'])) && ($_SESSION['pagerater_user']==$username) && ($_SESSION['pagerater_pass']==$password)) {} else {
        //show login form
            echo'<form name="form" method="post" action="'.$_SERVER['PHP_SELF'].'">
			Please Login:<br><br>
			<input type="hidden" name="login" value="login" />
			<b>Username:</b><br>
			<input type="text" name="username" value="" /><br><br>
			<b>Password:</b><br>
			<input type="password" name="password" value="" /><br>';
            echo '<input type="submit" name="login" value="login" />';
        }
    }

    if ((isset($_SESSION['pagerater_user'])) && (isset($_SESSION['pagerater_pass'])) && ($_SESSION['pagerater_user']==$username) && ($_SESSION['pagerater_pass']==$password)) {
    //show admin stuff
        
        
        $inserted=0;
        if (isset($_POST['pageid'])) {
            $inserted=$_POST['pageid'];
        } else if (isset($_POST['pageid2'])) {
            $_POST['pageid']=$_POST['pageid2'];
        } else if (isset($_GET['id'])) {
            $_POST['pageid2']=$_GET['id'];
        } else {
            $_POST['pageid2']="1";
        }

        if (isset($_GET['p'])) {
            $p=$_GET['p'];
        } else {
            $p='no';
        }

        if (isset($_GET['s'])) {
            $s=$_GET['s'];
        } else {
            $s='no';
        }
        head();
        echo'<div align="center"><div style="font-size:18px; padding:3px; background-color:#C9E2FC; border-bottom:1px solid #0033CC">
                <b>PageRater Admin Panel</b>
                <div style="font-size:13px;"> <a href="admin.php">'.bold("Home","no").'</a> | Stats | <a href="admin.php?p=manage">'.bold("Manage","manage").'</a> | <a href="admin.php?p=scripts">'.bold("Scripts","scripts").'</a> | <a href="readme.php" target="_blank">Help</a> | <a href="admin.php?p=logoff">'.bold("Logoff","logoff").'</a>
                </div>
            </div></div>';
        add();

        if ($p=="manage") {
        //build list of existing rating pages &amp; allow the deletion of those pages
            echo'<div class="heading"><b>View/Edit/Delete rate pages</b>:</div>';
            buildpages();

            if (!isset($_GET['id'])) {
                echo'<div class="heading"><b>Add rate page</b>:</div>';
                //allow the addition of pages
                addpage();
            }
        }

        if ($p=="logoff") {
            $_SESSION['pagerater_user']="dsfsdf";

            $_SESSION['pagerater_pass']="vfdsgs";
        }

        if ($p=="scripts") {
            //show scripts
            //menu
            echo'<div align="center"><div style="font-size:18px; padding:3px; background-color:#C9E2FC; border-bottom:1px solid #0033CC">
            <div style="font-size:13px;"> <a href="?p=scripts&amp;s=insert">'.bold2("Insert Codes","insert").'</a> | <a href="?p=scripts&amp;s=top">'.bold2("Top Rated","top").'</a> | <a href="?p=scripts&amp;s=plain">'.bold2("Plain Data","plain").'</a>
            </div></div>
            </div>';

            if (isset($_POST['add'])  || isset($_POST['pageid']) || ($s=="insert")) {
                //show insert methods
                insertcode();
            } else if ($s=="top") {
                echo"<div class=\"heading\"><b>Top Rated (Ascending)</b></div>
                <div class=\"content\">";
                    $path="";
                    include("functions.php");
                    top_rated("d","background-color:#C7DEF1; padding:4px; width:300px;","width:300px;");

                    echo"<textarea name=\"\" cols=\"50\" wrap=\"off\" rows=\"4\"><?php\n//Path to pagerater directory (relative)\n".'$'."path='';\ninclude_once(".'$'."path.'functions.php');\ntop_rated(\"d\",\"background-color:#C7DEF1; padding:4px; width:300px;\",\"width:300px;\");\n?>
                    </textarea>

                </div>
                <div class=\"heading\"><b>Top Rated (Descending)</b></div>
                <div class=\"content\">";
                    top_rated("a","background-color:#C7DEF1; padding:4px; width:300px;","width:300px;");

                    echo"<textarea name=\"\" cols=\"50\" wrap=\"off\" rows=\"4\"><?php\n//Path to pagerater directory (relative)\n".'$'."path='';\ninclude_once(".'$'."path.'include_once('functions.php');\ntop_rated(\"a\",\"background-color:#C7DEF1; padding:4px; width:300px;","width:300px;\");\n?>
                    </textarea></div>";
            }else if ($s=="plain") {
                echo"<div class=\"heading\">Get the rating value of a rate box (0-5)</div>
                    <div class=\"content\">
                        <textarea name=\"\" cols=\"50\" wrap=\"off\" rows=\"4\"><?php\n//Path to pagerater directory (relative)\n".'$'."path='';\ninclude_once(".'$'."path.'include_once('functions.php');\n".'$'."val=get_rating(150);\nif (".'$'."val===false) {\n    echo \"error\";\n} else {\n    echo ".'$'."val;\n}</textarea>
                    </div>";
                
            }else if ($s=="no") {


                echo"<div class=\"heading\"><b>Scripts</b></div>
                <div class=\"content\">You can generate insert code for a rate box and there are a few scripts here which you can use for various tasks, if you require a script to do a particular task please ask at the <a href=\"http://forum.resplace.net\" target=\"_blank\">forums</a> and we will do what we can to help you.</div>";
            }

        }

        if ($p=="old") {
            echo'<br><div style="padding:3px; background-color:#C9E2FC"><b>How to use in site</b>:</div>';
            echo'Each "page" you have created above resembles a different rating system, to include one of the "pages" into your webpage, just put the ID of the page in the input box below and then select the style you desire, click generate to create the code you require.<br>Experienced users can edit the CSS code sent through the PHP function to make their own style!<br><br>Make sure the page you are placing the code in is a PHP file, if its a HTML file just rename it to .php and make sure all your links are updated.<br><br>
                    <iframe src="admin.php?gen" width="100%" height="300"></iframe>';
        }

        if ($p=="no") {
            echo'
                <div class="heading"><b>A script by: <a href="http://resplace.net">resplace.net</a></b></div>
                    <div class="content">
                        <div class="bodydiv">Thanks for choosing PageRater to gain your visitors feedback, we hope you like our system as much as we like providing such quality systems and support. If you have any suggestions and/or comments then <a href="http://forum.resplace.net">please goto our forums</a> and create a topic!<br><br>
                        <b>resplace.net</b> is a donation run site, if you like this script then please give something back to us, we would really appreciate it!
                        <div align="center"><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" name="submit" alt="Make a donation with your Paypal account (or a credit card), were varified!! Thanks">
                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
                        ">
                        </form></div>
                    </div>
               </div>';

            echo'<br><div class="heading"><b>Updates</b>:</div>';
            echo'<div class="content">When an update is availiable it will appear below...
                <br><iframe frameborder="0" height="100" width="100%" src="http://software.resplace.net/PageRater/update.php?id='.$version.'"></iframe></div>';
        }
        
    }
}

function addpage() {
    echo'<div class="content">
        <form name="form" method="post" action="'.$_SERVER['PHP_SELF'].'?p=scripts">
			<input type="hidden" name="add" value="add" />
			<b>Page Name:</b><br>
                        <i>A name to distinguish this from others</i><br>
			<input type="text" name="pagename" value="" /><br><br>
			<b>Page URL:</b><br>
                        <i>Link to the page where you will put the code, so you can go right to that URL if needed (can be left blank)</i>
			<br><input type="text" name="url" value="http://" /><br><br>
                        <input type="submit" name="add page" value="add page" />
        </form>
        </div>';
}

function buildpages() {
    global $db_host,$db_user,$db_pass,$db_name,$db_table;

    $db=mysql_connect($db_host,$db_user,$db_pass);
    mysql_select_db($db_name,$db);

    if (isset($_POST['pagename']) && (!isset($_POST['add']))) {
        //submit
        $sql="UPDATE ".$db_table."pages SET PAGENAME='".mysql_real_escape_string($_POST['pagename'])."', PAGEURL='".mysql_real_escape_string($_POST['url'])."' WHERE PAGEID=".(int)$_POST['id'];
        mysql_query($sql,$db);
    }

    if (!isset($_GET['id'])) {
        //show pages
        $sql="select * from ".$db_table."pages ";
        $res=mysql_query($sql,$db);

        echo "<div class='content'>
                <table cellpadding=\"0\" cellspacing=\"0\">
                    <tr>
                        <td><b>Name:</b></td>
                        <td><b>ID:</b></td>
                        <td><b>Actions:</b></td>
                    </tr>";
        while ($row=mysql_fetch_row($res)) {
            echo"
                    <tr>
                        <td><a href=\"{$row[2]}\">{$row[1]}</a></td>
                        <td><div align=\"center\">{$row[0]}</div></td>
                        <td><a href=\"admin.php?p=scripts&amp;s=insert&amp;id={$row[0]}\" target=\"_blank\"><img src=\"images/view.png\" border=\"0\" class=\"center\" alt=\"Codes\" title=\"Get Code\" /></a> <a href=\"admin.php?p=manage&amp;id={$row[0]}\"><img src=\"images/edit.png\" border=\"0\" class=\"center\" alt=\"Edit\" title=\"Edit Item\" /></a> <a href=\"?p=manage&amp;delid={$row[0]}\" style=\"padding-left:10px\"><img src=\"images/delete.png\" border=\"0\" class=\"center\" alt=\"[X]\" title=\"delete\"></a></td>
                    </tr>
            ";
        }
        echo"</table>
            </div>";
    } else {
        //edit a page
        $sql="select * from ".$db_table."pages where PAGEID=".((int)$_GET['id']);
        $res=mysql_query($sql,$db);

        while ($row=mysql_fetch_row($res)) {
            echo"
                <div class=\"content\">
                    <form name=\"form\" method=\"post\" action=\"admin.php?p=manage\">
                        <input type='hidden' name='id' value='{$_GET['id']}'>
			<b>Page Name:</b><br>
                        <i>A name to distinguish this from others</i><br>
			<input type=\"text\" name=\"pagename\" value=\"{$row[1]}\" /><br><br>
			<b>Page URL:</b><br>
                        <i>Link to the page where you will put the code, so you can go right to that URL if needed (can be left blank)</i>
			<br><input type=\"text\" name=\"url\" value=\"{$row[2]}\" /><br><br>
                        <input type=\"submit\" name=\"update\" value=\"Update Page\" />
                    </form>
                </div>
            ";
        }

    }

}

function add() {
    global $db_host,$db_user,$db_pass,$db_name, $db_table, $inserted;
    if (isset($_GET['delid'])) {

        $db=mysql_connect($db_host,$db_user,$db_pass);
        mysql_select_db($db_name,$db);

        $sql="DELETE FROM ".$db_table."pages WHERE PAGEID=".intval($_GET['delid']);
        $res=mysql_query($sql,$db);

        $sql="DELETE FROM ".$db_table."rater WHERE PAGEID=".intval($_GET['delid']);
        $res=mysql_query($sql,$db);
        echo'<i>Page was removed from database</i><br><br>';
    }
    if ((isset($_POST['add'])) && ($_POST['add']=="add")) {
    error_reporting (E_ALL); 
        $db=mysql_connect($db_host,$db_user,$db_pass);
        mysql_select_db($db_name,$db);

        $sql="INSERT INTO ".$db_table."pages (PAGENAME,PAGEURL) VALUES('".$_POST['pagename']."','".$_POST['url']."')";
        $res=mysql_query($sql,$db);
        $inserted=mysql_insert_id();
        echo'<i>Page was added to database</i><br><br>';
    }
}

//insertcode

function head() {
    global $s;
    echo"<html>
    <head>
        <link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\">
        <title>PageRater Admin Panel</title>
    </head>";
    if (isset($_POST['add'])  || isset($_POST['pageid']) || ($s=="insert")) {
        echo"<body onload='onlod();genresult();'>";
    } else {
        echo"<body>";
    }
}

function bold($text,$string) {
    global $p;
    if ($p==$string) {
        return "<b>".$text."</b>";
    } else {
        return $text;
    }
}
function bold2($text,$string) {
    global $s;
    if ($s==$string) {
        return "<b>".$text."</b>";
    } else {
        return $text;
    }
}
?>
<div class="heading" align="center">PageRater v<?=$version; ?></div>