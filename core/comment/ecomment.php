<?php
ini_set("display_errors", 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
/**
 * 1.7.0 изменения:
 * Новое: добавлена возможность настроить свои дополнительные поля формы комментирования;
 * Новое: обработчик инф.сообщений вынесен в отдельную функцию для удобства перегрузки;
 * Новое: в обязательные для заполнения поля добавлен HTML5-параметр "required";
 * Новое: добавлена настройка формата отображения даты комментария;
 * Новое: добавлена настройка флуд-контроля;
 * FIX: дублируется HTTP_REFERER на аварийный случай (не поддерживается браузером или режется файрволлом);
 * FIX: исправлена ошибка дополнительных search параметров страницы, которые могли бы участвовать в формировании ref;
 * FIX: исправлено формирование обратной ссылки в нотификации.
 *
 * 1.7.1 Изменения:
 * FIX: баг с обратной ссылкой в нотификации;
 * FIX: баг с серверной интеграцией, когда не формировался http_ref - не сохранялись комменты с первого раза или не проходили бот-проверку;
 * FIX: лишнее сообщение об отписке при первом комменте;
 *
 * todo: хеширование ссылки на проект + возможность отключения
 *
 * 1.7.2 Изменения:
 * NEW: убрана проверка флуд-контроля для администратора и добавлена возможность вывода таймера до снятия флуд контроля;
 * FIX: баг с именем сервера в обратной ссылке нотификации;
 * FIX: обработка переносов строк при редактировании сообщений;
 */
class ecomment {
	private $version = '1.7.2'; //версия скрипта

	//основные настройки
	private $store = '/store/'; //путь до директории хранения файлов с комментариями. Директория должна существовать и иметь достаточные права доступа.
	private $moderate = true; //премодерация - если true, то сообщения попадают в публичный список только после утверждения модератором.
	private $notify = true; //уведомление о новых комментах
	private $subscribe_allowed = true; //разрешить подписку на комментарии
	private $flood_control = 60; //контроль флуда, в секундах. Время, которое должно пройти между публикациями одного автора. Ноль для отключения.
	private $show_flood_control_timeout = true; //показывать ли при неудачной проверке флуд-контроля оставшееся время.

	private $password = 'admin'; //пароль администратора. Рекомендуется сменить после установки.
	private $salt = '8f56eeedf73175082gg8f4c4fceef4f86'; //секретный ключ шифрования. Желательно сменить перед началом использования скрипта.
	private $query = 'primer,test'; //переменные из запроса, которые могут определять уникальность страницы (через запятую)
	private $rating = true; //включение оценок сообщений

	private $max_length = 1024; //максимальная длинна сообщения (0 для отключения)
	private $cpp = 5; //комментариев на страницу
	private $gravatar_size = '60'; //размер граватара к комменту.
	private $gravatar_default = 'mm'; //путь картинки по умолчанию для граватара (оставьте пустые кавычки, если нужно использовать родную дефолтную картинку граватара)
	private $timedate_format = 'd.m.Y'; //формат времени комментария (дата + время: "d.m.Y H:i:s")
	//по умолчанию все комменты сортируются по дате добавления - сначала старые, потом свежие.
	private $total_reverse = false; //реверс последовательности всего списка комментариев
	private $page_reverse = false; //реверс комментов на странице
	private $from_last_page = true; //показывать последнюю страницу комментариев
	private $pagination_top = true; //отображать пагинацию сверху списка комментов
	private $pagination_bottom = true; //отображать пагинацию снизу
	//уведомления о новых комментариях отправляются только при включенной премодерации
	private $mail_subject = 'Комментарий к сайту'; //заголовок письма с уведомлением о новом комментарии
	private $mail_target = 'sample@email.ru'; //адреса, на которые будут отправляться уведомления (через запятую)
	private $mail_sender_name = 'Нотификатор'; //имя отправителя

	private $answer_allowed = true; //разрешить отвечать на комментарии
	private $admin_answer_only = false; //отвечать на комменты может только администратор
	private $answer_only_top = false; //разрешено отвечать только на комменты первого уровня

	/*
	Настройки дополнительных полей формы (пример)
	Каждый вложенный массив - одно поле
	private $extra_fields = array(
	    array(
			'name' => 'city',       // имя поля ввода латинскими буквами без пробелов. Используется в формировании стилевых классов и как ключ для хранения внутри комментария.
			'title' => 'Город',     // отображаемое название поля ввода
			'required' => false,    // обязательность заполнения при добавлении комментария true|false
			'public' => true        // публикация вместе с комментарием true|false
		),
		array(
			'name' => 'phone',
			'title' => 'Телефон',
			'required' => false,
			'public' => false
		)
	);
	 */
	private $extra_fields = array();

	function __construct($ref = false){
		if($ref) {
			$this->ref = $this->make_ref($ref);
		}

		//задаем константы
		define('RPATH', realpath($_SERVER['DOCUMENT_ROOT']).'/');
		define('STORE', RPATH.$this->store);

		$this->post = filter_var_array($_REQUEST);
		if(get_magic_quotes_gpc()){
			foreach($this->post as $key=>$value) $this->post[$key] = stripslashes($value);
		}

		//
		$operation = 'op_'.$this->post['op'];
		if(method_exists($this, $operation)){
			$this->$operation();
		}

	}

	//волшебный метод ;)
	//
	function &__get($name){
		if(property_exists(__CLASS__,$name)){
			return $this->$name;
		} else {
			switch($name){

				case 'page':
					//соблюдение стандарта о нумерации страниц:
					//"пользователь нумерует от 1, в системе - от 0"
					if(empty($this->post['ecomment_page'])){
						if($this->from_last_page){ //если нужно показывать с последней страницы по умолчанию
							$this->$name = $this->last_page - 1;
							$this->post['ecomment_page'] = $this->last_page;
						} else {
							$this->$name = 0;
							$this->post['ecomment_page'] = 1;
						}
					} else {
						$this->$name = $this->post['ecomment_page'] - 1;
					}
					return $this->$name;

				case 'http_ref':
					if(isset($this->post['http_ref'])){
						$this->$name = $this->post['http_ref'];
					} else { //аварийный случай. Может случиться при серверной интеграции
						$this->$name = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					}
					return $this->$name;

				case 'ref':
					$this->$name = $this->make_ref();
					return $this->$name;

				case 'user_posted':
					$this->user_posted = (!empty($this->post['ecomment_posted']) ? unserialize($this->post['ecomment_posted']) : array());
					return $this->user_posted;

				case 'user_rated':
					$this->user_rated = (!empty($this->post['ecomment_rated']) ? unserialize($this->post['ecomment_rated']) : array());
					return $this->user_rated;

				case 'post':
				case 'err':
				case 'info':
					$this->$name = array();
					return $this->$name;

				case 'is_admin':
					if(!empty($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == $this->salt_word($this->password.$_SERVER['SERVER_NAME'])){
						$this->is_admin = true;
					} else {
						//$this->err[] = 'Вы не авторизированы.';
						$this->is_admin = false;
					}
					return $this->is_admin;

				case 'list':
					$this->list = $this->get_comments($this->ref, false);
					return $this->list;

				case 'total':
					$this->total = $this->get_total();
					return $this->total;

				case 'last_page':
					if($this->is_admin){
						$total = $this->total['total'] - $this->total['answers'];
					} else {
						$total = $this->total['moderated'] - $this->total['moderated_answers'];
					}
					$this->last_page = (int) ceil($total / $this->cpp);
					return $this->last_page;

				case 'subscribes':
					//достаточно дернуть чтение списка комментов, во время которого формируется список подписок.
					if(empty($this->list)){
						$this->$name = array();
					}
					return $this->$name;

				default:
					$this->err[] = 'Обращение к незаданной переменной '.$name;
					break;
			}
		}
	}

//
//  основные рабочие методы
//

	/**
	 * инициализация гостевой или просто вывод списка комментов + форма
	 */
	function op_init(){
		exit(json_encode(array(
			'list'=>$this->render_list($this->ref),
			'info'=>$this->render_info($this->ref),
			'desktop'=>$this->render_form($this->ref)
		)));
	}

	/**
	 * получение списка комментариев (без обновления формы, экономим трафик)
	 */
	function op_get_list(){
		exit(json_encode(array(
			'list'=>$this->render_list(),
			'info'=>$this->render_info()
		)));
	}

	/**
	 * авторизация. Принимает из _POST пароль и сравнивает с настройками. При успешном сравнении добавляет "соленые" куки пользователю.
	 */
	function op_login(){
		$list = '';
		if($this->post['password'] == $this->password){
			$_COOKIE['is_admin'] = $this->salt_word($this->password.$_SERVER['SERVER_NAME']);
			setcookie('is_admin', $this->salt_word($this->password.$_SERVER['SERVER_NAME']), 0);
			$this->is_admin = true;
			$this->info[] = 'Вы успешно авторизированы.';
			$list = $this->render_list();
		} else {
			$this->err[] = 'Неверный пароль администратора.';
		}
		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info(),
			'desktop'=>$this->render_form()
		)));
	}

	/**
	 * метод выхода (разлогинивание). Очищает метку логина в текущем сеансе и в куках пользователя.
	 */
	function op_logout(){
		$list = '';
		if(isset($_COOKIE['is_admin'])){
			unset($_COOKIE['is_admin']);
			$this->is_admin = false;
			setcookie('is_admin', '', 0);
			$list = $this->render_list();
			$this->info[] = 'Вы успешно разлогинились.';
		} else {
			$this->err[] = 'Вы не были авторизованы.';
		}
		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info(),
			'desktop'=>$this->render_form()
		)));
	}

	/**
	 * добавление нового комментария
	 */
	function op_add_comment(){
		$comment = array(
			'name'=>htmlspecialchars(trim($this->post['name'])),
			'email'=>htmlspecialchars(trim($this->post['email'])),
			'message'=>$this->post['message'],
			'moderated'=>!$this->moderate,
			'date' => time(),
			'key' => $this->get_timeid(),
			'rating' => 0,
			'parent' => preg_replace('/[^\d\.]*/iu', '', $this->post['parent']),
			'is_admin'=> $this->is_admin
		);

		//обрабатываем дополнительные кастомные поля
		if(!empty($this->extra_fields)){
			foreach($this->extra_fields as $field){
				$comment[$field['name']] = htmlspecialchars(trim($this->post[$field['name']]));
				if($field['required'] && empty($comment[$field['name']])){
					$this->err[] = 'Поле "'.$field['title'].'" не должно быть пустым.';
				}
			}
		}

		//проверки на корректность ввода
		if(!$comment['name']){
			$this->err[] = 'Имя комментатора не должно быть пустым.';
		}
		if(!filter_var($comment['email'],FILTER_VALIDATE_EMAIL)){
			$this->err[] = 'Введен некорректный электронный адрес.';
		}
		if(!$comment['message']){
			$this->err[] = 'Необходимо ввести текст комментария.';
		}
		if($this->max_length && (mb_strlen($comment['message'], 'UTF-8') > $this->max_length)){
			$this->err[] = 'Длинна комментария не должна превышать <b>'.$this->max_length.'</b> символов.';
		} else $comment['message'] = nl2br(htmlspecialchars(trim($this->post['message'])));

		if($this->post['e-mail']){
			$this->err['spam'] = 'Вы не прошли бот-проверку.';
		}
		if(empty($this->post[$this->salt_word($this->ref.$this->post['ecomment_start'])])){
			$this->err['spam'] = 'Вы не прошли бот-проверку. Попробуйте еще раз.';
		}
		if($this->flood_control && !$this->is_admin){
			if(
				$_COOKIE['last_comment_time'] + $this->flood_control > time() ||
				($last_comment = $this->find_last_comment($comment['email'])) && $last_comment['date'] + $this->flood_control > time()
			){
				if($this->show_flood_control_timeout){
					$text_min = array('минуту','минуты','минут');
					$text_sec = array('секунду','секунды','секунд');

					$timeout = $_COOKIE['last_comment_time'] + $this->flood_control - time();
					$timeout_min = floor($timeout / 60);
					$timeout_sec = $timeout % 60;

					$timeout_str = ($timeout_min ? $timeout_min.' '.$this->num_conjugation($timeout_min, $text_min).' и ' : '').$timeout_sec.' '.$this->num_conjugation($timeout_sec, $text_sec).'.';

					$this->err[] = 'Вы слишком часто оставляете комментарии. Подождите еще '.$timeout_str;
				} else {
					$this->err[] = 'Вы слишком часто оставляете комментарии. Попробуйте еще раз через несколько минут.';
				}
			}
		}

		//если не было ошибок, то сохраняем
		if(!sizeof($this->err)){

			//отправляем метку последнего комментария в куки
			if($this->flood_control){
				setcookie('last_comment_time', time());
			}

			//обрабатываем подписку
			if($this->subscribe_allowed){
				$subscribe = $this->post['subscribe'] ? true : false;
				$this->subscribe_email($comment['email'], $subscribe);
				if($subscribe) {
					setcookie('ecomment_subscribe', true);
					$_COOKIE['ecomment_subscribe'] = true;
				} else {
					unset($_COOKIE['ecomment_subscribe']);
					setcookie('ecomment_subscribe', false, 0);
				}
			}

			//регистрируем ответ у родительского сообщения
			if($comment['parent']){
				if($parent = $this->get_comment($comment['parent'])){
					$this->list[$parent['key']]['childs'][] = $comment['key'];
				}
			}
			$this->list[$comment['key']] = $comment; //добавляем сам новый коммент
			if($this->save_comments($this->ref, $this->list)){
				$this->info[] = 'Ваш комментарий успешно добавлен.';
				if($this->moderate) {
					$this->info[] = 'Комментарий появится в общем списке сразу же после одобрения модератором.';
				}
				if($this->notify){
					$this->comment_notify($comment);
				}
				if($this->subscribe_allowed && !$this->moderate){
					$this->comment_notify($comment, $this->subscribes, false);
				}
				$this->user_posted[] = $comment['key'];
				setcookie('ecomment_posted', serialize($this->user_posted), 0x7FFFFFFF);
				unset(
				$this->post['message'],
				$this->post['parent']
				); //чистим то, что не должно больше запоминаться
			}

		} else {
			$this->err[] = 'Сообщение не было сохранено. Заполните все поля корректно.';
		}
		exit(json_encode(array(
			'list'=>$this->render_list(),
			'info'=>$this->render_info(),
			'desktop'=>$this->render_form()
		)));
	}

	/**
	 * удаление комментария по $_POST['id']
	 */
	function op_delete_comment(){
		$list = '';
		if($this->is_admin){
			if($comment = $this->get_comment($this->post['id'])){
				if(!empty($comment['parent'])){ //вычищаем упоминание об удаляемом комменте у его родителя (если есть родитель)
					if($parent = $this->get_comment($comment['parent'], false)){
						unset($parent['childs'][array_search($comment['key'], $parent['childs'])]);
						$this->list[$parent['key']] = $parent;
					}
				}
				if(!empty($comment['childs'])){ //вычищаем у дочерних ответов инфу о родителе (если есть дочерние)
					foreach($comment['childs'] as $child){
						$parent = ($comment['parent'] ? $comment['parent'] : '');
						$this->list[$child]['parent'] = $parent; //переписываем все дочерние ответы родителю удаляемого ответа
						if($this->get_comment($parent, false)){
							$this->list[$parent]['childs'][] = $child;
						}
					}
				}
				unset($this->list[$comment['key']]);
				if($this->save_comments($this->ref, $this->list)){
					$this->info[] = 'Комментарий успешно удален.';
					$list = $this->render_list();
				}
			}
		}
		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info()
		)));
	}

	/**
	 * toggle статуса промодерированности комментария по $_POST['id']
	 */
	function op_moderate_comment(){
		$list = '';
		if($this->is_admin){
			if(isset($this->list[$this->post['id']])){
				$this->list[$this->post['id']]['moderated'] = !$this->list[$this->post['id']]['moderated'];
				if($this->save_comments($this->ref, $this->list)){
					$this->info[] = 'Комментарий успешно промодерирован.';
					$list = $this->render_list();
					//обработка подписки: если разрешена и включено премодерирование и коммент одобрен
					if($this->subscribe_allowed && $this->moderate && $this->list[$this->post['id']]['moderated']){
						$this->comment_notify($this->list[$this->post['id']], $this->subscribes, false);
					}
				}
			}
		}
		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info()
		)));
	}

	/**
	 * админская пометка коммента по $_POST['id']
	 */
	function op_admin_marker(){
		$list = '';
		if($this->is_admin){
			if(isset($this->list[$this->post['id']])){
				$this->list[$this->post['id']]['is_admin'] = !$this->list[$this->post['id']]['is_admin'];
				if($this->save_comments($this->ref, $this->list)){
					$list = $this->render_list();
				}
			}
		}
		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info()
		)));
	}

	/**
	 * повышение рейтинга комментария
	 */
	function op_rate_up(){
		$list = '';

		if(isset($this->list[$this->post['id']])){
			if($this->can_rate($this->post['id'], true)){
				$comment = $this->list[$this->post['id']];
				$comment['rating'] = (!isset($comment['rating']) ? $comment['rating']+1 : 1);
				$this->list[$this->post['id']] = $comment;
				$this->user_rated[] = $comment['key'];
				$this->user_rated = array_unique($this->user_rated);
				if($this->save_comments($this->ref, $this->list)){
					setcookie('ecomment_rated', serialize($this->user_rated), 0x7FFFFFFF);
					$list = $this->render_list();
				}
			}
		}

		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info()
		)));
	}

	/**
	 * понижение рейтинга комментария
	 */
	function op_rate_down(){
		$list = '';

		if(isset($this->list[$this->post['id']])){
			if($this->can_rate($this->post['id'], true)){
				$comment = $this->list[$this->post['id']];
				$comment['rating'] = (!isset($comment['rating']) ? $comment['rating']-1 : -1);
				$this->list[$this->post['id']] = $comment;
				$this->user_rated[] = $comment['key'];
				$this->user_rated = array_unique($this->user_rated);
				if($this->save_comments($this->ref, $this->list)){
					setcookie('gb_rated', serialize($this->user_rated), 0x7FFFFFFF);
					$list = $this->render_list();
				}
			}
		}

		exit(json_encode(array(
			'list'=>$list,
			'info'=>$this->render_info()
		)));
	}

	/**
	 * редактирование полей комментария
	 */
	function op_update_comment(){
		if($this->is_admin){
			if($comment = $this->get_comment($this->post['id'])){
				$new = array();
				if($this->post['name'])     $new['name'] = trim($this->post['name']);
				if($this->post['message'])  $new['message'] = strip_tags(trim($this->post['message']), '<br>');
				if($this->post['date'])     $new['date'] = strtotime(trim($this->post['date']));

				//обработка дополнительных кастомных полей
				if(!empty($this->extra_fields)){
					foreach($this->extra_fields as $field){
						if(isset($this->post[$field['name']])){
							$new[$field['name']] = strip_tags(trim($this->post[$field['name']]));
						}
					}
				}

				$comment = array_merge($comment, $new);
				if($this->save_comment($this->ref, $comment)){
					$this->info[] = 'Комментарий успешно обновлен.';
				}
			}
		} else $this->err[] = 'У вас недостаточно прав чтобы редактировать комментарии.';
		exit(json_encode(array(
			'info'=>$this->render_info()
		)));
	}

	/**
	 * получение информации о списке комментов (количество комментов). Возвращает ассоциативный массив счетчиков:
	 * ref - идентификатор страницы, для которой берутся счетчики.
	 * total - комментариев всего.
	 * moderated - количество промодерированных комментариев.
	 * answers - количество всех комментов, являющихся ответами на другие комменты.
	 * moderated_answers - количество промодерированных ответов.
	 * @param string $ref имя (идентификатор) страницы, для которой вычисляются значения счетчиков
	 * @return array
	 */
	function get_total($ref = ''){
		if(!$ref) $ref = $this->ref;
		$this->list = $this->get_comments($ref, false);
		$total = array(
			'ref' => $ref,
			'total'=>sizeof($this->list),
			'moderated'=>0,
			'answers'=>0,
			'moderated_answers'=>0
		);
		foreach($this->list as $comment){
			if($comment['moderated']) $total['moderated']++;
			if($comment['parent']) $total['answers']++;
			if($comment['parent'] && $comment['moderated']) $total['moderated_answers']++;
		}
		return $total;
	}

	/**
	 * AJAX-интерфейс для получения информации о количестве комментов на странице
	 * @param string $ref идентификатор страницы (по умолчанию идентификатор страницы, с которой был отправлен запрос)
	 */
	function op_get_total($ref = ''){
		$ref = $ref ? $ref : $this->ref;
		exit(json_encode($this->get_total($ref)));
	}

	/**
	 * сортировка ассоциативного массива элементов по указанному ключу.
	 * @param $array массив элементов для сортировки.
	 * @param $key ключ, по которому сортируются вложенные элементы.
	 * @param string $direct направление сортировки ASC|DESC (ASC def.)
	 * @return array отсортированный по ключу массив.
	 */
	function array_sort($array, $key, $direct = 'ASC'){
		$tmp = array();
		foreach($array as $row){
			$k = $row[$key];
			while(isset($tmp[$k])){
				$k = (is_int($k) ? ++$k : $k.= '-');
			}
			$tmp[$k] = $row;
		}
		if($direct == 'ASC') ksort($tmp); else krsort($tmp);
		return array_values($tmp);
	}
