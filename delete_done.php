<!doctype html>
<html lang="ja">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>ダジャレポスト</title>
  </head>
  <body>

  <div class="container-fluid">
    <h1 class="text-success">ダジャレポスト</h1>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>


<?php

// 削除対象の情報を受け取る
$nickname = $_POST['nickname'];
$delete_password = $_POST['delete_password'];
$delete_code = $_POST['delete_code'];
$delete_inquiry = $_POST['delete_inquiry'];
$delete_image_path = $_POST['delete_image_path'];

// XSS対策
$nickname = htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8');
$delete_password = htmlspecialchars($delete_password, ENT_QUOTES, 'UTF-8');
$delete_code = htmlspecialchars($delete_code, ENT_QUOTES, 'UTF-8');
$delete_inquiry = htmlspecialchars($delete_inquiry, ENT_QUOTES, 'UTF-8');
$delete_image_path = htmlspecialchars($delete_image_path, ENT_QUOTES, 'UTF-8');


// 削除対象のニックネームと削除用パスワードを取り出すためのデータベース接続
// データベースログイン
$dsn = 'mysql:dbname=*****';
$user = '*****';
$password = '*****';

$dbh = new PDO($dsn, $user, $password);
$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 削除対象のニックネームと削除用パスワードを取り出す
$sql = 'SELECT nickname, delete_password FROM table_pun_post WHERE code = '.$delete_code;
$stmt = $dbh -> prepare($sql);
$stmt -> execute();
// 取得したニックネームと削除用パスワードを配列として入れる
$rec= $stmt -> fetchAll(PDO::FETCH_ASSOC);

// データベースログアウト
$dbh = null;

if ($nickname == $rec[0]['nickname'] && password_verify($delete_password, $rec[0]['delete_password'])) {
    // print 'OK';
    print '<br>';
    // 削除対象のdelete_flagを立てるためのデータベース接続
    // データベースログイン
    $dsn = 'mysql:dbname=*****';
    $user = '*****';
    $password = '*****';
    
    $dbh = new PDO($dsn, $user, $password);
    $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL対策しつつデータ準備
    $sql = 'UPDATE table_pun_post SET delete_flag=? WHERE code=?';
    $stmt = $dbh -> prepare($sql);
    $data[] = 1;
    $data[] = $delete_code;
    $stmt -> execute($data);

    // データベースログアウト
    $dbh = null;

    print '削除が完了しました。<br>';
    print '<br>';
}else {
    // print 'NG';
    print '<br>';
    print 'ニックネームとパスワードのいずれか、あるいはどちらも登録時のものと一致しません。';
    print '<br>';
    print '<form method="post" action="delete_check.php">';
    print '    <input type="button" class="btn btn-outline-primary btn-sm" name="back" onclick="history.back()" value="戻る">';
    print '</form>';
}
print '<br>';
print '<br>';
print '<br>';
print '<br>';

?>

<button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href='index.php'"><b>トップページへ戻る</b></button>
<br>
<br>

  </div>
</body>
</html>