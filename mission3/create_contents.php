<?php
require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();

try{
//データベースに接続
      
            
            $sql = '
            CREATE TABLE IF NOT EXISTS contents(
            postid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            userid VARCHAR(20) NOT NULL,
            comment VARCHAR(200) NOT NULL,
            reg_date TIMESTAMP
            );';
    
            $re = $pdo->query($sql);   
   
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
        <title>createtable</title>
    </head>
    <body>

        <!-- ここではHTMLを書く以外のことは一切しない -->
        <p>MySQL接続</p>
    </body>
</html>