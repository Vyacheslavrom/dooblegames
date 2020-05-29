<?php
//function to generate top rated pages!
function top_rated($order, $style1,$style2)
{
include('settings.php');
$novote=1;

$db=mysql_connect($db_host,$db_user,$db_pass);
	mysql_select_db($db_name,$db);

//FIRST LETS GET THE PAGES
	$sql="select * from ".$db_table."pages ";
	$res=mysql_query($sql,$db);
	$count=0;

	while ($row=mysql_fetch_row($res))
		{
		//page id : $row[0]

	//NOW LETS GET DATA INFORMATION!
		$sql="select * from ".$db_table."rater WHERE PAGEID=".$row[0];
		$res2=mysql_query($sql,$db);
		$ratingsum=0;
		$ratingcount=0;


		while ($row2=mysql_fetch_row($res2))
			{
			//do all the math
			$ratingsum+=$row2[2];
			$ratingcount+=1;
			}

		//generate points and place into ARRAY
		if (($ratingcount==0) || ($ratingsum==0))
		{
		$score=0;
		}
		else
		{
		$score=($ratingsum/$ratingcount);
		}
		//$score=($ratingsum*$scoreb);

		$top_array[]=array('link'=>$row[2],
						   'name'=>$row[1],
						   'score'=>$score,
						   'id'=>$row[0],
						   'ppl'=>$ratingcount);

		}
	$order_arr =
  array(
   array('score',$order), // d means decending - swap for 'a' to see effect
   array('ppl',$order),
  );

$sortedarray = arfsort( $top_array, $order_arr);


	foreach ($sortedarray as $key=>$value) {
	$switch=0;
    foreach ($value as $key=>$value2) {


		if ($switch==0)
		{
		//link
    	echo '<div style="'.$style1.'"><a href='.$value2.'>';
		}
		else if ($switch==1)
		{
		//name
		echo $value2.'</a></div>';
		}
		else if ($switch==2)
		{
		//score
		//echo $value2.'<br>';

		}
		else if ($switch==3)
		{
		//id
		echo'<div style="'.$style2.'">';
		show_rating($value2);
		}
		else
		{
		echo $value2.' vote\'s.</div>';
		}
		if ($switch==5) {$switch=0;} else { $switch+=1;}

    }
  }
}


function arfsort( $a, $fl ){
  $GLOBALS['__ARFSORT_LIST__'] = $fl;
  usort( $a, 'arfsort_func' );
  return $a;
}

// extended to allow sort direction per field sorted against
function arfsort_func( $a, $b ){
  foreach( $GLOBALS['__ARFSORT_LIST__'] as $f ) {
   switch ($f[1]) { // switch on ascending or descending value
     case "d":
       $strc = strcmp( strtolower($b[$f[0]]), strtolower($a[$f[0]]) );
       if ( $strc != 0 ){
         return $strc;
       }
     break;
     default:
       $strc = strcmp( strtolower($a[$f[0]]), strtolower($b[$f[0]]) );
       if ( $strc != 0 ){
         return $strc;
       }
     break;
   }
  }
  return 0;
}



?>