<?php
session_start();

$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;
$error = "";
// ログイン状態チェック
if (!isset($_SESSION["userid"])) {
    header("Location:".$nowpage."/../login.php");
    exit;
}

$userid = $_SESSION['userid'];
$name = $_SESSION['name'];

require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();
//接続
try{
     

    //-----------------------投稿内容の表示の準備--------------------------
     $sql = "SELECT * from contents ";
        $stmt=$pdo->prepare($sql);
        $stmt->execute();
        foreach($stmt as $re){//reはcontentsの要素,table中身,
            $post['postid']  = $re['postid'];
            $postid = $re['postid'];
            $post['userid']  = $re['userid'];
            $post['comment']  = $re['comment'];
            $post['reg_date']  = $re['reg_date'];
            $postall[$postid] = $post;//$postall-$post- id二十にいれてる
        }
        //print_r($postall);
        if($postall){
            foreach($postall as $post){//postは一つの投稿
            $sql = "SELECT * from profile WHERE userid=? ";//該当のもの1
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(1, $post['userid'], PDO::PARAM_STR);
                $stmt->execute();
                foreach($stmt as $re){//該当のものに対し
                    $post['name'] = $re['name'];
                }
                $postid = $post['postid'];
                $postall[$postid] = $post;

            }


            $sql = "SELECT * from images ";
            $stmt=$pdo->prepare($sql);
            $stmt->execute();
            foreach($stmt as $re){//各データについて
                $imgpostid  = $re['postid'];
                $post = $postall[$imgpostid];
                $post['imgpath']  = $re['imgpath'];
                $post['mime']  = $re['mime'];
                $post['title'] = $re['title'];
                $postall[$imgpostid] = $post;
            }

        }
            $contents = $postall;  
           



      //-----------------------投稿----------------------------------------
           
    if (!empty($_POST['comment'])){  //コメント定義
        $comment=$_POST['comment'];
        
        //コメントDB格納---------------------------------------------------------------------------------------------
        // INSERT文を変数に格納
        $sql = "INSERT INTO contents (userid,  comment,  reg_date ) VALUES (:userid,  :comment,   now()  )";

        // 挿入する値は空のまま、SQL実行の準備をする
        $stmt = $pdo->prepare($sql);
    
        // 挿入する値を配列に格納する
        $params = array(':userid' =>  "$userid",  ':comment' => "$comment");
        
        // 挿入する値が入った変数をexecuteにセットしてSQLを実行
        $stmt->execute($params);

        // 登録完了のメッセージ
        echo "登録完了しました<br />\n";
        echo "comment:".$comment   ."<a href=./top.php>再読み込み</a><br />";
        

       //画像あり-----------------------------------------------------
    if(is_uploaded_file($_FILES['upfile']['tmp_name'])){
        if ($_FILES['upfile']['error'] > 0) {
            $error ='ファイルアップロードに失敗しました。';
        }
            if($_FILES['upfile']['error']==0){
            // 画像を取得
            //echo "がぞう";
            $tmp_name = $_FILES["upfile"]["tmp_name"];
            // ファイル名（ハッシュ値でファイル名を決定するため、同一ファイルは同盟で上書きされる）
            $title = sha1_file($tmp_name);


            //拡張子
            $info = getimagesize($tmp_name);
            if (!$info) {
                $error='有効な画像ファイルを指定してください';
            }
            $mime = $info['mime'];
            // 許可するMIMETYPE
            $allowed_types = array(
                'jpg' => 'image/jpeg'
                , 'png' => 'image/png'
                , 'gif' => 'image/gif'
            );
            if (!in_array($mime, $allowed_types)) {
                $error = '許可されていないファイルタイプです。';
            }

            $ext = array_search($mime, $allowed_types);

            //echo $mime;
            //echo $ext;

            // 保存ファイルパス
            $imgpath = sprintf('%s/%s.%s'
            , 'upfiles'
            , $title
            , $ext );

            //echo $imgpath;

            // アップロードディレクトリに移動
            if (!move_uploaded_file($tmp_name, $imgpath)) {
                $error = 'ファイルの保存に失敗しました。';
            }



            //画像----------------------------------------------------------------------------
            $sql = "SELECT * from contents ";
            $stmt=$pdo->prepare($sql);
            $stmt->execute();
            /*foreach($stmt as $re){//reはcontentsの要素,table中身,
                $postid  = $re['postid'];
            }でもよさそう*/
            $postid = $pdo->lastInsertId('contents');
            //echo $postid;
            if($error == ""){
                echo "postid".$postid."imgpath".$imgpath."title".$title."mime".$mime;

                // INSERT文を変数に格納
                $sql = "INSERT INTO images(postid, imgpath, title , mime ) VALUES (:postid, :imgpath, :title, :mime )";
            
                // 挿入する値は空のまま、SQL実行の準備をする
                $stmt = $pdo -> prepare($sql);
            
                $params = array(':postid' =>  "$postid",  ':imgpath' => "$imgpath" , ':title' => "$title" , ':mime' => "$mime");
            
                // 挿入する値が入った変数をexecuteにセットしてSQLを実行
                $stmt->execute($params);
                echo "画像のアップロードが完了しました<br >\n";
            }
            
        }
    }


    }

    //接続終了--------------------------------------------------------------
    $pdo = null;
}
catch (PDOException $e){//接続に失敗した際のエラー処理
    print('エラーが発生しました。');
    exit($e->getMessage());
}


?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>メイン</title>
    </head>
    <body>
        <h1>メイン画面</h1>
        <!-- ユーザーIDにHTMLタグが含まれても良いようにエスケープする -->
        <p>ようこそ,  <u><?php echo htmlspecialchars($_SESSION["name"], ENT_QUOTES); ?></u>さん</p>  <!-- ユーザー名をechoで表示 -->

            <?php if (!$error=="") : ?>
                <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'utf-8'); ?></p>
            <?php endif; ?>

        <form action="" method="post" name="form_input" enctype="multipart/form-data">
            <label for="name">名前</label>　　　
            <input type="text" id="name" size="15" value= "<?php echo $name; ?>" readonly="readonly" ><br />
            <label for ="comment">コメント</label>　
            <textarea name="comment" size="40" maxlength="200" required></textarea><br />
            <label for="upfile">ファイル</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="300000"/>
            <input type="file" name="upfile" id="upfile" /><br />
            <button type="submit">送信</button><br /><br />
        </form>
        <?php
        if(!empty($contents)):
            foreach($contents as $content){ ?>
                <hr />
                [<?=$content['postid']?>]
                name:<?=$content['name']?>　　id:<?=$content['userid']?>
                <br /><?=$content['comment']?><br /><br />
                <?=$content['reg_date']?><br />
                <?php if(isset($content['imgpath'])){ ?>
                <img src="<?= htmlspecialchars($content['imgpath'], ENT_QUOTES, 'utf-8'); ?>" alt="<?= htmlspecialchars($content['title'], ENT_QUOTES, 'utf-8'); ?>" />
            <?php }}
        endif;
        ?>
        <ul>
            <li><a href="./logout.php">ログアウト</a></li>
            <li><a href="./unsubscribe_confirm.php">退会</a></li>
        </ul>
    </body>
</html>




