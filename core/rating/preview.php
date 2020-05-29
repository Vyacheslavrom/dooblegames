<?php
/*
function insertcode() {
    global $inserted;
    if (isset($_POST['pageid'])) {
    //echo gen code
        echo'
        <div class="heading"><b>Insert Code:</b></div>
        <div class="content">
        <textarea name="gen" style="width:100%; height:80px">';
        echo"<?php\n".'$'."path=\"path to Page Rater dir\";\ninclude(".'$'."path.\"functions.php\");";

        if (isset($_POST['type'])) {
            if ($_POST['type']==1) {
                echo"\n\ncreate_box(".$_POST['pageid'].',"Rate Our Page:","text-align:center;font-size:12px","padding-top:8px; text-align:center;","background-color:red; padding:1px; margin-top:7px; text-align:center;","border: 1px solid black; padding:5px; width:160px; text-align:center; background-color:white",'.$_POST['rtype'].');';
            }
            if ($_POST['type']==2) {
                echo"\n\ncreate_box(".$_POST['pageid'].',"Rate this page:","font-size:12px","","","background-color:#BBDDF7; padding:5px; width:160px",'.$_POST['rtype'].');';
            }
            if ($_POST['type']==3) {
                echo"\n\ncreate_box(".$_POST['pageid'].',"Rate this page:","font-size:12px","margin-top:7px;","padding:1px; margin-top:7px; text-align:center;","border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #0099FF; background-color: #AEDFFF;",'.$_POST['rtype'].');';
            }
            if ($_POST['type']==4) {
                echo"\n\ncreate_box(".$_POST['pageid'].',"Rate this page:","font-size:12px","margin-top:7px;","padding:1px; margin-top:7px; text-align:center;","border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #00CC00; background-color: #A4FFA4;",'.$_POST['rtype'].');';
            }
        }

        echo"\n?>\n</textarea></div>";

        //make gen
        echo'<div class="heading"><b>Preview:</b></div>
            <div class="content">';
        $path='';
        include("functions.php");
        if (isset($_POST['type'])) {
            if ($_POST['type']==1) {

                create_box($_POST['pageid'],"Rate Our Page:","text-align:center;font-size:12px;","padding-top:8px; text-align:center;","background-color:red; padding:1px; margin-top:7px; text-align:center;","border: 1px solid black; padding:5px; width:160px; text-align:center; background-color:white",$_POST['rtype']);
            }
            if ($_POST['type']==2) {

                create_box($_POST['pageid'],"Rate this page:","font-size:12px","","","background-color:#BBDDF7; padding:5px; width:160px",$_POST['rtype']);
            }
            if ($_POST['type']==3) {

                create_box($_POST['pageid'],"Rate this page:","font-size:12px","margin-top:7px;","padding:1px; margin-top:7px; text-align:center;","border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #0099FF; background-color: #AEDFFF;",$_POST['rtype']);
            }
            if ($_POST['type']==4) {

                create_box($_POST['pageid'],"Rate this page:","font-size:12px","margin-top:7px;","padding:1px; margin-top:7px; text-align:center;","border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #00CC00; background-color: #A4FFA4;",$_POST['rtype']);
            }
        }
        echo'</div>';
    }

    echo'<div class="heading"><b>Style options</b></div>
        <div class="content"><form name="form" method="post" action="'.$_SERVER['PHP_SELF'].'?p=scripts">';
        if ($inserted==0) {
			echo'<b>Page ID:</b><br>
			<input type="text" name="pageid2" value="'.$_POST['pageid2'].'" /><br><br>';
        } else {
            echo'<input type="hidden" name="pageid" value="'.$inserted.'" />';
        }
			echo'<b>Rate Styles:</b><br>
			Style1: <input name="rtype" type="radio" value="1" checked /> Style2: <input name="rtype" type="radio" value="2" /><br>
			<img src="images/style.png" style="vertical-align:middle" border="0">
			<br><br>
			<b>Color Styles:</b><br>
			<input name="type" type="radio" value="1" checked />
			<img src="images/img1.png" style="vertical-align:middle" border="0">
			<input name="type" type="radio" value="2" />
			<img src="images/img2.png" style="vertical-align:middle" border="0">
			<br>
			<input name="type" type="radio" value="3" />
			<img src="images/img3.png" style="vertical-align:middle" border="0">
			<input name="type" type="radio" value="4" />
			<img src="images/img4.png" style="vertical-align:middle" border="0">
			<br>';
    echo '<br><input type="submit" name="Generate Code" value="Generate Code" /></div>';
}
 * 
 */

