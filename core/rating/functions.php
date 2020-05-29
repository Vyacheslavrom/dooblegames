<?php
/*
several funtions will be needed:

> Read all of DB and fnd average
> Create a GD image of the average
> Add a users rating to DB
> Check users IP
*/

function getip() {
    return getenv("REMOTE_ADDR");
}

function writerating($page,$rating) {
    global $path;
    $users_ip=getip();

    include($path.'settings.php');

    $db=mysql_connect($db_host,$db_user,$db_pass);
    mysql_select_db($db_name,$db);

    $sql="select * from ".$db_table."rater WHERE PAGEID=".$page;
    $res=mysql_query($sql,$db);

    while ($row=mysql_fetch_row($res)) {
        if ($row[1]==$users_ip) {
            echo("Sorry you already rated this page.");
            exit;
        }
    }

    //if we got here then they CAN rate this page!!

    $sql="INSERT INTO ".$db_table."rater (PAGEID,IP,RATING) VALUES('".$page."','".$users_ip."','".$rating."')";
    mysql_query($sql,$db);

}

function get_rating($page) {
    global $path;

    $found=0;
    include($path.'settings.php');
    $db=mysql_connect($db_host,$db_user,$db_pass);
    mysql_select_db($db_name,$db);
    $sql="select * from ".$db_table."pages WHERE PAGEID=".$page;
    $res=mysql_query($sql,$db);

    while ($row=mysql_fetch_row($res)) {
        $found=1;
    }
    if (!$found==1) {
        return false;
    }
    else {

        $db=mysql_connect($db_host,$db_user,$db_pass);
        mysql_select_db($db_name,$db);

        $sql="SELECT * FROM ".$db_table."rater WHERE PAGEID=".$page;
        $res=mysql_query($sql,$db);
        $ratingsum=0;
        $ratingcount=0;

        while ($row=mysql_fetch_row($res)) {
            $ratingsum+=$row[2];
            $ratingcount+=1;
        }

        if ($ratingcount=="0") {
            $sum=0;
        } else {
            $sum=$ratingsum/$ratingcount;
        }

        return $sum;
    }

}

