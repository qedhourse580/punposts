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


<p>
    通報する内容に間違いがなければ通報するボタンを押してください
</p>

<?php

// 通報内容を受け取る
$report_code = $_POST['report_code'];
$report_inquiry = $_POST['report_inquiry'];
$report_image_path = $_POST['report_image_path'];


// XSS対策
$report_code = htmlspecialchars($report_code, ENT_QUOTES, 'UTF-8');
$report_inquiry = htmlspecialchars($report_inquiry, ENT_QUOTES, 'UTF-8');
$report_image_path = htmlspecialchars($report_image_path, ENT_QUOTES, 'UTF-8');

// 通報内容の確認
// 関連画像がない場合
if ($report_image_path == './upload/') {
// カードレイアウトに収める ************************************
print '<div class="card" style="width: 18rem;">';

print '  <div class="card-body">';
print '    <h6 class="card-title">No.'.$report_code.'</h6>';
print '    <p class="card-text">'.$report_inquiry.'</p>';
print '    <br>';  
print '    <br>';  
}else {
// 関連画像がある場合 
// カードレイアウトに収める ************************************
print '<div class="card" style="width: 18rem;">';

print '  <div class="card-body">';
print '    <h6 class="card-title">No.'.$report_code.'</h6>';
print '    <p class="card-text">'.$report_inquiry.'</p>';
print '    <img src="'.$report_image_path.'" class="card-img-top" alt="...">';
print '    <br>';  
print '    <br>';  
}
print ' <br>';
print ' </div>';

print '</div>';
// カードレイアウトに収める・ここまで ************************************


// 通報内容を確定ページに渡す
print '<form method="post" action="report_done.php">';
print '     備考<br>';
print '     <textarea name="report_remarks" id="" cols="30" rows="10"></textarea><br>';
print '     <input type="submit" class="btn btn-outline-warning btn-sm" value="通報する">';
print '     <input type="hidden" name="report_code" value="'.$report_code.'">';  //入力内容を次のページへ飛ばす
print '     <input type="hidden" name="report_inquiry" value="'.$report_inquiry.'">';  //入力内容を次のページへ飛ばす
print '     <input type="hidden" name="report_image_path" value="'.$report_image_path.'">';  //入力内容を次のページへ飛ばす
print '</form>';
print '<br>';
print '<br>';

?>

<button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href='index.php'"><b>トップページへ戻る</b></button>
<br>
<br>

  </div>
</body>
</html>