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

// 通報内容を受け取る
$report_remarks = $_POST['report_remarks'];
$report_code = $_POST['report_code'];
$report_inquiry = $_POST['report_inquiry'];
$report_image_path = $_POST['report_image_path'];

// XSS対策
$report_remarks = htmlspecialchars($report_remarks, ENT_QUOTES, 'UTF-8');
$report_code = htmlspecialchars($report_code, ENT_QUOTES, 'UTF-8');
$report_inquiry = htmlspecialchars($report_inquiry, ENT_QUOTES, 'UTF-8');
$report_image_path = htmlspecialchars($report_image_path, ENT_QUOTES, 'UTF-8');


// データベースログイン
$dsn = 'mysql:dbname=*****';
$user = '*****';
$password = '*****';

$dbh = new PDO($dsn, $user, $password);
$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// SQL対策しつつデータ準備
$sql = 'UPDATE table_pun_post SET report_flag=? WHERE code=?';
$stmt = $dbh -> prepare($sql);
$data[] = 1;
$data[] = $report_code;
$stmt -> execute($data);

// 同じデータベースにアクセスするときに同じ配列の名前を使いまわしているので、一旦配列を空にしておく。
// そうしないと配列に一回目の内容が残ってしまい、二回目のアクセス時に配列のデータ数があっていませんというエラーが出る。
$data = array();

// 通報用データベース
// SQL対策しつつデータ準備
$sql = 'INSERT INTO report_inquiry(report_code, ip_address, http_user_agent, report_remarks) VALUES(?, ?, ?, ?)';
$stmt = $dbh -> prepare($sql);
$data[] = $report_code;
$data[] = $_SERVER['REMOTE_ADDR']; // ipaddressを保存
$data[] = $_SERVER['HTTP_USER_AGENT']; // ユーザーエージェント（どんなブラウザでアクセスしているか）を保存
$data[] = $report_remarks;
$stmt -> execute($data);

// データベースログアウト
$dbh = null;

print '通報が完了しました。<br>';

print '<br>';

?>

<button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href='index.php'"><b>トップページへ戻る</b></button>
<br>
<br>

  </div>
</body>
</html>