<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 
$_SESSION['token'] =  md5(uniqid(rand(),1));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>新規登録</title>
<meta charset="utf-8">
</head>
<body>
<h1>新規登録</h1>
 <p>メールアドレスを入力し登録ボタンを押してください。登録URLを送信します。</p>
<form action='./sendmail.php' method="post">
 
<p>メールアドレス：<input type="text" name="mail" size="50" required></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="登録する">
 
</form>
 
</body>
</html>