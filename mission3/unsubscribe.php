<?php
session_start();
$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;

// ログイン状態チェック
if (!isset($_SESSION["userid"])) {
    header("Location:".$nowpage."/../logout.php");
    exit;
}

$userid = $_SESSION['userid'];
$name = $_SESSION['name']; 
$password = $_POST['password'];

//メッセージの初期化
$errors =array();

require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();
//接続
try{
//echo $_SESSION["unsubscribe"];
    if ($_SESSION["unsubscribe"]=="yes") {

        $sql = "UPDATE register set password = :password, mail = :mail,  register = :register where userid = :userid";
        
            // 挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql);
        
            // 挿入する値を配列に格納する
            $params = array( ':password'=>"" , ':mail'=>"", ':register'=>"no", ':userid' => "$userid",);
            
            // 挿入する値が入った変数をexecuteにセットしてSQLを実行
            $stmt->execute($params);

        $sql = "UPDATE profile set name = :name,  comment = :comment where userid = :userid";
        
            // 挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql);
        
            // 挿入する値を配列に格納する
            $params = array( ':name'=>"-" , ':comment'=>"", ':userid' => "$userid",);
                
            // 挿入する値が入った変数をexecuteにセットしてSQLを実行
            $stmt->execute($params);

            // セッションの変数のクリア
            $_SESSION = array();

            // セッションクリア
            @session_destroy();
            


    }else{
        unset($_SESSION["unsubscribe"]);
        $errors['set_unsubscribe'] = "以下のページから退会処理を行ってください<br />\n <a href=".$nowpage."/../unsubscribe_confirm.php>退会</a><br /><br />\n";       

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
    <?php if (count($errors) === 0): ?>
        <h1>退会完了画面</h1>

        <p>退会完了しました<br />
        <a href="<?=$nowpage?>/../top.php">TOP</a><br /></p>

    <?php elseif(count($errors) > 0): ?>
 
    <?php
    foreach($errors as $value){
        echo "<p>".$value."</p>";
    }
    ?>
  
<?php endif; ?>

    </body>
</html>