//
//  методы рендера
//

	//рендер пагинации
	//принимает значения: количество элементов всего, текущая страница, элементов на страницу, строка GET или массив параметров для ссылки на страницу
	//страница - принимаем системное значение (счет от нуля), а возвращаем - пользовательское (от 1)
	/**
	 * Рендер блока пагинации
	 * @param int $count расчетное количество элементов (всего).
	 * @param int $current текущая активная страница в последовательности пагинации (нумерация от 0)
	 * @param int $cpp количество элементов на страницу
	 * @param string|array $options дополнительные параметры для ссылок пагинации
	 * @return string HTML-разметка под пагинацию
	 */
	function render_pagination($count = 1, $current = 0, $cpp = 1, $options = ''){
		if(!$count || $count<=$cpp){
			return '';
		}
		if(is_array($options)){
			$tmp = '';
			foreach($options as $key=>$val){
				$tmp.= '&'.$key.'='.$val;
			}
			$options = $tmp;
		}

		$first = $prev = $next = $last = false;
		$page_count = ceil($count / $cpp);
		//начальная точка
		$start  = $current - 3;
		if($start >= 1) { $prev = true; $first = true; }
		if($start < 1) $start = 0;
		//конечная точка
		$end    = $current + 3;
		if($end < ($page_count-1)) { $next = true; $last = true; }
		if($end >= $page_count) $end = $page_count-1;

		$echo = '<div class="pagination"><small>Страницы'.(($page_count>11)?' (всего '.$page_count.')':'').':</small><br />';
		if($first) $echo.= '<a href="?ecomment_page=1'.$options.'" class="first">первая</a>';
		if($prev) $echo.= '<a href="?ecomment_page='.$current.$options.'" class="prev">&laquo;</a> ... ';

		for($i = $start; $i <= $end; $i++){
			$echo.= '<a href="?ecomment_page='.($i+1).$options.'" '.(($i==$current)?'class="active"':'').'>'.($i+1).'</a>';
		}

		if($next) $echo.= ' ... <a href="?ecomment_page='.($current+2).$options.'" class="next">&raquo;</a>';
		if($last) $echo.= '<a href="?ecomment_page='.$page_count.$options.'" class="last">последняя</a>';

		$echo.= '</div>';
		return $echo;
	}

	/**
	 * рендер списка комментариев (основная логика)
	 * @param bool $ref идентификатор страницы. Если не указан или False, то используется идентификатор текущей загруженной страницы
	 * @param bool $log вывод сообщений\ошибок рендера
	 * @return string HTML-разметка списка комментариев
	 */
	function render_list($ref = false, $log = true){
		$echo = '';

		if($ref) {
			$ref = $this->make_ref($ref);
			$this->list = $this->get_comments($ref, false);
		}
		$count = $this->get_total();

		if(!$count['total']) $this->info[] = 'Для текущей страницы нет комментариев';

		//сортируем по дате
		$this->list = $this->array_sort($this->list, 'date');

		//историческая сортировка - старые сообщения на последних страницах
		if($this->total_reverse) $this->list = array_reverse($this->list, true);

		//восстанавливаем ключи
		foreach($this->list as $val){
			$list[$val['key']] = $val;
		}
		$this->list = $list;
		//return '<pre>'.var_export($list, true).'</pre>';

		if(!$list) return ' ';

		//фильтруем, оставляя только исходные комментарии (без ответов) в любом случае, чтобы не дублировались
		foreach($list as $comment){
			if($comment['parent'] && $this->get_comment($comment['parent']))
				unset($list[$comment['key']]);
		}

		//фильтруем, если есть необходимость, от не прошедших модерацию комментов
		if(!$this->is_admin && $this->moderate){
			$filtered = array();
			foreach($list as $key => $comment)
				if(!$comment['moderated']) unset($list[$key]);
		}

		//включаем пагинацию
		$count = sizeof($list);
		$options = array('op' => 'get_list');
		if($this->pagination_top) $echo.= $this->render_pagination($count, $this->page, $this->cpp, $options); //верхняя пагинация


		//обрезаем лишние сообщения
		$list = array_slice($list, $this->page*$this->cpp, $this->cpp);

		//реверс сообщений на странице - сверие вверху (двойное отрицание ибо один реверс уже был)
		if($this->page_reverse) $list = array_reverse($list, true);

		//перебор списка с комментариями
		foreach($list as $comment){
			if($this->is_admin || $comment['moderated'] || !$this->moderate)
				$echo.= $this->render_comment($comment, $log);
		}
		if($this->pagination_bottom) $echo.= $this->render_pagination($count, $this->page, $this->cpp, $options); //нижняя пагинация
		return $echo;
	}

	/**
	 * Рендер одного конкретного комментария (для последующего использования внутри списка комментариев)
	 * @param array $comment массив с данными по комментарию
	 * @param bool $log отображение возможных ошибок во время рендера
	 * @return string HTML-разметка одного комментария
	 */
	function render_comment($comment, $log = true){
		$control = '';
		$ecomment_editable = ($this->is_admin ? 'ecomment_editable' : '');
		if($this->is_admin){
			$control = '<div class="ecomment_control">
                <a href="?op=moderate_comment&id='.$comment['key'].($this->post['ecomment_page'] ? '&ecomment_page='.$this->post['ecomment_page'] : '').'" class="ecomment_op">'.($comment['moderated'] ? 'скрыть' : 'утвердить').'</a>
                &nbsp;|&nbsp;
                <a href="?op=delete_comment&id='.$comment['key'].($this->post['ecomment_page'] ? '&ecomment_page='.$this->post['ecomment_page'] : '').'" class="ecomment_op">удалить</a>
            </div>';
		}

		$rating = '';
		if($this->rating){
			$rating = '
            <div class="ecomment_comment_rating">
                <a href="?op=rate_up&id='.$comment['key'].($this->post['ecomment_page'] ? '&ecomment_page='.$this->post['ecomment_page'] : '').'" title="Повысить рейтинг" class="ecomment_rate_link ecomment_rate_up ecomment_op">+</a>
                <span class="ecomment_rating_value'.($comment['rating'] < 0 ? ' negative': '').'" title="Рейтинг сообщения"> '.$comment['rating'].' </span>
                <a href="?op=rate_down&id='.$comment['key'].($this->post['ecomment_page'] ? '&ecomment_page='.$this->post['ecomment_page'] : '').'" title="Понизить рейтинг" class="ecomment_rate_link ecomment_rate_down ecomment_op">-</a>
            </div>';
		}

		$extra_fields = '';
		if(!empty($this->extra_fields)){
			foreach($this->extra_fields as $field){
				if($field['public']){
					$extra_fields.= '
					<span class="ecomment_extra_field ecomment_'.$field['name'].'">
						<span class="ecomment_extra_field_title">'.$field['title'].':</span>
						<span class="ecomment_extra_field_value '.$ecomment_editable.'" rel="'.$field['name'].'">'.(empty($comment[$field['name']]) ? 'не указано' : $comment[$field['name']]).'</span>
					</span>';
				}
			}
		}
		if($extra_fields){
			$extra_fields = '<div class="ecomment_extra_fields">'.$extra_fields.'</div>';
		}

		$answer = '<small class="ecomment_answer_control">';

		if($this->answer_allowed && (!$this->answer_only_top || ($this->answer_only_top && empty($comment['parent'])) || !$this->admin_answer_only))
			$answer.= '<a href="?id='.$comment['key'].'" class="ecomment_answer_link ecomment_control_icon" title="Ответить на комментарий">ответить</a>&nbsp;';
		if($this->is_admin)
			$answer.= '<a href="mailto:'.$comment['email'].'" title="Ответить письмом на '.$comment['email'].'" class="ecomment_mailto_link ecomment_control_icon">email</a>&nbsp;';
		if($this->is_admin)
			$answer.= '<a href="?op=admin_marker&id='.$comment['key'].($this->post['ecomment_page'] ? '&ecomment_page='.$this->post['ecomment_page']: '').'" class="ecomment_control_icon ecomment_isadmin_link ecomment_op '.($comment['is_admin'] ? '' : 'ecomment_opacity').'" title="'.($comment['is_admin'] ? 'Снять админскую метку' : 'Поставить админскую метку').'">Сообщение администратора</a>';
		$answer.= '</small>';

		$echo = '
            <div id="ecomment_'.$comment['key'].'" rel="'.$comment['key'].'" class="ecomment '.($comment['moderated'] ? 'moderated' : 'unmoderated').' '.($comment['is_admin'] ? 'admin' : '').'">
                <div class="ecomment_avatar"><img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($comment['email']))).'?s='.$this->gravatar_size.'&d='.urlencode($this->gravatar_default).'"/></div>
                <div class="ecomment_date '.$ecomment_editable.'" rel="date">'.date($this->timedate_format, $comment['date']).'</div>
                '.$rating.'
                <div class="ecomment_title">
                    <span class="ecomment_name '.$ecomment_editable.'" rel="name">'.$comment['name'].'</span>'.$answer.'
                </div>
                '.$extra_fields.'
                <div class="ecomment_message '.$ecomment_editable.'" rel="message">'.$comment['message'].'</div>
                '.$control.'
            </div>
        ';

		if(!empty($comment['childs'])){
			$echo.= '<div class="ecomment_answers">';
			foreach($comment['childs'] as $key){
				if($child = $this->get_comment($key, true)){
					if($this->is_admin || $child['moderated'] || !$this->moderate){
						$echo.= $this->render_comment($child , $log );
					}
				}
			}
			$echo.= '</div>';
		}
		return $echo;
	}

	/**
	 * рендер информационных сообщений
	 * @return string HTML-разметка информационных сообщений, накопившихся в системе. Если их нет, возвращает пустую строку.
	 */
	function render_info(){
		$err = $info = '';
		if($this->err)  $err  = '<div class="ecomment_err">'.implode('<br />', $this->err).'</div>';
		if($this->info) $info = '<div class="ecomment_info">'.implode('<br />', $this->info).'</div>';
		return $err.$info;
	}

	/**
	 * Рендер формы для добавление нового комментария
	 * @param str $ref альтернативный идентификатор страницы (url или произвольная строка)
	 * @param str $http_ref альтернативный адрес страницы (url страницы запроса). Если не уверены, оставьте пустым.
	 * @return string HTML-разметка формы комментирования
	 */
	function render_form($ref = false, $http_ref = false){
		if($ref){
			$this->ref = $this->make_ref($ref);
		}
		if($http_ref){
			$this->http_ref = $http_ref;
		}
		$start = $this->get_timeid();
		//избавляемся от error Notice
		$this->post['parent']   = (empty($this->post['parent']) ? '' : $this->post['parent']);
		$this->post['name']     = (empty($this->post['name']) ? '' : $this->post['name']);
		$this->post['email']    = (empty($this->post['email']) ? '' : $this->post['email']);
		$this->post['message']  = (empty($this->post['message']) ? '' : $this->post['message']);

		//формируем дополнительные кастомные поля
		$extra_fields = '';
		if(!empty($this->extra_fields)){
			foreach($this->extra_fields as $field){
				$extra_fields.= '
					<dt>'.$field['title'].':</dt>
					<dd><input type="text" name="'.$field['name'].'" '.($field['required'] ? 'required':'').' value="'.$this->post[$field['name']].'" class="ecomment_form_'.$field['name'].'"/></dd>
				';
			}
		}

		return '
        <h2>Оставить комментарий</h2>
        <form method="post" class="ecomment_form">
            <input type="hidden" name="op" value="add_comment"/>
            <input type="hidden" name="http_ref" value="'.$this->http_ref.'"/>
            <input type="hidden" name="ecomment_start" value="'.$start.'"/>
            <input type="hidden" name="ecomment_page" value="'.$this->post['ecomment_page'].'"/>
            <input type="hidden" name="parent" value="'.$this->post['parent'].'"/>
            <div class="ecomment_form_login"><noindex>'.($this->is_admin ? '<a href="?op=logout" class="ecomment_op" rel="nofollow">logout</a>' : '<a href="?op=login" class="ecomment_op" rel="nofollow">login</a>').'</noindex></div>
            <dl>
                <dt>Имя:</dt>
                <dd><input type="text" name="name" required class="ecomment_form_name" value="'.htmlspecialchars($this->post['name']).'"/><span class="ecomment_answer_caption"></span></dd>

                <dt>Email:</dt>
                <dd>
                    <input type="email" name="email" required class="ecomment_form_email" value="'.htmlspecialchars($this->post['email']).'"/>
                    <input type="text" name="e-mail" value=""/>
                </dd>

                '.$extra_fields.'

                <dt>Комментарий:</dt>
                <dd>
                    <textarea name="message" class="ecomment_form_message" maxlength="'.$this->max_length.'">'.$this->post['message'].'</textarea>
                    <input type="text" name="ecomment_counter" readonly class="ecomment_counter" value="'.$this->max_length.'"/>
                </dd>
				'.($this->subscribe_allowed ? '
                <dt></dt>
                <dd>
                	<input type="checkbox" name="subscribe" '.($_COOKIE['ecomment_subscribe'] ? 'checked' : '').' /> - подписаться на обновления
                </dd>' : '').'

                <dt>&nbsp;</dt>
                <dd>
                    <input type="submit" class="ecomment_form_submit" value="Добавить"/>
                    <a href="http://ecomment.su" class="ecomment_version">eComment v.'.$this->version.'</a>
                </dd>
            </dl>
        </form>
        <script language="JavaScript" type="text/javascript">
            $(".ecomment_form_message").after(\'<br /><input type="checkbox" name="'.$this->salt_word($this->ref.$start).'" class="ecomment_form_not_robot" value="test"/> - я не робот\');
            var ecomment_counter = '.$this->max_length.'
        </script>
        ';
	}


//
//  методы ЧТЕНИЯ и СОХРАНЕНИЯ в файловой системе
//

	/**
	 * чтение списка комментариев по идентификатору страницы
	 * @param string $ref идентификатор страницы
	 * @param bool $log отображение возможных ошибок
	 * @return array массив комментариев или пустой массив в случае ошибок чтения
	 */
	private function get_comments($ref, $log = true){
		$ref = $this->translit($ref);
		if($list = $this->read_data($ref, $log)){
			$this->subscribes = isset($list['subscribes']) && !empty($list['subscribes']) ? $list['subscribes'] : array();
			unset($list['subscribes']);
			return $list;
		} else {
			return array();
		}
	}

	/**
	 * выбор определенного коммента из текущего списка комментариев
	 * @param $key идентификатор коммента
	 * @param bool $log вывод ошибок
	 * @return bool|array массив с данными комментария или false в случае ошибки (если коммент не найден)
	 */
	private function get_comment($key, $log = true){
		if(isset($this->list[$key])){
			return $this->list[$key];
		} else {
			if($log) $this->err[] = 'В текущем списке нет указанного комментария "'.$key.'".';
			return false;
		}
	}

	/**
	 * сохранение одного комментария в базе конкретной страницы
	 * @param $ref идентификатор страницы
	 * @param $comment массив с данными комментария
	 * @param bool $log вывод ошибок
	 * @return bool
	 */
	private function save_comment($ref, $comment, $log = true){
		if(!$list = $this->read_data($ref, false)){
			$list = array();
		}
		$list[$comment['key']] = $comment;
		return $this->save_data($ref, $list, $log);
	}

	/**
	 * сохранение базы комментариев (всего списка по странице)
	 * @param string $ref идентификатор страницы
	 * @param array $list массив комментариев
	 * @param bool $log вывод ошибок
	 * @return bool
	 */
	private function save_comments($ref, $list, $log = true){
		$list['subscribes'] = $this->subscribes;
		return $this->save_data($ref, $list, $log);
	}


	/**
	 * чтение .dat-файлов с сериалиализованными данными из хранилища STORE.
	 * @param $name имя файла для чтения (без расширения).
	 * @param bool $log вывод ошибок.
	 * @return bool|mixed десериализованные данные или false в случае ошибок чтения или десериализации
	 */
	private function read_data($name, $log = true){
		if(@$data = file_get_contents(STORE.$name.'.dat')){
			$data = unserialize($data);
			if($data !== false){
				return $data;
			} else {
				if($log) $this->err[] = 'Не удалось распаковать данные из файла.';
				return false;
			}
		} else {
			if($log) $this->err[] = 'Не удалось прочесть файл данных "'.$name.'".';
			return false;
		}
	}

	/**
	 * сохранение сериализованных данных в хранилище STORE.
	 * @param $name имя файла для сохранения (без расширения)
	 * @param $data данные для сохранения
	 * @param bool $log вывод ошибок
	 * @return bool
	 */
	private function save_data($name, $data, $log = true){
		if(@file_put_contents(STORE.$name.'.dat', serialize($data))){
			return true;
		} else {
			if($log) $this->err[] = 'Не удалось сохранить файл данных с комментариями.';
			if(file_exists(STORE)){
				if(!is_writable(STORE)) if($log) $this->err[] = 'Недостаточно прав доступа к директрории хранения данных.';
			} elseif($log) $this->err[] = 'Указанная директория хранения файлов не существует.';

			return false;
		}
	}

	/**
	 * Управление подпиской на странице.
	 * @param $email адрес для подписки
	 * @param bool $subs статус подписки - подписаться или отписаться
	 * @param bool $log вывод сообщений
	 */
	private function subscribe_email($email, $subs = false, $log = true){
		if($subs){
			//если были подписаны, то ничего не делаем
			if(array_search($email, $this->subscribes) === false){
				$this->subscribes[] = $email;
				if($log) $this->info[] = 'Вы успешно подписаны на обновления комментариев этой страницы.';
			}
		} else {
			//если были подписаны, то отписываемся
			$k = array_search($email, $this->subscribes);
			if($k !== false){
				unset($this->subscribes[$k]);
				if($log) $this->info[] = 'Вы успешно отписаны от обновлений комментариев на странице.';
			}
		}
	}

//
//  Вспомогательные методы
//

	/**
	 * "соленое слово". Хэширует строку используя секретный ключ.
	 * @param $word строка для хеширования.
	 * @return string
	 */
	function salt_word($word){
		return md5(md5($this->salt).md5($word));
	}

	/**
	 * получение метки времени в качестве uid (с микросекундами)
	 * @return string 12-значная цифровая строка
	 */
	protected function get_timeid(){
		$time = microtime(true);
		$time = $time*100; //избавились от дробной части
		return substr($time, 0, 12);
	}

	/**
	 * Транслитерация строки
	 * @param $str строка для транслитерации
	 * @return mixed
	 */
	function translit($str){
		$rp = array("Ґ"=>"G","Ё"=>"YO","Є"=>"Ye","є"=>"ie","Ї"=>"YI","І"=>"I",
			"і"=>"i","ґ"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
			"ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
			"Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
			"Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
			"О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
			"У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"Ts","Ч"=>"Ch",
			"Ш"=>"Sh","Щ"=>"Shch","Ъ"=>"'","Ы"=>"Yi","Ь"=>"",
			"Э"=>"E","Ю"=>"Yu","Я"=>"Ya","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"shch","ъ"=>"'",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
			" "=>"_","»"=>"","«"=>""
		);
		$str = strtr($str, $rp);
		return preg_replace('/[^-\d\w]/','',$str);
	}

	/**
	 * Почтовое уведомление администраторам о новом комментарии. Если хотя бы одна отправка провалилась, методо возвращает false.
	 * @param bool $comment массив с данными комментария
	 * @param bool $log вывод ошибок отправки
	 * @return bool
	 */
	protected function comment_notify($comment = false, $emails = false, $log = true){

		//составляем заголовки
		$mailHeaders = "Date: ".date("D, d M Y H:i:s")." UT\r\n";
		$mailHeaders.= "Subject: =?UTF-8?B?".base64_encode($this->mail_subject)."?=\r\n";
		$mailHeaders.= "MIME-Version: 1.0\r\n";
		$mailHeaders.= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
		$mailHeaders.= "Content-Transfer-Encoding: 8bit\r\n";
		$mailHeaders.= "From: =?UTF-8?B?".base64_encode($this->mail_sender_name)."?= <".$this->mail_target.">\r\n";
		$mailHeaders.= "X-Priority: 3";
		$mailHeaders.= "X-Mailer: PHP/".phpversion()."\r\n";

		//формируем обратную ссылку на страницу, с которой был отправлен комментарий
		$http_ref = parse_url($this->http_ref);
		parse_str($http_ref['query'],$http_ref['query']); //отдельно обрабатываем параметры, чтобы добавить еще один
		$http_ref['ecomment_page'] = $this->post['ecomment_page'];
		$http_ref['scheme'] = empty($http_ref['scheme']) ? 'http' : $http_ref['scheme'];
		$http_ref = $http_ref['scheme'].'://'.$http_ref['host'].$http_ref['path'].'?'.http_build_query($http_ref['query']);

		//используем человеко-понятное название страницы либо копию обратной ссылки
		$page_title = $this->post['page_title'] ? mb_convert_encoding($this->post['page_title'], 'UTF-8', 'auto') : $http_ref;

		$mailBody = 'На странице <a href="'.$http_ref.'#ecomment_list">'.$page_title.'</a> оставлен новый комментарий:<br /><br />';
		if($comment){
			$mailBody.= '<b>Автор:</b> '.$comment['name'].'<br />';
			if($emails === false) $mailBody.= '<b>Email:</b> '.$comment['email'].'<br />';
			if(!empty($this->extra_fields)){
				foreach($this->extra_fields as $field){
					if($field['public'] || $emails === false){
						$mailBody.= '<b>'.$field['title'].':</b> '.(empty($comment[$field['name']]) ? 'не указано' : $comment[$field['name']]).'<br/>';
					}
				}
			}
			$mailBody.= '<b>Сообщение:</b> '.$comment['message'].'<br />';
		}
		$result = 1;

		$mail_target = explode(',', $this->mail_target);
		//если это рассылка по кастомным адресам, то исключаем из них админские
		if($emails !== false){
			foreach($mail_target as $mt){
				$k = array_search($mt, $emails);
				if($k !== false) unset($emails[$k]);
			}
		} else {
			$emails = $mail_target;
		}

		foreach($emails as $mail){
				$mail_result = mail(trim($mail), "=?UTF-8?B?".base64_encode($this->mail_subject)."?=", $mailBody, $mailHeaders);
				if(!$mail_result){
					if($log) $this->err[] = 'Не удалость отправить уведомление на почту '.$mail;
				}
				$result*= $mail_result;
		}
		return (bool)$result;
	}

	/**
	 * проверка на разрешение юзеру оценивать определенный комментарий
	 * @param string $key идентификатор комментария
	 * @param bool $log вывод ошибок
	 * @return bool
	 */
	protected function can_rate($key = '', $log = false){
		if(!$this->is_admin){
			if(!in_array($key, $this->user_posted)){ //запрещаем рейтить свои же посты
				if(!in_array($key, $this->user_rated)){ //запрещаем рейтить уже оцененные посты
					if($this->moderate){ //если включена премодерация сообщений, то проверяем доверенность пользователя
						foreach($this->user_posted as $posted){
							if(isset($this->list[$posted]) && $this->list[$posted]['moderated']){
								return true;
							}
						}
						if($log) $this->err[] = 'Оценивать сообщения могут лишь пользователи, оставившие в теме обсуждения хотя бы один одобренный модератором комментарий.';
					} else return true;
				} elseif($log) $this->err[] = 'Вы уже оценивали этот пост.';
			} elseif($log) $this->err[] = 'Авторы не могут оценивать собственные сообщения.';
		} else return true;
		return false;
	}

	/** Формируем идентификатор страницы из параметра или пытаемся восстановить из поста или http_ref
	 * @param string $ref предпологаемые идентификатор страницы (url или просто строка)
	 * @return string
	 */
	protected function make_ref($ref = ''){
		if(empty($ref)){
			$ref = empty($this->post['ref']) ? $this->http_ref : $this->post['ref'];
		}
		$url = parse_url($ref);
		$ref = $this->translit($url['path']);

		if(!empty($this->query) && isset($url['query'])){ //добавляем в ref при любом раскладе параметр
			parse_str($url['query'], $url['query']);
			foreach(explode(',', $this->query) as $query){
				$query = trim($query);
				if(isset($url['query'][$query])) $ref.= '_'.$url['query'][$query];
			}
		}

		return $this->translit($ref);
	}

	/**
	 * Поиск последнего комментария в скписке
	 * @param string $author опциональный параметр, Email автора - поиск последнего комментария автора
	 * @return bool
	 */
	protected function find_last_comment($author = ''){
		$last_comment = false;

		foreach($this->list as $comment){
			if(
				!$author && $comment['date'] > $last_comment['date'] ||
				$author && $comment['email'] == $author && $comment['date'] > $last_comment['date']
			){
				$last_comment = $comment;
			}

		}
		return $last_comment;
	}

	/**
	 * склонение фразы относительно числа
	 * @param int $num число, относительно которого нужно выбрать вариант склонения
	 * @param array $text варианты склонений, массив:
	 * 0 - для чисел, оканчивающихся на 1;
	 * 1 - для оканчивающихся на 2,3,4;
	 * 2 - для оканчивающихся на 5,6,7,8,9,0 + второй десяток.
	 * @return mixed
	 */
	protected function num_conjugation($num = 0, $text = array()){
		if($num%100 > 10 && $num%100 < 20){
			return $text[2];
		} else{
			$num = $num % 10;
			switch($num){
				case 1:
					return $text[0];
				case 2:
				case 3:
				case 4:
					return $text[1];
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 0:
					return $text[2];
			}
		}
	}

}
if($_REQUEST['op']){
	$comment = new ecomment();
}
?>