function create_box($page,$text1,$style,$style1,$style2,$outlinestyle,$layout) {
    echo'<form name="form" method="post" action="'.$_SERVER['PHP_SELF'].'"><div style="'.$outlinestyle.'">';
    global $path, $novote, $version;

    $users_ip=getip();

    include($path.'settings.php');

    $db=mysql_connect($db_host,$db_user,$db_pass);
    mysql_select_db($db_name,$db);

    $sql="select * from ".$db_table."pages WHERE PAGEID=".$page;
    $res=mysql_query($sql,$db);
    $found=0;

    while ($row=mysql_fetch_row($res)) {
        $found=1;
    }
    if (!$found==1) {
        echo'Page is not rateable.';
    }
    else {

        $db=mysql_connect($db_host,$db_user,$db_pass);
        mysql_select_db($db_name,$db);

        $sql="SELECT * FROM ".$db_table."rater WHERE PAGEID=".$page;
        $res=mysql_query($sql,$db);
        $ratingsum=0;
        $ratingcount=0;
        $ipfound=0;

        while ($row=mysql_fetch_row($res)) {
            $ratingsum+=$row[2];
            $ratingcount+=1;

            //while where looking here lets check for the ip too
            if ($row[1]==$users_ip) {
                $ipfound=1;
                $therating=$row[2];

            }
        }

        if ($ipfound==1) {
        //user rated this already
        //echo image
            if ($ratingcount=="0") {
                $sum=0;
            }
            else {
                $sum=$ratingsum/$ratingcount;
            }
            if (!$text1=="") {
                echo $text1;
            }
            echo'<div style="'.$style.'"><img src="'.$path.'images/gd.php?rating='.($sum).'" alt="'.($sum).' Out of 5" style="vertical-align:middle;"><br>Rated by '.$ratingcount.' users.</div>';

            //no rating allowed
            echo'<div style="'.$style2.'">You already rated this '.$therating.'</div>';
        }
        else {
        //fresh rating :)
        //echo image
            if ($ratingcount=="0") {
                $sum=0;
            }
            else {
                $sum=$ratingsum/$ratingcount;
            }
            if (!$text1=="") {
                echo $text1;
            }
            echo'<div style="'.$style.'"><img src="'.$path.'images/gd.php?rating='.($sum).'" alt="'.($sum).' Out of 5" style="vertical-align:middle;"><br>Rated by '.$ratingcount.' users.</div>';
            if ($novote==0) {
                echo'<div style="'.$style1.'">
		<input type="hidden" name="rate" value="rate" />
		<input type="hidden" name="page" value="'.$page.'" />';
                if ($layout==1) {
                    echo '<input type="radio" value="5" name="rating_'.$page.'" id="rate5_'.$page.'" />Excellent<br>';
                    echo '<input type="radio" value="4" name="rating_'.$page.'" id="rate4_'.$page.'" />Very Good<br>';
                    echo '<input type="radio" value="3" name="rating_'.$page.'" id="rate3_'.$page.'" />Good<br>';
                    echo '<input type="radio" value="2" name="rating_'.$page.'" id="rate2_'.$page.'" />Fair<br>';
                    echo '<input type="radio" value="1" name="rating_'.$page.'" id="rate1_'.$page.'" />Poor<br>';
                    echo '<input type="hidden" name="rs_id" value="'.$page.'" />';
                    echo '<input type="submit" name="rate'.$page.'" value="Rate" />';
                }
                else {
                    echo '<input type="radio" value="1" name="rating_'.$page.'" id="rate5_'.$page.'" />1';
                    echo '<input type="radio" value="2" name="rating_'.$page.'" id="rate4_'.$page.'" />2';
                    echo '<input type="radio" value="3" name="rating_'.$page.'" id="rate3_'.$page.'" />3';
                    echo '<input type="radio" value="4" name="rating_'.$page.'" id="rate2_'.$page.'" />4';
                    echo '<input type="radio" value="5" name="rating_'.$page.'" id="rate1_'.$page.'" />5<br>';
                    echo '<input type="hidden" name="rs_id" value="'.$page.'" />';
                    echo '<input type="submit" name="rate'.$page.'" value="Rate" />';
                }

                echo'</div>';
            }
        //echo rating choices

        }
    }
    echo'<div align="center" style="font-size:12px"><a href="http://software.resplace.net/PageRater/" target="_blank" title="website page rating script by resplace.net">PageRater '.$version.'</a></div></div></form>';
}

//just show image
function show_rating($page) {
    global $path, $novote, $style;

    $users_ip=getip();

    include($path.'settings.php');

    $db=mysql_connect($db_host,$db_user,$db_pass);
    mysql_select_db($db_name,$db);

    $sql="select * from ".$db_table."pages WHERE PAGEID=".$page;
    $res=mysql_query($sql,$db);

    while ($row=mysql_fetch_row($res)) {
        $found=1;
    }
    if (!$found==1) {
        echo'Page is not rateable.';
    }
    else {

        $db=mysql_connect($db_host,$db_user,$db_pass);
        mysql_select_db($db_name,$db);

        $sql="SELECT * FROM ".$db_table."rater WHERE PAGEID=".$page;
        $res=mysql_query($sql,$db);
        $ratingsum=0;
        $ratingcount=0;

        while ($row=mysql_fetch_row($res)) {
            $ratingsum+=$row[2];
            $ratingcount+=1;

            //while where looking here lets check for the ip too
            if ($row[1]==$users_ip) {
                $ipfound=1;
                $therating=$row[2];

            }
        }

        //fresh rating :)
        //echo image
        if ($ratingcount=="0") {
            $sum=0;
        }
        else {
            $sum=$ratingsum/$ratingcount;
        }
        echo'<div style="'.$style.'"><img src="'.$path.'images/gd.php?rating='.($sum).'" alt="'.($sum).' Out of 5" style="vertical-align:middle;"></div>';
    //echo rating choices


    }
}

//heres the magical form submitted code!!
if ((isset($_POST['rate'])) && ($_POST['rate']=="rate")) {
    $var='rating_'.$_POST['page'];
    writerating($_POST['page'],$_POST[$var]);
}

include($path."top.php");
?>