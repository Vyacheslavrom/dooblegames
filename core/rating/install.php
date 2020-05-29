<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
	color: #000099;
}
body {
 background-image: url(bg.gif);
 background-repeat: repeat-x;
 background-color: #6f9db8;
}
.tbl{
 background-color: white;
 height:100%;
}
.style1 {font-size: 16px
/*float: none;
visibility: visible;visibility: hidden;*/
}
.style2 {font-size: 18px}
-->
</style>
<form action="install.php" name="myform" id="myform" method="post">
<?php
if (!isset($_POST["stage"])){
  $title="PageRater Installer";
  $body="Welcome, thanks for choosing Page Rater, before continuing please make sure you have all the files in the ZIP package unzipped onto your server. <br><br>If you have any problems installing this then please visit our forums <a href=http://tpvgames.co.uk/forum target='_blank'>HERE.</a>";
  $st=2;
  $back="hidden";
  $next="false";
}
else{
  $back="visible";
  switch ($_POST["stage"]){
    case "2":{
      $title="Step 1 - Check directory";
      if (!is_writeable(getcwd()."/")){
        $body="Firstly we will need to have access to write to the directory this installer has been placed in, we will check now... <br><br>Please Chmod this dir to 777";
      }else{
        $body="Firstly we will need to have access to write to the directory this installer has been placed in, we will check now... <br><br>Directory validation sucess!!";
      }
      $st=3;
    };break;
	case "3":{
      $title="Step 3 - MySQL Details";
      
      $body='Page Rater uses MySQL to operate, please input your database details below:<br><br><table width="446" height="110" border="0" cellpadding="0" cellspacing="0" id="none">
  <tr>
    <td width="266" height="22">Database Location:
    <br></td>
    <td width="180"><input name="db_server" type="text" value="localhost" size="30"></td>
  </tr>
  <tr>
    <td height="22">Database Name: </td>
    <td><input name="db_name" type="text" value="" size="30"></td>
  </tr>
  <tr>
    <td height="22">Database Username:</td>
    <td><input name="db_user" type="text" value="" size="30"></td>
  </tr>
  <tr>
    <td height="22">Database Password: </td>
    <td><input name="db_passwd" type="password" value="" size="30"></td>
  </tr>
  <tr>
    <td height="22">Page Rater Prefix:</td>
    <td><input name="prex" type="text" value="PRater_" size="30"></td>
  </tr>
</table>
<b>Page Rater Prefix</b> is a string which we place at the beginning of each table created, this can help prevent MPCS trying to use an existing table that another system is using, you are recommended to leave this unless your installing multiple copies of Page Rater. <br>
<br>
';
      $st=4;
    };break;
	case "4":{
      $title="Step 3 - making the tables";
      
      $body="We will now attempting to create the tables Page Rater requires...<br><br><input name=\"db_server\" type=\"hidden\" value=\"".$_POST["db_server"]."\" size=\"30\"><input name=\"db_name\" type=\"hidden\" value=\"".$_POST["db_name"]."\" size=\"30\"><input name=\"db_user\" type=\"hidden\" value=\"".$_POST["db_user"]."\" size=\"30\"><input name=\"db_passwd\" type=\"hidden\" value=\"".$_POST["db_passwd"]."\" size=\"30\"><input name=\"prex\" type=\"hidden\" value=\"".$_POST["prex"]."\" size=\"30\">".setuptables($_POST["prex"],$_POST["db_server"],$_POST["db_user"],$_POST["db_passwd"],$_POST["db_name"],$_POST["path"])."<br><br>if there are no errors above then it looks like we was successful :)";
      $st=5;
    };break;
	
	case "5":{
      $title="Step 4 - creating settings.php file";
      $rand=rand(11111,999999999);
      
      $body="We will now create a file to store the database details. <br>Attempting to write to the config.php file now...<br><br>".writesetting("settings.php",
	  
	  "<?php
//db settings
".chr(36)."db_host='".$_POST["db_server"]."';
".chr(36)."db_name='".$_POST['db_name']."';
".chr(36)."db_user='".$_POST["db_user"]."';
".chr(36)."db_pass='".$_POST['db_passwd']."';
".chr(36)."db_table='".$_POST['prex']."';

//admin page settings
".chr(36)."username=\"admin\";
".chr(36)."password=\"pass{$rand}\";
".chr(36)."enable=1;
".chr(36)."version=\"0.5.0\";
?>")."
<br><br>NOTE: Your username is <b>admin</b> and password is <b>pass{$rand}</b> for the admin panel, Its recommended you open the settings file up as soon as possible and change these settings.";
      $st=6;
    };break;
  case "6":{
      $title="Step 5 - removing uneeded files";
      
      $body="It is crutual that the installer is removed after installation, if you notice that the install.php still exists then please manually delete me, <br><br>Bye and enjoy :)";

      $st=7;
    };break;
	  case "7":{
      $title="Step 5 - Redirecting to Page Rater";

      $body="You should redirect... if not please <a href=\"index.php\">click here</a>";
	  echo '<META HTTP-EQUIV="refresh" content="1;URL=index.php">';
	        unlink("1.png");
unlink("2.png");
unlink("bg.gif");
unlink("leftlogo.gif");
unlink("on.gif");
unlink("install.php");
    };break;
  }
}
?>
<title>
  <?php echo $title; ?>
</title>
</head>
<body>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="tbl">
  <tr  style="background:url(2.png) repeat-x;border:1px solid black ;">
    <td height="38" colspan="2" valign="middle">
      
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="background:url(2.png) repeat-x;border:1px solid black ;">
          <td width="38"><span><img src="on.gif" alt="Gallery installer" title="Gallery installer" border="0"></span></td>
          <td width="216"><span class="style2">Page Rater Installer</span></td>
          <td><span style=" " class="style2" valign="midle" ><?php echo $title; ?></span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="236" valign="top" style="background:#EEEEEE"><img src="leftlogo.gif" width="236" height="582"> </td>
    <td width="505" valign="top">
      <table width="100%" height="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" height="80%" valign="top" style="padding:50px;">
          <?php echo $body; ?>          </td>
        </tr>
        <tr>
          <td width="100%" height="20%">
            <div align="center">
              
                <input type="hidden" name="stage" value="<?php echo $st; ?>">
                <input type="button" name="back" style="visibility:<?php echo $back; ?>" value="Back" onClick="history.back()">
                <input type="submit" name="submit" value="Continue" <?php if ((isset($next)) && ($next=="true")) echo 'disabled='.$next; ?>>
              </form>
            </div>          </td>
        </tr>
      </table>    </td>
  </tr>
</table>
 <p><br>
   If you need assistence with installing your software please <a href="http://forums.resplace.net" title="resplace forums" target="_blank">click here to goto our forums.</a></p>
</body>
</html>
<?php

function writesetting($filename,$somecontent)
{
//chmod($filename,777);
// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {

   // In our example we're opening $filename in append mode.
   // The file pointer is at the bottom of the file hence 
   // that's where $somecontent will go when we fwrite() it.
   if (!$handle = fopen($filename, 'a')) {
         return "<b>Cannot open file $filename, it doesnt exist or restrictions forbid access.</b><br>";
         exit;
   }

   // Write $somecontent to our opened file.
   if (fwrite($handle, $somecontent) === FALSE) {
       return "<b>Cannot write to file $filename</b><br>";
       exit;
   }
   
   return "<b>Success, data was written to config.php</b><br>";
   
   fclose($handle);

} else {
   return "The file $filename is not writable<br>";
}
}

