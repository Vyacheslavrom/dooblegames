<?php
header("Content-type: image/gif");

$gdm=imagecreatefromgif("skin_yellow.gif");
$gd=imagecreatefromgif("starnone.gif");

if ($_GET['rating']==0)
{
$sum=0;
}
else
{
$sum=(($_GET['rating'])*(imagesx($gdm)/5));
}

imagecopymerge($gd,$gdm,0,0,0,0,($sum),imagesy($gdm),100);

imagegif($gd);
?>