function insertcode() {
    global $inserted;
    
    ?>
        <div class="heading"><b>Insert Code:</b></div>
        <div class="content">
            <form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>?p=scripts">
                <style type="text/css">
                    .style1_1 {
                        text-align:center;font-size:12px;
                    }
                    .style1_2 {
                        padding-top:8px; text-align:center;
                    }
                    .style1_3 {
                        background-color:red; padding:1px; margin-top:7px; text-align:center;
                    }
                    .style1_4 {
                        border: 1px solid black; padding:5px; width:160px; text-align:center; background-color:white;
                    }
                    .style2_1 {
                        font-size:12px;
                    }
                    .style2_4 {
                        background-color:#BBDDF7; padding:5px; width:160px;
                    }
                    .style3_1 {
                        font-size:12px
                    }
                    .style3_2 {
                        margin-top:7px;
                    }
                    .style3_3 {
                        padding:1px; margin-top:7px; text-align:center;
                    }
                    .style3_4 {
                        border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #0099FF; background-color: #AEDFFF;
                    }
                    .style4_1 {
                        font-size:12px
                    }
                    .style4_2 {
                        margin-top:7px;
                    }
                    .style4_3 {
                        padding:1px; margin-top:7px; text-align:center;
                    }
                    .style4_4 {
                        border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #00CC00; background-color: #A4FFA4;
                    }
                    
                    .styleblank {

                    }
                </style>
                <script type="text/javascript">
                    function onlod() {
                        document.getElementById("style1").setAttribute("class", "style1_1");
                            style1="text-align:center;font-size:12px;";
                            document.getElementById("style11").setAttribute("class", "style1_1");
                            document.getElementById("style2").setAttribute("class", "style1_2");
                            style2="padding-top:8px; text-align:center;";
                            document.getElementById("style3").setAttribute("class", "style1_3");
                            style3="background-color:red; padding:1px; margin-top:7px; text-align:center;";
                            document.getElementById("outlinestyle").setAttribute("class", "style1_4");
                            style4="border: 1px solid black; padding:5px; width:160px; text-align:center; background-color:white;";
                            document.getElementById("outlinestyle1").setAttribute("class", "style1_4");

                    }
                    function hideshow(div,show) {
                        document.getElementById(div).style.display=show;
                        genresult();
                    }
                    function stylech() {
                        if (document.getElementById("style").value=="1") {
                            document.getElementById("style1").setAttribute("class", "style1_1");
                            style1="text-align:center;font-size:12px;";
                            document.getElementById("style11").setAttribute("class", "style1_1");
                            document.getElementById("style2").setAttribute("class", "style1_2");
                            style2="padding-top:8px; text-align:center;";
                            document.getElementById("style3").setAttribute("class", "style1_3");
                            style3="background-color:red; padding:1px; margin-top:7px; text-align:center;";
                            document.getElementById("outlinestyle").setAttribute("class", "style1_4");
                            style4="border: 1px solid black; padding:5px; width:160px; text-align:center; background-color:white;";
                            document.getElementById("outlinestyle1").setAttribute("class", "style1_4");
                        }
                        if (document.getElementById("style").value=="2") {
                            document.getElementById("style1").setAttribute("class", "style2_1");
                            style1="font-size:12px";
                            document.getElementById("style11").setAttribute("class", "style2_1");
                            document.getElementById("style2").setAttribute("class", "styleblank");
                            style2="";
                            document.getElementById("style3").setAttribute("class", "styleb");
                            style3="";
                            document.getElementById("outlinestyle").setAttribute("class", "style2_4");
                            style4="background-color:#BBDDF7; padding:5px; width:160px";
                            document.getElementById("outlinestyle1").setAttribute("class", "style2_4");
                        }
                        if (document.getElementById("style").value=="3") {
                            document.getElementById("style1").setAttribute("class", "style3_1");
                            style1="font-size:12px";
                            document.getElementById("style11").setAttribute("class", "style3_1");
                            document.getElementById("style2").setAttribute("class", "style3_2");
                            style2="margin-top:7px;";
                            document.getElementById("style3").setAttribute("class", "style3_3");
                            style3="padding:1px; margin-top:7px; text-align:center;";
                            document.getElementById("outlinestyle").setAttribute("class", "style3_4");
                            style4="border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #0099FF; background-color: #AEDFFF;";
                            document.getElementById("outlinestyle1").setAttribute("class", "style3_4");
                        }
                        if (document.getElementById("style").value=="4") {
                            document.getElementById("style1").setAttribute("class", "style4_1");
                            style1="font-size:12px";
                            document.getElementById("style11").setAttribute("class", "style4_1");
                            document.getElementById("style2").setAttribute("class", "style4_2");
                            style2="margin-top:7px;";
                            document.getElementById("style3").setAttribute("class", "style4_3");
                            style3="padding:1px; margin-top:7px; text-align:center;";
                            document.getElementById("outlinestyle").setAttribute("class", "style4_4");
                            style4="border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #00CC00; background-color: #A4FFA4;";
                            document.getElementById("outlinestyle1").setAttribute("class", "style4_4");
                        }
                        if (document.getElementById("style").value=="5") {
                            hideshow("custom","block");
                        } else {
                            hideshow("custom","none");
                        }
                        genresult();
                        
                    }
                    function genresult() {
                        id=document.getElementById("jspageid").value;
                        if (document.getElementById("rtype").checked==true) {
                            rtype=1;
                        } else {
                            rtype=2;
                        }
                        document.getElementById("result").innerHTML="&lt;?php\n$path=\"Relative path to PageRater Directory\"\ninclude($path.\"include.php\");\ncreate_box("+id+",\"Rate Page:\",\""+style1+"\",\""+style2+"\",\""+style3+"\",\""+style4+"\","+rtype+");\n?&gt;";
                    }
                </script>
                <div style="float:right;width:200px;" >
                    <div style="padding-bottom:4px"><b><u>Preview 1:</u></b></div>
                    <?php
                    previewrate("","","","");
                    ?>
                </div>
                <?php
                if ($inserted==0) {
			echo'<b>Page ID:</b>
			<input type="text"  id="jspageid" name="pageid2" value="'.$_POST['pageid2'].'" onchange="genresult()" />
                        <input type="hidden" name="jspageid" value="'.$_POST['pageid2'].'" /><br><br>';
                } else {
                    echo'<input type="hidden" id="jspageid" name="pageid" value="'.$inserted.'" />
                        <input type="hidden" name="jspageid" value="'.$inserted.'" />';
                }
                ?>
                <div style="padding-bottom:4px"><b><u>Display:</u></b></div>
                <input id="rtype" name="rtype" type="radio" value="1" checked onclick="hideshow('hori','block');hideshow('vert','none');" /> Horizontal  <br>
                <input name="rtype" type="radio" value="2" onclick="hideshow('hori','none');hideshow('vert','block');" /> Vertical <br>
                <br>
                <div style="padding-bottom:4px"><b><u>Style:</u></b></div>
                <select id="style" onchange="stylech()" onclick="stylech()">
                    <option value="1">White/Red</option>
                    <option value="2">Blue (no border)</option>
                    <option value="3">Blue (with border)</option>
                    <option value="4">Green (with border)</option>

                </select>
                <div id="custom" style="padding-top:10px;padding-left:20px;display:none">
                    Style1:<br>
                    <input type="text" name="style1" />
                </div>
                <br><br>
                <div style="padding-bottom:4px"><b><u>PHP Code:</u></b></div>
                <textarea id="result" style="width:550px;height:150px;">
                </textarea>
            </form>
        </div>
    <?php
}

