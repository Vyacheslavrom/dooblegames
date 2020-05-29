
<?php
	require 'db.php';


	$data = $_POST;
	if ( isset($data['send']) )
	{
		$user = R::findOne('users', 'login = ?', array($data['log_mail']));

   if ($user){
    $email  = $user->email;
		$id  = $user->id;
		include "./core/send_email.php";
		$pass_new = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 7);?>
		<div class ='login_text' > <?= send_email ("$email","$pass_new")?> </div><br/>
		<?php $pass = R::load('users', $id);
		$pass->password = password_hash($pass_new, PASSWORD_DEFAULT);
		R::store($pass);
     // получаем email из базы
     // отправка письма на email

  }

  $user_1 = R::findOne('users', 'email = ?', array($data['log_mail']));
  //print_r($user_1);
  if ($user_1){
    $email_1  = $user_1->email;
		$id  = $user_1->id;
		include "./core/send_email.php";
		$pass_new = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 7);?>
		<div class ='login_text' > <?= send_email ("$email_1","$pass_new")?> </div><br/>
	<?php	$pass = R::load('users', $id);
    $pass->password = password_hash($pass_new, PASSWORD_DEFAULT);
    R::store($pass);

		//echo password_hash($pass_new, PASSWORD_DEFAULT);
		//print_r($pass).'<br/>';
		//echo $pass->password.'<br/>';

    //отправка письма на email

  }



		if (!isset($email) && !isset($email_1))
		{
			$errors[] = 'нет такого логина или емаила';
			//выводим ошибки авторизации
			echo '<div class = "login_text"><div id="errors" style="color:red;">' .array_shift($errors). '</div></div><hr>';
		}

	}

?>
 <?php if (!isset($email) && !isset($email_1)):?>
<form class = "login_text"  method="POST">
  <button type="submit" class="x">×</button>
	<strong>Логин или почта</strong>
	<input type="text" name="log_mail" ><br/>
	<button type="submit" name="send">Отправить новый пароль</button>
</form>
<?php endif ?>
