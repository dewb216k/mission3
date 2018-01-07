<?php
session_start();
$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;

if (isset($_SESSION["name"])) {
    $errorMessage = "ログアウトしました。";
} else { 
    $errorMessage = "セッションがタイムアウトしました。";
}

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
@session_destroy();
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8"> 
        <title>ログアウト</title>
    </head>
    <body>
        <h1>ログアウト画面</h1>
        <div><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></div>
        <ul>
            <li><a href="<?=$nowpage?>/../login.php">ログイン画面に戻る</a></li>
            
        </ul>
    </body>
</html>



