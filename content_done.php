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

try {

// 入力内容を受け取る
$pun_inquiry = $_POST['pun_inquiry'];   //ダジャレ本文
$pun_inquiry_adjustment = $_POST['pun_inquiry_adjustment'];   //調整したダジャレ本文
// 関連画像がある場合
if (array_key_exists('pun_encode_image', $_POST)) {
    $pun_encode_image = $_POST['pun_encode_image'];
}
$pun_original = $_POST['pun_original']; //ダジャレネタ元
$nickname = $_POST['nickname']; //投稿者ニックネーム
$delete_password = $_POST['delete_password']; //削除用パスワード


// XSS対策
$pun_inquiry = htmlspecialchars($pun_inquiry, ENT_QUOTES, 'UTF-8');
$pun_inquiry_adjustment = htmlspecialchars($pun_inquiry_adjustment, ENT_QUOTES, 'UTF-8');
// 関連画像がある場合
if (array_key_exists('pun_encode_image', $_POST)) {
    $pun_encode_image = htmlspecialchars($pun_encode_image, ENT_QUOTES, 'UTF-8');
}
$pun_original = htmlspecialchars($pun_original, ENT_QUOTES, 'UTF-8');
$nickname = htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8');
$delete_password = htmlspecialchars($delete_password, ENT_QUOTES, 'UTF-8');


// 入力欄の有無チェック
if ($pun_inquiry == '') {
    print 'ダジャレ本文が入力されていません。<br>';
}else {
    print 'ダジャレ本文';
    print '<br>';
    print $pun_inquiry;
    print '<br>';
}

// 関連画像がある場合
if (array_key_exists('pun_encode_image', $_POST)) {
    print '<img src="data:jpg;base64,'.$pun_encode_image.'" alt="">';
    print '<br>';

    // ランダムな文字を作成。投稿画像のファイル名にするため。
    $randomString = chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122));

    // 表示するファイル名の作成
    // 取得する時刻を日本に設定。初期設定はヨーロッパ。サーバーの設定ファイルを調整すればその都度日本に設定しなくてよさそう。
    date_default_timezone_set('Asia/Tokyo');
    // 時刻取得
    $nowTime = date("Y-m-d-H-i-s");

    // 投稿時間をファイル名にくっつける。ランダムな文字が他と被った時の為。
    $randomString .= $nowTime;
    // 投稿画像に合わせたmimeタイプを後ろにくっつける
    $randomString .= '.jpg';

    // imagecreatefromstringの引数に渡すためデコードしている。
    $pun_decode_image = base64_decode($pun_encode_image);
    // imagejpegの引数に渡すためリソースを作成している。
    $test2 = imagecreatefromstring($pun_decode_image);
    imagejpeg($test2, './upload/'.$randomString, 100);
}else {
    // 関連画像がない場合
    print '関連画像が選択されていません。<br>';
}

if ($pun_original == '') {
    print 'ネタ元が入力されていません。<br>';
}else {
    print 'ネタ元';
    print '<br>';
    print $pun_original;
    print '<br>';
}

if ($nickname == '') {
    print 'ニックネームが入力されていません。<br>';
}else {
    print 'ニックネーム';
    print '<br>';
    print $nickname;
    print '<br>';
}

if ($delete_password == '') {
    print '削除用パスワードが入力されていません。<br>';
}else {

// 入力欄の有無チェック・ここまで

// ダジャレ本文とニックネームと削除用パスワードが空欄じゃなければ、内容をデータベースに登録
if ($pun_inquiry != '' && $nickname != '' && $delete_password != '') {
    // データベースログイン
    $dsn = 'mysql:dbname=*****';
    $user = '*****';
    $password = '*****';

    $dbh = new PDO($dsn, $user, $password);
    $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL対策しつつデータ準備
    $sql = 'INSERT INTO table_pun_post(pun_inquiry, pun_inquiry_adjustment, pun_original, nickname, ip_address, image_path, delete_password) VALUES(?, ?, ?, ?, ?, ?, ?)';
    $stmt = $dbh -> prepare($sql);
    $data[] = $pun_inquiry;
    $data[] = $pun_inquiry_adjustment;
    $data[] = $pun_original;
    $data[] = $nickname;
    $data[] = $_SERVER['REMOTE_ADDR']; // ipaddressを保存
    // 関連画像がある場合
if (array_key_exists('pun_encode_image', $_POST)) {
    $data[] = './upload/'.$randomString;
}else {
    // 関連画像がない場合
    $data[] = './upload/';
}
    $data[] = $delete_password;
    $stmt -> execute($data);

    // データベースログアウト
    $dbh = null;

    print '投稿ありがとうございました。<br>';

}

}
}
catch (Exception $e) {
    print 'ただいま障害により大変ご迷惑をおかけしております';
    echo $e->getMessage();
    exit();
}



?>

<br>
<button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href='index.php'"><b>トップページへ戻る</b></button>
<br>
<br>

  </div>
</body>
</html>