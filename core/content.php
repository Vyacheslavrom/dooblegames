

<?php
include "resize.php";
include "./games/base.thtml";
if(isset($_GET['game'])){
$page_game = $arr[$_GET['game']];?>


<div id="block">
<div class='game_container'><iframe src=<?php echo $page_game; ?>></iframe></div>
  <div id="block_resize"></div>
</div>
<?php
//echo "<div class='game_container'><iframe src=$page_game></iframe></div>";

}
else{
include "articles.php";
echo "<div class='data'>$art</div>";
}
include "./pages/rating.php";
include "./pages/commit.php";
?>
