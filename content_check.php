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
$pun_image_size = $_FILES['pun_image']['size'];  //関連画像のサイズ。後で画像有無をチェックするため。画像はあったときのみ利用。
$pun_original = $_POST['pun_original']; //ダジャレネタ元
$nickname = $_POST['nickname']; //投稿者ニックネーム
$delete_password = $_POST['delete_password']; //削除用パスワード

// XSS対策
$pun_inquiry = htmlspecialchars($pun_inquiry, ENT_QUOTES, 'UTF-8');
$pun_original = htmlspecialchars($pun_original, ENT_QUOTES, 'UTF-8');
$nickname = htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8');
$delete_password = htmlspecialchars($delete_password, ENT_QUOTES, 'UTF-8');


// 新規投稿と過去投稿が被ってないか比較する
// データベースログイン
$dsn = 'mysql:dbname=*****';
$user = '*****';
$password = '*****';

$dbh = new PDO($dsn, $user, $password);
$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// SQL対策しつつデータ準備
// 全データ取得
$sql = 'SELECT * FROM table_pun_post WHERE delete_flag != 1';
$stmt = $dbh -> prepare($sql);
$stmt -> execute();

$rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);

// データベースログアウト
$dbh = null;

// 後述のダジャレ内容比較用の全角記号一覧
$serch_character = array("！", "”", "＃", "＄", "％", "＆", "’", "（", "）", "＝", "～", "｜", "‘", "｛", "＋", "＊", "｝", "＜", "＞", "？", "＿", "－", "＾", "￥", "＠", "「", "；", "：", "」", "、", "。", "・", "　", "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ", "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ", "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ", "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ", "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ", "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ", "０", "１", "２", "３", "４", "５", "６", "７", "８", "９");

// 同じダジャレがあった時用のフラグ
$same_flag = 0;

// 新規投稿の日本語部分を抜き出し別の変数に格納
// 全角記号を削除
$pun_inquiry_adjustment = str_replace ($serch_character, "", $pun_inquiry);
// 全角日本語以外を削除（半角英数字記号などを削除）
$pun_inquiry_adjustment = preg_replace ('/[^一-龠ぁ-んァ-ヶ、-↓，．]/', "", $pun_inquiry_adjustment);
// 全角カタカナをひらがなに変換（「ぁ」や「ァ」などの全角小文字を「あ」や「ア」に変換は出来ない）
$pun_inquiry_adjustment = mb_convert_kana ($pun_inquiry_adjustment, "c");

foreach ($rec as $value) {

    // 新規投稿と過去投稿のそれぞれの調整済み内容を比較
    if ($pun_inquiry_adjustment == $value['pun_inquiry_adjustment']) {
        $same_flag = 1; // 同じダジャレがあった時用のフラグを立てる
        print '過去に同じ内容のダジャレがありますので、また別のダジャレをお願いします。';
        print '<br>';
        print 'No.'.$value['code'];
        print '<br>';

    }else {
        // print 'NO';
        // print '<br>';
        // print '<br>';
    }
}


// 入力欄の有無チェック
// ネタ元のチェックはしていない。(オリジナルの場合はネタ元が無い為)
if ($pun_inquiry == '') {
    print 'ダジャレ本文が入力されていません。<br>';
    print 'ダジャレ本文の入力は必ず必要です。<br>';
    print '<br>';
}else {
    print 'ダジャレ本文';
    print '<br>';
    print $pun_inquiry;
    print '<br>';
}

