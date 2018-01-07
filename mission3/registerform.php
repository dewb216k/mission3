<?php
 
header("Content-type: text/html; charset=utf-8");
$errors = array();
$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;

if(empty($_GET)) {
	header("Location:".$nowpage."/../top.php");
    exit();
}
    
//GETデータを変数に入れる
$urltoken = isset($_GET['urltoken']) ? $_GET['urltoken'] : NULL;
//メール入力判定
if ($urltoken == ''){
    $errors['urltoken'] = "エラーが発生しました。";
}

require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();


try{
//データベースに接続

    
        //例外処理を投げる（スロー）ようにする
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //id
        $userid =  md5(uniqid(rand(),1));
        $userid =  mb_strimwidth("$userid", 0, 8);

        $sql = "SELECT * from register WHERE userid=?";//useridチェック
        $stmt = $pdo->prepare($sql);         
        $stmt->bindParam(1, $userid, PDO::PARAM_STR);
        // SQL の実行
        $stmt->execute();
        foreach($stmt as $re){
        $idcheck = "no";//被りがあったらno
        }
        if($idcheck=="no"){
            $errors['id_check'] = "エラーが発生しました。再読み込みしてください。";

        }

        $sql = "SELECT mail FROM register WHERE urltoken=?  AND register=? AND reg_date > now() - interval 24 hour";
        $stmt = $pdo->prepare($sql);         
        $stmt->bindParam(1, $urltoken, PDO::PARAM_STR);
        $register = "temp";
        $stmt->bindParam(2, $register, PDO::PARAM_STR);
        $stmt->execute();
        //レコード件数取得
        $row_count = $stmt->rowCount();
        if( $row_count ==1){
            $mail_array = $stmt->fetch();
            $mail = $mail_array['mail'];
        }else{
            $errors['url_check'] = "URLが無効です";
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
        <title>ユーザー登録</title>
    </head>
    <body>


        <?php if (count($errors) === 0): ?>
            <h1>ユーザ登録</h1> 
            <form action="./register.php" method="post" name="form_register" >
            <p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
            名前※必須　　　
            <input type="text" name="name" maxlength="30" size="30" required><br />
            自己紹介　
            <textarea name="comment" size="200" maxlength="200" ></textarea><br />
            パスワード※必須(半角英数、8文字以上20文字以内)　
            <input type="password" name="password" style="ime-mode:disabled;" size="20" minlength="8" maxlength="20" required><br />
            <input type="hidden" name="userid" value="<?php echo $userid; ?>"  ><br />
            <input type="hidden" name="urltoken" value="<?php echo $urltoken; ?>"  ><br />
            <input type="hidden" name="mail" value="<?php echo $mail; ?>"  ><br />
            <input type="submit" value="登録"  ><br /><br />
            </form>



            <?php elseif(count($errors) > 0): ?>
            <?php
            foreach($errors as $value){
                echo "<p>".$value."</p>";
            }
            ?>

        <?php endif; ?>
  
        <p><a href="./top.php">TOP</a></p>



</body>
</html>