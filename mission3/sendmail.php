<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
//エラーメッセージの初期化
$errors = array();

$nowpage = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;


//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

require_once(dirname(__FILE__) . "/../dbconnect.php");
$pdo = connect();
//接続
try{
 
	
	if(empty($_POST)) {
		header("Location:".$nowpage."/../mailform.php");//入力されていないときは登録フォームへ
		exit();
	}else{
		//POSTされたデータを変数に入れる
		$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;//if文らしい
		
		//メール入力判定
		if ($mail == ''){
			$errors['mail'] = "メールが入力されていません。";
		}else{
			if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
				$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
			}
			
			/*
			ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
			*/
			$sql = "SELECT * from register WHERE mail=? AND register=? ";//あるかチェック
				$stmt = $pdo->prepare($sql);         
				$stmt->bindParam(1, $mail, PDO::PARAM_STR);
				$register = "perm";
				$stmt->bindParam(2, $register, PDO::PARAM_STR);
				// SQL の実行
				$stmt->execute();
				foreach($stmt as $re){
					$errors['member_check'] = "このメールアドレスはすでに利用されております。";
				}


		}
	}
	
	if (count($errors) === 0){//正常なとき
		
		$urltoken = hash('sha256',uniqid(rand(),1));//アルゴリズム
		$url = $nowpage."/../registerform.php"."?urltoken=".$urltoken;//URL
		
		//例外処理を投げる（スロー）ようにする
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "INSERT INTO register ( mail, register, reg_date, urltoken) VALUES ( :mail, :register , now() , :urltoken)";
					
			// 挿入する値は空のまま、SQL実行の準備をする
			$stmt = $pdo->prepare($sql);
		
			// 挿入する値を配列に格納する
			$params = array(  ':mail'=>"$mail", ':register'=>"temp",':urltoken'=>"$urltoken",);
			
			// 挿入する値が入った変数をexecuteにセットしてSQLを実行
			$stmt->execute($params);
			
		//mail---------------------------------------------
		//メールの宛先
		$mailTo = $mail;
		//Return-Pathに指定するメールアドレス
		$returnMail = '';
		$name = "";
		$mail = '';
		$subject = "会員登録用URLのお知らせ";

//文字化け対策らしいけどわからない	
$body = <<<EOM
24時間以内に下記のURLからご登録下さい。
{$url}
EOM;
		mb_language('ja');
		mb_internal_encoding('UTF-8');
	
		//Fromヘッダーを作成
		$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
	
		if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {
		
			//セッション変数を全て解除
			$_SESSION = array();
		
			//クッキーの削除
			if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
			}
		
			//セッションを破棄する
			session_destroy();
		
			$message = "メールを送りました。24時間以内にメールに記載されたURLからご登録下さい。";
		
		} else {
			$errors['mail_error'] = "メールの送信に失敗しました。";
		}	
	
	}

	//データベース接続切断
	$pdo = null;

}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
}

 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>メール確認画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>メール確認画面</h1>
 
<?php if (count($errors) === 0): ?>
 
<p><?=$message?></p>

<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}

?>
<p><a href="./mailform.php">再度入力</a></p>
  
<?php endif; ?>
<p><a href="./top.php">TOP</a></p>
 
</body>
</html>