if ($pun_image_size == '') {
    print '関連画像が選択されていません。<br>';
    print '関連画像が無くても投稿は可能です。<br>';
    print '<br>';
}else {

// 投稿画像をリサイズして表示 ******************************************
// 元画像の幅と高さを取得。imagecopyresampledで指定が必要となる為。
$image_info = getimagesize($_FILES['pun_image']['tmp_name']);


// 画像をjpegかpngかを判断 *************************************************
// 投稿画像のタイプによって呼び出す関数を分けている。
if ($image_info['mime'] == 'image/jpeg') {
    // 元画像から新しい画像を作成。
    $image1 = imagecreatefromjpeg($_FILES['pun_image']['tmp_name']);
    // 後でファイル名の後ろに付けるために、mimeタイプを変数に入れておく
    $addImageType = '.jpg';
}else if ($image_info['mime'] == 'image/png') {
    // 元画像から新しい画像を作成。
    $image1 = imagecreatefrompng($_FILES['pun_image']['tmp_name']);
    // 後でファイル名の後ろに付けるために、mimeタイプを変数に入れておく
    $addImageType = '.png';
}else {
    print '画像はjpegかpngの形式でお願いします。';
    print '<br>';
    $addImageType = '';
}
// 画像をjpegかpngかを判断・ここまで *************************************************

if ($addImageType == '.jpg' || $addImageType == '.png') {
// 投稿画像をリサイズして表示 ******************************************
// 後に元ファイルの幅と高さの指定が必要な為変数に入れておく
$oldWidth = $image_info[0];
$oldHeight = $image_info[1];


// 画像比率維持しながらリサイズ ******************************************
// 基準サイズを決める。このサイズを元に、元画像が横長だったら横が基準サイズになり縦は自動計算される。
// 縦長だったら縦が基準サイズになり横が自動計算される。
$baseSize = 800;
// 元画像が横長か縦長かを判断
// 横長の場合
if ($oldWidth > $oldHeight) {
    $newWidth = $baseSize;
    $newHeight = $newWidth / $oldWidth * $oldHeight;    // 幅を元に元画像の比率維持しながら縦を計算
    
    $image2 = imagecreatetruecolor($newWidth, $newHeight);  // リサイズ先の土台を作成。
    imagecopyresampled($image2, $image1, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight); // リサイズ先の土台に元画像を貼り付け。
}else if ($oldWidth < $oldHeight) { // 縦長の場合
    $newHeight = $baseSize;
    $newWidth = $newHeight / $oldHeight * $oldWidth;    // 縦を元に元画像の比率維持しながら横を計算

    $image2 = imagecreatetruecolor($newWidth, $newHeight);  // リサイズ先の土台を作成。
    imagecopyresampled($image2, $image1, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight); // リサイズ先の土台に元画像を貼り付け。
}else if ($oldWidth == $oldHeight) {   // 横と縦の長さが同じ場合 
    $newWidth = $baseSize;
    $newHeight = $baseSize;

    $image2 = imagecreatetruecolor($newWidth, $newHeight);  // リサイズ先の土台を作成。
    imagecopyresampled($image2, $image1, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight); // リサイズ先の土台に元画像を貼り付け。
}
// 画像比率維持しながらリサイズ ・ここまで *************************************************


// バッファ状態にする。画像の出力先をフォルダに残さないため。
// バッファ開始。
ob_start();
// 画像を出力。
imagejpeg($image2);
// バッファの画像を変数に格納。
$insert_img = ob_get_contents();
// バッファ終了。
ob_end_clean();

// 画像を文字列にエンコード
$encode_test = base64_encode($insert_img);

print '<img src="data:jpg;base64,'.$encode_test.'" alt="">';
print '<br>';

// 投稿画像をリサイズして表示・ここまで ******************************************
}

}

if ($pun_original == '') {
    print 'ネタ元が入力されていません。<br>';
    print 'ネタ元がなくても投稿は可能です。<br>';
    print '<br>';
}else {
    print 'ネタ元';
    print '<br>';
    print $pun_original;
    print '<br>';
}

if ($nickname == '') {
    print 'ニックネームが入力されていません。<br>';
    print 'ニックネームの入力は必ず必要です。<br>';
    print '<br>';
}else {
    print 'ニックネーム';
    print '<br>';
    print $nickname;
    print '<br>';
}

