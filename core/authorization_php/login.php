<?php
	require 'db.php';


	$data = $_POST;
	if ( isset($data['do_login']) )
	{
		$user = R::findOne('users', 'login = ?', array($data['login']));

		if ( $user )
		{
			//логин существует
			if ( password_verify($data['password'], $user->password) )

			{
				//если пароль совпадает, то нужно авторизовать пользователя
				$_SESSION['logged_user'] = $user;
				echo '<div class = "login_text">Вы авторизованы!<br/>';
				header('Location: /wblog');


				}else
			{
				$errors[] = 'Неверно введен пароль!';
			}

		}else
		{
			$errors[] = 'Пользователь с таким логином не найден!';
		}

		if ( ! empty($errors) )
		{
			//выводим ошибки авторизации
			echo '<div class = "login_text"><div id="errors" style="color:red;">' .array_shift($errors). '</div></div><hr>';
		}

	}

?>



<form class = "login_text"  method="POST">
<button type="submit" class="x">×</button>
	<strong>Логин</strong>

	<input type="text" name="login" value="<?php echo @$data['login']; ?>"><br/>

	<strong>Пароль</strong>
	<input type="password" name="password" value="<?php echo @$data['password']; ?>"><br/>

	<button type="submit" name="do_login">Войти</button>
	<button  type="submit" name="login1" value="send_password">Забыли пароль</button>
</form>
