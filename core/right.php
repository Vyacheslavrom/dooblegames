<?php get_page()->begin_right_side(); ?>


  <?php if ( isset ($_SESSION['logged_user']) ) : ?>
  <div class='data'>Информация</div>

  <?php endif; ?>
  <div class='data'>Блок информации справа 1</div>

<?php  get_page()->end_right_side(); ?>