if ($delete_password == '') {
    print '削除用パスワードが入力されていません。<br>';
    print '削除用パスワードの入力は必ず必要です。<br>';
    print '<br>';
}else {
    $delete_password = password_hash($delete_password, PASSWORD_BCRYPT);
    print '<br>';
}

// 入力欄の有無チェック・ここまで

print '<h3>実際の表示イメージ</h3>';
print '<br>';

// 関連画像がない場合
if ($pun_image_size == '') {
    // カードレイアウトに収める ************************************
    print '<div class="card" style="width: 18rem;">';

    print '  <div class="card-body">';
    print '    <h6 class="card-title">No.***</h6>';
    print '    <p class="card-text">'.$pun_inquiry.'</p>';
    print '    <br>';  
    print '    <br>';  

    print '<div style="display:inline-flex">';
    print '       <input type="submit" class="btn btn-outline-secondary btn-sm" value="削除する">';
    print '       <input type="submit" class="btn btn-outline-warning btn-sm" value="通報する">';
    print ' <br>';
    print '</div>';

    print ' <br>';
    print ' </div>';

    print '</div>';
    // カードレイアウトに収める・ここまで ************************************
}else {
    // 関連画像がある場合
    // カードレイアウトに収める ************************************
    print '<div class="card" style="width: 18rem;">';

    print '  <div class="card-body">';
    print '    <h6 class="card-title">No.***</h6>';
    print '    <p class="card-text">'.$pun_inquiry.'</p>';
    print '    <img src="data:jpg;base64,'.$encode_test.'" class="card-img-top" alt="...">';
    print '    <br>';  
    print '    <br>';  

    print '<div style="display:inline-flex">';
    print '       <input type="submit" class="btn btn-outline-secondary btn-sm" value="削除する">';
    print '       <input type="submit" class="btn btn-outline-warning btn-sm" value="通報する">';
    print ' <br>';
    print '</div>';

    print ' <br>';
    print ' </div>';

    print '</div>';
    // カードレイアウトに収める・ここまで ************************************

}



// ダジャレとニックネームと削除用パスワードが空白じゃなければ
// また、同じダジャレがあった時用のフラグが立っていなければOKと戻るボタンを表示
// どれかひとつでも空白だと戻るボタンのみ表示
if ($pun_inquiry != '' && $nickname != '' && $delete_password != '' && $same_flag == 0) {
    print '<form method="post" action="content_done.php" enctype="multipart/form-data">';
    print '     <input type="hidden" name="pun_inquiry" value="'.$pun_inquiry.'">';  //入力内容を次のページへ飛ばす
    print '     <input type="hidden" name="pun_inquiry_adjustment" value="'.$pun_inquiry_adjustment.'">';  //入力内容を次のページへ飛ばす
    // 関連画像がない場合は無処理
    if ($pun_image_size == '') {
    }else {
        // 関連画像がある場合は画像を渡す
        print '     <input type="hidden" name="pun_encode_image" value="'.$encode_test.'">';  //入力内容を次のページへ飛ばす
    }
    print '     <input type="hidden" name="pun_original" value="'.$pun_original.'">';  //入力内容を次のページへ飛ばす
    print '     <input type="hidden" name="nickname" value="'.$nickname.'">';  //入力内容を次のページへ飛ばす
    print '     <input type="hidden" name="delete_password" value="'.$delete_password.'">';  //入力内容を次のページへ飛ばす
    print '     <button type="button" class="btn btn-outline-primary btn-sm" onclick="history.back()"><b>戻る</b></button>';
    print '     <button type="submit" class="btn btn-outline-primary btn-sm"><b>OK</b></button>';
    print '</form>';
}else {
    print '<form method="post" action="index.php">';
    print '     <button type="button" class="btn btn-outline-primary btn-sm" onclick="history.back()"><b>戻る</b></button>';
    print '</form>';
}

}

catch (Exception $e) {
    print 'ただいま障害により大変ご迷惑をおかけしております';
    echo $e->getMessage();
    exit();
}


?>

  </div>
</body>
</html>