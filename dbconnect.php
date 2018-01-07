<?php
function connect(){
$dsn = 'mysql:dbname='データベース名';host='ホスト';charset=utf8';
$username = 'ユーザー名';
$password = 'パスワード';

$options = array(
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
);
return new PDO($dsn, $username, $password, $options);
}
?>