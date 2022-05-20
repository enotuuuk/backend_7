<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">

  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&family=Poppins:wght@500&display=swap" rel="stylesheet"> 

  <title>Задание 6</title>
</head>
<body>
  <nav>
    <ul>
      <li><a href="index.php#form" title = "Форма">Форма</a></li>
      <li>
        <?php 
        if(!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])){
            print('<a href="index.php?quit=1" title = "Выйти">Выйти</a>');
            print('<a href="admin.php" title = "Администрирование"> Администрирование</a>');}
        else
          print('<a href="login.php" title = "Войти">Войти</a>');

        ?>
      </li>
    </ul>
  </nav>
  <div class="main">
    <section id="form">
    <h2>Авторизация</h2>
<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  // Делаем перенаправление на форму.
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (!empty($_GET['nologin']))
    print("<div>Пользователя с таким логином не существует</div>");
  if (!empty($_GET['wrongpass']))
    print("<div>Неверный пароль!</div>");

?>
<form action="" method="post">
  <label>
    Логин:<br />
    <input name="login" />
  </label> <br />
  <label>
    Пароль:<br />
    <input name="pass" />
  </label> <br />
  <input type="submit" value="Войти" />
</form>
<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  $user = 'u41031';
  $pass = '1232344';
  $db = new PDO('mysql:host=localhost;dbname=u41031', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  $stmt1 = $db->prepare('SELECT form_id, pass_hash FROM forms WHERE login = ?');
  $stmt1->execute([$_POST['login']]);
  $row = $stmt1->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    header('Location: ?nologin=1');
    exit();
  }
  
  $pass_hash = substr(hash("sha256", $_POST['pass']), 0, 20);

  if ($row['pass_hash'] != $pass_hash) {
    header('Location: ?wrongpass=1');
    exit();
  }

  $_SESSION['login'] = $_POST['login'];
  // Записываем ID пользователя.
  $_SESSION['uid'] = $row['form_id'];

  // Делаем перенаправление.
  header('Location: ./');
}
?>

</section>
</div>
</body>
</html>
