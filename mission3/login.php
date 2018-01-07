<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", FALSE);
header("Pragma: no-cache");
// セッション開始
session_start();

// エラーメッセージの初期化
$errorMessage = "";
//現在のページ
$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;


// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["userid"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) { 
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
        // 入力したユーザIDを格納
        $userid = $_POST["userid"];

        require_once(dirname(__FILE__) . "/../dbconnect.php");
        $pdo = connect();
                
        // 3. エラー処理
        try {

            $stmt = $pdo->prepare("SELECT * FROM register WHERE userid = ? and register = ?");
            $stmt->execute(array($userid,"perm"));

            $password = $_POST["password"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($password == $row['password']) {//パスワード一致
                    session_regenerate_id(true);

                    // 入力したIDのユーザー名を取得
                    $id = $row['userid'];
                    $_SESSION['userid'] = $row['userid'];
                    $sql = "SELECT * FROM profile WHERE userid = ?";  //入力したIDからユーザー名を取得
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(1, $id, PDO::PARAM_STR);
                    $stmt->execute();                   
                    // SQL の実行
                    foreach ($stmt as $row) {
                    $_SESSION["name"] = $row['name'];  // ユーザー名取得                    
                    }
                    //echo $nextpage."/../top.php";
                    header("Location:".$nowpage."/../top.php");  // メイン画面へ遷移
                    exit();  // 処理終了
                } else {
                    // 認証失敗
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } else {
                // 該当データなし
                $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            //$errorMessage = $sql;
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
             echo $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>ログイン</title>
    </head>
    <body>
        <h1>ログイン画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>ログインフォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <label for="userid">ユーザーID　</label><input type="text"  name="userid" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">
                <br>
                <label for="password">パスワード　</label><input type="password" name="password" value="" placeholder="パスワードを入力">
                <br>
                <input type="submit" id="login" name="login" value="ログイン">
            </fieldset>
        </form>
        <br>
        <form action="<?=$nowpage?>/../mailform.php">
            <fieldset> 
            <legend>初めての方はこちら</legend>   
                <input type="submit" value="新規登録">
            </fieldset>
        </form>
    </body>
</html>
