<?php
//here is a very quick example using the system...
$path="";
include("functions.php");

?>
Thanks for installing PageRater, the first thing you may want to do is edit settings.php so that you have a truely unique username and password. you will also have to go here to
find out your initial username and password to login to the admin page.
<br><br>
<a href="admin.php">Take me to the admin page...</a>
<br><br>
you may delete this file now it is just a welcome file...<br /><br />
<b><u>Quick Rating example:</u></b><br>
<?php

$path='';

create_box(10,"Rate this page:","font-size:12px","margin-top:7px;","padding:1px; margin-top:7px; text-align:center;","border: 1px solid; padding:5px; width:160px; text-align:center; border-color: #00CC00; background-color: #A4FFA4;",2);

?>
<br /><br /> This is the end, see admin page for more...