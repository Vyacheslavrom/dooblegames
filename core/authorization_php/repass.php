<?php
	  //require 'db.php';
    $action = 'no';

    $login = $_SESSION['logged_user']->login;
    $id  = $_SESSION['logged_user']->id;
    $oldp = $_SESSION['logged_user']->password;


  	if ( isset($data_1['send_repass']) )
	{


			//логин существует
			if ( password_verify($data_1['log_oldpass'], $oldp))

			     {

				//если пароль совпадает, и новый пароль не короче 7 символов то меняем парол

          if (strlen($data_1['log_newpass']) >= 7)
                    {
                      $pass = R::load('users', $id);
                      $pass->password = password_hash($data_1['log_newpass'], PASSWORD_DEFAULT);
                      if (!password_verify($data_1['log_oldpass'], $pass->password)) {

    		                    R::store($pass);
                            $action = 'yes';

                               }else
                               {
                               $errors[] = 'Пароль не должен совпадать с предыдущим!';
                                }

                                             }else
                                                  {
                                                  $errors[] = 'Новый пароль короче 7 символов!';
                                                   }

				   }else
			         {
				        $errors[] = 'Неверно введен текущий пароль!';
			          }

  }



    if (isset($errors)){
    echo '<div class = "login_text"><div id="errors" style="color:red;">' .array_shift($errors). '</div></div><hr>';
    }


?>

 <?php if ($action != 'yes'): ?>
   <form class = "login_text"  method="POST">
     <button type="submit" class="x">×</button>
     <strong>Текущий пароль</strong>
     <input type="text" name="log_oldpass" ><br/>
   	<strong>Новый пароль "минимум 7 символов"</strong>
   	<input type="text" name="log_newpass"><br/>
   	<button type="submit" name="send_repass">Установить новый пароль</button>
   </form>
<?php else: ?>
	<div class = "login_text">Пароль изменен!</div> <br/>

<?php endif; ?>