function previewrate($style1,$style2,$style3,$outlinestyle) {
    global $path, $version;
    echo'<div id="outlinestyle" style="'.$outlinestyle.'">Rate Page:<div id="style1" style="'.$style1.'"><img src="'.$path.'images/gd.php?rating=3.5" alt="3.5 Out of 5" style="vertical-align:middle;"><br>Rated by 4 users.</div>';
        echo'<div id="style2" style="'.$style2.'">';
        echo'<div id="vert" style="display:none">';
            echo '<input type="radio" value="5" name="rating_0" id="rate5_0" />Excellent<br>';
            echo '<input type="radio" value="4" name="rating_0" id="rate4_0" />Very Good<br>';
            echo '<input type="radio" value="3" name="rating_0" id="rate3_0" />Good<br>';
            echo '<input type="radio" value="2" name="rating_0" id="rate2_0" />Fair<br>';
            echo '<input type="radio" value="1" name="rating_0" id="rate1_0" />Poor<br>';
            echo '<input type="button" name="rate0" value="Rate" />';
        echo'</div><div id="hori">';
            echo '<input type="radio" value="1" name="rating_0" id="rate5_0" />1';
            echo '<input type="radio" value="2" name="rating_0" id="rate4_0" />2';
            echo '<input type="radio" value="3" name="rating_0" id="rate3_0" />3';
            echo '<input type="radio" value="4" name="rating_0" id="rate2_0" />4';
            echo '<input type="radio" value="5" name="rating_0" id="rate1_0" />5<br>';
            echo '<input type="button" name="rate0" value="Rate" />';
        echo'</div></div><div align="center" style="font-size:12px"><a href="http://software.resplace.net/PageRater/" target="_blank">Page Rater '.$version.'</a></div></div>';

        echo"<br><div style=\"padding-bottom:4px\"><b><u>Preview 2:</u></b></div>";

            echo'<div id="outlinestyle1" style="'.$outlinestyle.'"><div id="style11" style="'.$style1.'"><img src="'.$path.'images/gd.php?rating=3.5" alt="3.5 Out of 5" style="vertical-align:middle;"><br>Rated by 4 users.</div>';
        echo'<div id="style3" style="'.$style3.'">You already rated this 3</div>';
        echo'<div align="center" style="font-size:12px"><a href="http://software.resplace.net/PageRater/" target="_blank">Page Rater '.$version.'</a></div></div>';
}

?>