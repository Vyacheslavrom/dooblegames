<?php
define('RPATH', realpath($_SERVER['DOCUMENT_ROOT']));
require_once(RPATH.'/ecomment.php');

$ref = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$comment = new ecomment($ref);

echo '<div id="ecomment_list">'.$comment->render_list().'</div>';
echo '<div id="ecomment_info">'.$comment->render_info().'</div>';
echo '<div id="ecomment_desktop">'.$comment->render_form().'</div>';

echo '<link rel="stylesheet" href="/ecomment.css" type="text/css" />';
echo '<script language="JavaScript" src="/ecomment.js" type="text/javascript"></script>';
?>