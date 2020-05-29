<?php
$path="";
include("functions.php");
// Page Rater - Created by: Dean Williams
// http://resplace.net

// PLEASE OPEN THIS FILE IN YOUR BROWSER - PHP NEEDED
if (isset($_GET['id'])) {
    $id=$_GET['id'];
}

?>

<html>
<head>
<style>
.headi {
background-color:#CCCCFF;
padding:4px;
margin-top:7px;

}
</style>
</head>
<body>

<?php

if (!isset($id))
{
?>

Thanks for downloading <b>Page Rater</b>, this system will enable you to create and implement mini voting booths on your website, either so visitors can rate individual pages of your site, or anything else you may need a voting system for!
<br />
<br />
<div class="headi">How it works</div>
<b>Page Rater</b> has an admin panel, in this panel you create "pages", each "page" is a seperate voting system. Every "page" has a identifier which is passed to a PHP function ( create_box(); ), all you need todo is include the small peice of PHP code into your page, this is provided when you create a "page". When you include the script remember to set $path to a relative or direct link to the image directory of this system!
<br>
<br>
If you are attempting to use the rater more than once in one page, please only include functions.php once, same goes for settin $path. <br />
<br />
<div class="headi">Styling the voting box</div>
This system allows full styling, in the admin page you can use the code generator to create several styles, you may want to create your own style though so below we will show you the arguments of the function:
<br><br>
create_box(!! ID !!,/* top text */,/* image style */,/* form style */,/* un-rateable style */,/* box style */,/* display type */);

<br /><br />

<br />
<br />
<div class="headi">Adding 'top rated' into your site</div>
We have created a function so you can display the top rated pages. Again there is a styling system built into it so we have created several examples:
<br /><br />

<a href="?id=2">Top Rated examples.</a>


<?php
}
else if ($id==2)
{

echo'<div align="center"> <div class="headi">Ratings from highest to lowest:</div><br>';
top_rated("d","background-color:#C7DEF1; padding:4px; width:300px;","width:300px;");

?>
<textarea name="" cols="50" wrap="off" rows="4">
include_once('functions.php');
top_rated("d","background-color:#C7DEF1; padding:4px; width:300px;","width:300px;");
</textarea>

<?php
echo'</div>';

echo'<div align="center"> <div class="headi">Ratings from lowest to highest:</div><br>';
top_rated("a","background-color:#C7DEF1; padding:4px; width:300px;","width:300px;");

?>
<textarea name="" cols="50" wrap="off" rows="4">
include_once('functions.php');
top_rated("a","background-color:#C7DEF1; padding:4px; width:300px;","width:300px;");
</textarea>

<?php
echo'</div>';
}
?>
