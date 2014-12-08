<?php
/**
 * @var array $data User data
 */
?>
Добро пожаловать на evolveskateboards.ru, <?php echo $data['username']; ?>!

Сейчас вы можете войти на сайт с помощью своих учётных данных:

Логин: <?php echo $data['username'] . "\n"; ?>
Пароль: <?php echo $data['password'] . "\n"; ?>

Информация о вас:

Имя: <?php echo $data['first_name'] . "\n"; ?>
Фамилия: <?php echo $data['last_name'] . "\n"; ?>
Email: <?php echo $data['email'] . "\n"; ?>

С наилучшими пожеланиями.
Команда evolveskateboards.ru