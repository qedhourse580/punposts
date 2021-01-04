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
    削除する内容に間違いがなければ、ニックネームと削除用パスワードを入力してから削除するボタンを押してください
</p>

<?php

// 通報内容を受け取る
$delete_code = $_POST['delete_code'];
$delete_inquiry = $_POST['delete_inquiry'];
$delete_image_path = $_POST['delete_image_path'];


// XSS対策
$delete_code = htmlspecialchars($delete_code, ENT_QUOTES, 'UTF-8');
$delete_inquiry = htmlspecialchars($delete_inquiry, ENT_QUOTES, 'UTF-8');
$delete_image_path = htmlspecialchars($delete_image_path, ENT_QUOTES, 'UTF-8');


// 削除内容の確認
// 関連画像がない場合
if ($delete_image_path == './upload/') {
    // カードレイアウトに収める ************************************
    print '<div class="card" style="width: 18rem;">';
    
    print '  <div class="card-body">';
    print '    <h6 class="card-title">No.'.$delete_code.'</h6>';
    print '    <p class="card-text">'.$delete_inquiry.'</p>';
    print '    <br>';  
    print '    <br>';  
    }else {
    // 関連画像がある場合 
    // カードレイアウトに収める ************************************
    print '<div class="card" style="width: 18rem;">';
    
    print '  <div class="card-body">';
    print '    <h6 class="card-title">No.'.$delete_code.'</h6>';
    print '    <p class="card-text">'.$delete_inquiry.'</p>';
    print '    <img src="'.$delete_image_path.'" class="card-img-top" alt="...">';
    print '    <br>';  
    print '    <br>';  
    }
    print ' <br>';
    print ' </div>';
    
    print '</div>';
    // カードレイアウトに収める・ここまで ************************************
    

// 通報内容を確定ページに渡す
print '<form method="post" action="delete_done.php">';
print '     ニックネーム<br>';
print '     <input name="nickname" type="text"><br>';
print '     削除用パスワード<br>';
print '     <input name="delete_password" type="password"><br>';
print '     <input type="submit" class="btn btn-outline-secondary btn-sm" value="削除する">';
print '     <input type="hidden" name="delete_code" value="'.$delete_code.'">';  //入力内容を次のページへ飛ばす
print '     <input type="hidden" name="delete_inquiry" value="'.$delete_inquiry.'">';  //入力内容を次のページへ飛ばす
print '     <input type="hidden" name="delete_image_path" value="'.$delete_image_path.'">';  //入力内容を次のページへ飛ばす
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