function setuptables($prex,$db_server,$db_user,$db_passwd,$db_name)
{
$query="CREATE TABLE `".$prex."pages` (
  `PAGEID` int(255) NOT NULL auto_increment,
  `PAGENAME` varchar(255)  NOT NULL,
  `PAGEURL` varchar(255) NOT NULL,
  PRIMARY KEY  (`PAGEID`)
) ENGINE=MyISAM AUTO_INCREMENT=12 ;";

$query2="CREATE TABLE `".$prex."rater` (
  `PAGEID` varchar(255) NOT NULL,
  `IP` varchar(255) NOT NULL,
  `RATING` int(5) NOT NULL
) ENGINE=MyISAM;";

$db1 = mysql_connect($db_server,$db_user,$db_passwd);
mysql_select_db ($db_name);
$qry=mysql_query($query) or die ("1<b>Error: </b>". mysql_error());
$qry2=mysql_query($query2) or die ("2<b>Error: </b>". mysql_error());

mysql_query("INSERT INTO `".$prex."rater` VALUES ('10', '-', 5);") or die ("6<b>Error: </b>". mysql_error());
mysql_query("INSERT INTO `".$prex."rater` VALUES ('9', '-', 3);") or die ("7<b>Error: </b>". mysql_error());
mysql_query("INSERT INTO `".$prex."pages` VALUES (10, 'Default #2', 'http://default/2');") or die ("8<b>Error: </b>". mysql_error());
mysql_query("INSERT INTO `".$prex."pages` VALUES (9, 'Default #1', 'http://default/1');") or die ("9<b>Error: </b>". mysql_error());
mysql_query("INSERT INTO `".$prex."pages` VALUES (11, 'Default #3', 'http://default 3');") or die ("11<b>Error: </b>". mysql_error());
}
function quote_smart($db_server,$db_user,$db_passwd,$value){

	if (get_magic_quotes_gpc()){
		$value=stripslashes($value);
	}

	if (!is_numeric($value)){
		$db1 = mysql_connect($db_server,$db_user,$db_passwd);
mysql_select_db ($db_name,$db1);
		$value=mysql_real_escape_string($value);
	}

	return $value;
}

?>

