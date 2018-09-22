<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>

<body bgcolor="CEECF5">

<?php
//DB接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);


//DBに登録------------------------------------------------------------
//編集モード(未完成)
if(isset($_POST['editnumber']) && !empty($_POST['editnumber'])){//editnumberに入っていて
	$editnumber = $_POST['editnumber'];
	if(isset($_POST['pass_toukou']) && !empty($_POST['pass_toukou'])){//pass_toukouが入っているとき
		$pass_toukou = $_POST['pass_toukou'];
		if(empty($_POST['name'])){
			$name = "名無しさん";
		}
		if(isset($_POST['name']) && !empty($_POST['name'])){
			$name = $_POST['name'];
		}
		if(isset($_POST['comment']) && !empty($_POST['comment'])){
			$comment = $_POST['comment'];
		}

		$sql = 'UPDATE m4table SET name=:name, comment=:comment, pass=:pass WHERE id=:id';
//		$sql = "UPDATE m4table SET name='$name', comment='$comment', pass='$pass_toukou', WHERE id=:id";
//		$result = $pdo->query($sql);
		$stmt = $pdo -> prepare($sql);
		$stmt ->bindParam(':id',$editnumber,PDO::PARAM_INT);
		$stmt ->bindParam(':name',$name,PDO::PARAM_STR);
		$stmt ->bindParam(':comment',$comment,PDO::PARAM_STR);
		$stmt ->bindParam(':pass',$pass_toukou,PDO::PARAM_STR);
		$stmt ->execute();
	}else{
		$error_pass_toukou = "パスワードを入れてください";
	}
//書込モード
}else if(isset($_POST['comment']) && !empty($_POST['comment'])){//commentに入っていて
	$comment = $_POST['comment'];
	if(isset($_POST['pass_toukou']) && !empty($_POST['pass_toukou'])){//pass_toukouが入っているとき
		$pass = $_POST['pass_toukou'];
		if(empty($_POST['name'])){
			$name = "名無しさん";
		}
		if(isset($_POST['name']) && !empty($_POST['name'])){
			$name = $_POST['name'];
		}
		$addsql = $pdo ->prepare("INSERT INTO m4table (name,comment,pass) VALUES(:name,:comment,:pass)");
		$addsql -> bindParam(':name',$name,PDO::PARAM_STR);
		$addsql -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$addsql -> bindParam(':pass',$pass,PDO::PARAM_STR);
		$addsql -> execute();
	}else{
		$error_pass_toukou = "パスワードを入れてください";
	}
}
//--------------------------------------------------------------------------
//削除------------------------------------------------------------------
if(isset($_POST['delete']) && !empty($_POST['delete'])){//deleteに入っていて
	$delete = $_POST['delete'];
	if(isset($_POST['pass_delete']) && !empty($_POST['pass_delete'])){//pass_deleteが入っているとき
		$pass_delete = $_POST['pass_delete'];
		$sql = 'SELECT * FROM m4table';
		$result = $pdo -> query($sql);
		foreach($result as $row){
			if($delete == $row['id']){//idがヒットしたら
				if($pass_delete == $row['pass']){//passが正しいとき
					$sql = "DELETE FROM m4table WHERE id=$delete";
					$result = $pdo->query($sql);
				}else{
					$error_pass_delete = "パスワード忘れちゃった？";
				}
			}
		}
	}else{
		$error_pass_delete = "パスワードを入れてください";
	}
}
//--------------------------------------------------------------------------
//編集前--------------------------------------------------------------------
if(isset($_POST['edit']) && !empty($_POST['edit'])){//editに入っていて
	$edit = $_POST['edit'];
	if(isset($_POST['pass_edit']) && !empty($_POST['pass_edit'])){//passが入っているとき
		$pass_edit = $_POST['pass_edit'];
		$sql = 'SELECT * FROM m4table';
		$result = $pdo ->query($sql);
		foreach($result as $row){
			if($edit == $row['id']){//idがヒットしたら
				if($pass_edit == $row['pass']){//passが正しいとき
					$n = $row['name'];
					$c = $row['comment'];
					$p = $row['pass'];
					$en = $row['id'];
				}else{
					$error_pass_edit = "パスワード忘れちゃった？";
				}
			}
		}
	}else{
		$error_pass_edit = "パスワードを入れてください";
	}
}

//--------------------------------------------------------------------------
?>


 <form action="mission_4.php" method="post">
<center>
<b><font size="6" color="B404AE">名も無き掲示板</font></b><hr>
  <p>なんでもいいから投稿するのです…|ω・)
    <br><input type = "text" name = "name" placeholder = "名前" value = "<?php echo $n; ?>" size = "20">
    <input type = "text" name = "pass_toukou" placeholder = "パスワード(必須)" value = "<?php echo $p; ?>" size = "20">
    <br><input type = "text" name = "comment" placeholder = "コメント(必須)" value = "<?php echo $c; ?>" size = "46">
    <br><input type = "submit" value = "投稿" >
  </p>
    <font color="FF0000"><?php echo $error_pass_toukou; ?></font>
    <br>
  <p>削除はこちらからどうぞ( ;∀;)
    <br><input type = "text" name = "delete" placeholder = "削除対象番号" size = "20">
    <input type = "text" name = "pass_delete" placeholder = "パスワード" size = "20">
    <br><input type = "submit" value = "削除" >
  </p>
    <font color="FF0000"><?php echo $error_pass_delete; ?></font>
    <br>
  <p>編集はこちらからどうぞ(/・ω・)/
    <br><input type = "text" name = "edit" placeholder = "編集対象番号" size = "20">
    <input type = "text" name = "pass_edit" placeholder = "パスワード" size = "20">
    <br><input type = "submit" value = "編集" >
  </p>
    <font color="FF0000"><?php echo $error_pass_edit; ?></font>
    <br>
  <p>
    <input type = "hidden" name = "editnumber" value = "<?php echo $en; ?>">
  </p>
  <hr>
 </form>
</center>

<?php
//テキストファイルを読み込んで表示------------------------------------------
$showsql = 'SELECT * FROM m4table order by id';
$result = $pdo -> query($showsql);
foreach($result as $row){
	echo $row['id'].',';
	echo $row['name'].'　';
	echo $row['comment'].'　';
	echo $row['date'];
//.'　'	echo $row['pass'];
	echo '<br>';
}

//--------------------------------------------------------------------------
?>
</body>
</html>
