 <?php

session_start();
$errorMessage = "";

$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;


// ログイン状態チェック
if (!isset($_SESSION["userid"])) {
    header("Location:".$nowpage."/../logout.php");
    exit;
}

$userid = $_SESSION['userid'];
$name = $_SESSION['name'];

require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();

try{
//データベースに接続
       

    // ボタンが押された場合
    if (isset($_POST["unsubscribe"])) {
        //echo $_POST["unsubscribe"]."yes";
        if (empty($_POST["password"])) {
            $errorMessage = 'パスワードが未入力です。';
        }else{
            $sql="SELECT * FROM register WHERE userid = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(1, $userid, PDO::PARAM_STR);
                $stmt->execute();

                $password = $_POST["password"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($password == $row['password']) {//パスワード一致
                    $_SESSION['unsubscribe'] = "yes";
                    header("Location:".$nowpage."/../unsubscribe.php");  // 遷移
                    exit();  // 処理終了
                } else {
                    // 認証失敗
                    $errorMessage = 'パスワードに誤りがあります。';
                }
            } else {
                // 該当データなし
                $errorMessage = '該当データなし';
            }

        }
    }
    //接続終了
    $pdo = null;
}

//接続に失敗した際のエラー処理
catch (PDOException $e){
    print('エラーが発生しました。');
    exit($e->getMessage());
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>退会</title>
    </head>
    <body>
        <p>本当に退会しますか？<br /></p>
        <form action="" method="post" name="form_unsubscribe">
        <fieldset>
		パスワードを入力してください<br />
            <input type="password" name="password" style="ime-mode:disabled;" placeholder="パスワードを入力" required>
            <input type="submit" name="unsubscribe" value="退会">
            <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>

        </fieldset>
        </form>

        <p><a href="<?=$nowpage?>/../top.php">TOP</a></p>



    </body>
</html>