<?php
//データベースに接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO ( $dsn, $user, $password, array ( PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ) ) ;

//テーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS tbtest"
//カラムの作成
." ( "
."id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32) ,"
. "comment TEXT,"
. "pass char(32),"
."Delpass char(32)"
. " ) ; " ;
$stmt = $pdo->query( $sql ) ;


if(!empty($_POST["editnum"]) and !empty($_POST["Editpass"])){
	$Edid = $_POST["editnum"];
	$Editpass = $_POST["Editpass"];
	$sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach($results as $row){
		if($row['id'] == $Edid and $row['pass'] == $Editpass){
			$Ename = $row['name'];
			$Ecomment = $row['comment'];
			$Enumber = $row['id'];
			$Epass = $row['pass'];
		}
	}
}

?>

<html>
	<head>
		<meta charset = "utf-8">
	</head>
	<body>
		<form method = "post">
<!--初期値をphpで設定　編集番号・パスワードが変数に入っている場合にフォームに名前を表示-->
			<input type = "text" name = "name" value = "<?php if(!empty($Ename)){ echo $Ename; } ?>" placeholder = "Name"><br>
			<input type = "text" name = "comment" value = "<?php if(!empty($Ecomment)){ echo $Ecomment; } ?>" placeholder = "Comment"><br>
			<input type = "hidden" name = "Edit" value = "<?php if(!empty($Enumber)){ echo $Enumber; } ?>"> 
			<input type = "text" name = "password" value = "<?php if(!empty($Epass)){ echo $Epass; } ?>" placeholder = "password">
			<input type = "submit" value = "送信する"><br><br>
			<input type = "text" name = "delenum" placeholder = "Delete Number"><br>
			<input type = "text" name = "Delpass" placeholder = "password">
			<input type = "submit" name = "delete" value = "削除"><br><br>
			<input type = "text" name = "editnum" placeholder = "Edit Number"><br>
			<input type = "text" name = "Editpass" placeholder = "password">
			<input type = "submit" name = "edit" value = "編集"><br>
		</form>
	</body>
</html>

<?php

//投稿機能
if(!empty($_POST["name"]) and !empty($_POST["comment"]) and !empty($_POST["password"]) and empty($_POST["Edit"])){

//データを挿入　SQLを準備　テーブル名(name, comment, pass)にパラメータ(:name, :comment, :pass)を与える
	$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, pass) VALUES (:name, :comment, :pass) ");
	
	$name = $_POST["name"];
	$comment = $_POST["comment"];
	$pass = $_POST["password"];
//トークン（：）に変数を代入（バインド）　bindParamは変数のみ入れられる
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
//準備したものを実行
	$sql -> execute();
	
	echo "投稿を受け付けました。<br><br>";

}

//削除機能
if(!empty($_POST["delenum"]) and !empty($_POST["Delpass"])){
	$id = $_POST["delenum"];
	$Delpass = $_POST["Delpass"];
//データを挿入　SQLを準備　テーブル名(id, Delpass)にパラメータ(:id, :Delpass)を与える
	$sql = $pdo -> prepare("INSERT INTO tbtest (id, Delpass) VALUES (:id, :Delpass) ");
//削除文 'delete from テーブル名 where 条件'; sqlに代入する場合はトークンが必要
	$sql = 'delete from tbtest where id=:id and pass = :pass';
//prepareで準備
	$stmt = $pdo->prepare($sql);
//トークン（：）に変数を代入（バインド）
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->bindParam(':pass',$Delpass,PDO::PARAM_STR);
//準備したものを実行
	$stmt->execute();
	
	echo "投稿を削除しました。<br><br>";
	
}

//編集機能
elseif(!empty($_POST["name"]) and !empty($_POST["comment"]) and !empty($_POST["Edit"]) and !empty($_POST["password"])){

	$Edit = $_POST["Edit"];
//編集（更新）機能　'update テーブル名 set 変数（コンマで複数の変数を選択） where 条件';
	$sql = 'update tbtest set name = :name, comment = :comment, pass = :pass where id = :id';
//SQLを準備
	$stmt = $pdo->prepare($sql);
//トークン（：）に変数を代入（バインド）
	$stmt->bindParam(':id', $Edit, PDO::PARAM_INT);
	$stmt->bindParam(':name',$_POST["name"],PDO::PARAM_STR);
	$stmt->bindParam(':comment',$_POST["comment"],PDO::PARAM_STR);
	$stmt->bindParam(':pass',$_POST["password"],PDO::PARAM_STR);
	$stmt->execute();
	
	echo "投稿を編集しました。<br><br>";
	
}

//表示機能
//select文でテーブルを選択
$sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].'<br>';
		echo "<hr>";
	}
	
/*$sql ='SHOW CREATE TABLE tbtest';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
	echo "<hr>"; */
	
?>