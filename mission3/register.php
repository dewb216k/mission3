<?php
header("Content-type: text/html; charset=utf-8");
//エラーメッセージの初期化

$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;

require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();

if(empty($_POST)) {
	header("Location:".$nowpage."/../mailform.php");
	exit();
}


try{
    //データベースに接続
	//例外処理を投げる（スロー）ようにする
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $userid = $_POST['userid'];
        $urltoken = $_POST['urltoken'];
        $name = $_POST['name']; //POSTのデータを変数$nameに格納
        $comment = $_POST['comment']; //POSTのデータを変数$commentに格納
        $password = $_POST['password']; //POSTのデータ
        $mail = $_POST['mail'];



        
        $sql = "UPDATE register set  userid = :userid,  password = :password,  register = :register  where urltoken = :urltoken";
        
            // 挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql);
        
            // 挿入する値を配列に格納する
            $params = array( ':userid'=>"$userid", ':password'=>"$password" , ':register'=>"perm",  ':urltoken' => "$urltoken",);
            
            // 挿入する値が入った変数をexecuteにセットしてSQLを実行
            $stmt->execute($params);

        $sql = "INSERT INTO profile (userid, name,comment ) VALUES (:userid, :name, :comment )";
        
            // 挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql);
        
            // 挿入する値を配列に格納する
            $params = array(':userid' => "$userid", ':name'=>"$name" , ':comment'=>"$comment");
            
            // 挿入する値が入った変数をexecuteにセットしてSQLを実行
            $stmt->execute($params);
    
            
    
        //接続終了
        $pdo = null;
    }
 	
 	/*
 	登録完了のメールを送信
 	*/

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
        <title>ユーザ登録完了</title>
    </head>
    <body>
<h1>ユーザ登録完了</h1>

<p>登録完了しました<br /></p>
userid:<?=$userid?><br />name:<?=$name?><br />comment:<?=$comment?><br />
<a href="./login.php">ログイン</a><br />

    </body>
</html>