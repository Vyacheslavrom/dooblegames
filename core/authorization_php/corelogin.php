<?php

  $data_1 = $_POST;
if ( !isset($data_1['login1']) && !isset($data_1['do_login']) && !isset($data_1['do_signup']) && !isset($data_1['send'])){

      require './core/authorization_php/db.php';
}

 if ( isset ($_SESSION['logged_user']) ) :
  ?>
	<div class = "login_text">Авторизован!</div> <br/>
	<div class = "login_text">Привет,<?php echo $_SESSION['logged_user']->login; ?>!</div> <br/>

  <?php// print_r($_SESSION);?>
	<!--<a href="./core/authorization_php/logout.php">Выйти</a>-->



  <form method="POST">
  <button class = "login_text" type="submit" name="login1" value="logout">Выйти</button>
  <button class = "login_text" type="submit" name="login2" value="repass">Сменить пароль</button>
  </form>


<?php else : ?>
<div class = "login_text">Вы не авторизованы</div> <br/>

<!--<a href="./core/authorization_php/login.php">Авторизация</a>
<a href="./core/authorization_php/signup.php">Регистрация</a>-->
<form class = "login_text" method="POST">
	<button  type="submit" name="login1" value="login">Авторизация</button>
  <button  type="submit" name="login1" value="signup">Регистрация</button>
</form>
<?php endif; ?>
