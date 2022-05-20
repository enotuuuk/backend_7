<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');
$errors = array();
$hasErrors = FALSE;

$defaultValues = [
  'name' => '',
  'email' => '',
  'birthday' => '',
  'gender' => 'O',
  'limbs' => '4',
  'biography' => 'я родился...',
  'contract' => ''
];

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  foreach (['name', 'email', 'birthday', 'contract'] as $key) {
    $errors[$key] = !empty($_COOKIE[$key . '_error']);
    if ($errors[$key] != '')
      $hasErrors = TRUE;
  }

  // Выдаем сообщения об ошибках.
  if ($errors['name']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('name_error', '', 100000);
    // Выводим сообщение.
    $messages[] = 'Заполните имя.<br/>';
  }
  if ($errors['email']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('email_error', '', 100000);
    // Выводим сообщение.
    $messages[] = 'Заполните email.<br/>';
  }
  if ($errors['birthday']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('birthday_error', '', 100000);
    // Выводим сообщение.
    $messages[] = 'Заполните дату рождения.<br/>';
  }

  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  foreach (['name', 'email', 'birthday', 'gender', 'limbs', 'biography', 'contract'] as $key) {
    $values[$key] = !array_key_exists($key . '_value', $_COOKIE) ? $defaultValues[$key] : strip_tags($_COOKIE[$key . '_value']);
  }
  $values['superpowers'] = array();
  $values['superpowers']['0'] = empty($_COOKIE['superpowers_0_value']) ? '' : strip_tags($_COOKIE['superpowers_0_value']);
  $values['superpowers']['1'] = empty($_COOKIE['superpowers_1_value']) ? '' : strip_tags($_COOKIE['superpowers_1_value']);
  $values['superpowers']['2'] = empty($_COOKIE['superpowers_2_value']) ? '' : strip_tags($_COOKIE['superpowers_2_value']);

  session_start();
  if (!empty($_GET['quit'])) {
    session_destroy();
    $_SESSION['login'] = '';
  }

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (!$hasErrors && !empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {

    $user = 'u41031';
    $pass = '1232344';
    $db = new PDO('mysql:host=localhost;dbname=u41031', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    $stmt1 = $db->prepare('SELECT name, email, birthday, gender, limb_number, biography FROM forms WHERE form_id = ?');
    $stmt1->execute([$_SESSION['uid']]);
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    $values['name'] = strip_tags($row['name']);
    $values['email'] = strip_tags($row['email']);
    $values['birthday'] = strip_tags($row['birthday']);
    $values['gender'] = strip_tags($row['gender']);
    $values['limbs'] = strip_tags($row['limb_number']);
    $values['biography'] = strip_tags($row['biography']);

    $stmt2 = $db->prepare('SELECT ability_id FROM form_ability WHERE form_id = ?');
    $stmt2->execute([$_SESSION['uid']]);
    while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
      $values['superpowers'][$row['ability_id']] = TRUE;
    }
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $trimmedPost = [];
  foreach ($_POST as $key => $value)
    if (is_string($value))
      $trimmedPost[$key] = trim($value);
    else
      $trimmedPost[$key] = $value;

  if (empty($trimmedPost['name'])) {
    setcookie('name_error', 'true');
    $hasErrors = TRUE;
  } else {
    setcookie('name_error', '', 10000);
  }
  setcookie('name_value', $trimmedPost['name'], time() + 30 * 24 * 60 * 60);
  $values['name'] = $trimmedPost['name'];

  if (!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $trimmedPost['email'])) {
    setcookie('email_error', 'true');
    $hasErrors = TRUE;
  } else {
    setcookie('email_error', '', 10000);
  }
  setcookie('email_value', $trimmedPost['email'], time() + 30 * 24 * 60 * 60);
  $values['email'] = $trimmedPost['email'];

  if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $trimmedPost['birthday'])) {
    setcookie('birthday_error', 'true');
    $hasErrors = TRUE;
  } else {
    setcookie('birthday_error', '', 10000);
  }
  setcookie('birthday_value', $trimmedPost['birthday'], time() + 30 * 24 * 60 * 60);
  $values['birthday'] = $trimmedPost['birthday'];

  if (!preg_match('/^[MFO]$/', $trimmedPost['gender'])) {
    $hasErrors = TRUE;
  }
  setcookie('gender_value', $trimmedPost['gender'], time() + 30 * 24 * 60 * 60);
  $values['gender'] = $trimmedPost['gender'];

  if (!preg_match('/^[0-5]$/', $trimmedPost['limbs'])) {
    $hasErrors = TRUE;
  }
  setcookie('limbs_value', $trimmedPost['limbs'], time() + 30 * 24 * 60 * 60);
  $values['limbs'] = $trimmedPost['limbs'];

  foreach (['0', '1', '2'] as $value) {
    setcookie('superpowers_' . $value . '_value', '', 10000);
    $values['superpowers'][$value] = FALSE;
  }
  if (array_key_exists('superpowers', $trimmedPost)) {
    foreach ($trimmedPost['superpowers'] as $value) {
      if (!preg_match('/[0-2]/', $value)) {
        $hasErrors = TRUE;
      }
      setcookie('superpowers_' . $value . '_value', 'true', time() + 30 * 24 * 60 * 60);
      $values['superpowers'][$value] = TRUE;
    }
  }
  setcookie('biography_value', $trimmedPost['biography'], time() + 30 * 24 * 60 * 60);
  $values['biography'] = $trimmedPost['biography'];
  if (!isset($trimmedPost['contract'])) {
    $errorOutput .= 'Вы не ознакомились с контрактом.<br/>';
    setcookie('contract_error', 'true');
    $errors['contract'] = TRUE;
    $hasErrors = TRUE;
  } else {
    setcookie('contract_error', '', 10000);
  }

  if ($hasErrors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    
    $user = 'u41031';
    $pass = '1232344';
    $db = new PDO('mysql:host=localhost;dbname=u41031', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    $stmt1 = $db->prepare('UPDATE forms SET name=?, email=?, birthday=?, gender=?, limb_number=?, biography=? WHERE form_id = ?');
    $stmt1->execute([$values['name'], $values['email'], $values['birthday'], $values['gender'], $values['limbs'], $values['biography'], $_SESSION['uid']]);

    $stmt2 = $db->prepare('DELETE FROM form_ability WHERE form_id = ?');
    $stmt2->execute([$_SESSION['uid']]);

    $stmt3 = $db->prepare("INSERT INTO form_ability SET form_id = ?, ability_id = ?");
    foreach ($trimmedPost['superpowers'] as $s)
      $stmt3 -> execute([$_SESSION['uid'], $s]);
  }
  else {
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
    $id = uniqid();
    $hash = md5($id);
    $login = substr($hash, 0, 10);
    $pass = substr($hash, 10, 15);

    //хеш-функция из семейства алгоритмов SHA-2 предназначена для создания «отпечатков» или «дайджестов» для сообщений произвольной длины.
    $pass_hash = substr(hash("sha256", $pass), 0, 20);
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

    $user = 'u41031';
    $pass_db = '1232344';
    $db = new PDO('mysql:host=localhost;dbname=u41031', $user, $pass_db, array(PDO::ATTR_PERSISTENT => true));
    $stmt1 = $db->prepare("INSERT INTO forms SET name = ?, email = ?, birthday = ?, 
      gender = ? , limb_number = ?, biography = ?, login = ?, pass_hash = ?");

    $stmt1 -> execute([$trimmedPost['name'], $trimmedPost['email'], $trimmedPost['birthday'], 
      $trimmedPost['gender'], $trimmedPost['limbs'], $trimmedPost['biography'], $login, $pass_hash]);

    $stmt2 = $db->prepare("INSERT INTO form_ability SET form_id = ?, ability_id = ?");
    
    $id = $db->lastInsertId();
    foreach ($trimmedPost['superpowers'] as $s)
      $stmt2 -> execute([$id, $s]);
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}


