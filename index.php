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

    // ダジャレ表示エリア ************************************

    // データベースログイン
    $dsn = 'mysql:dbname=*****';
    $user = '*****';
    $password = '*****';
    
    $dbh = new PDO($dsn, $user, $password);
    $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // LIMITで指定した数のダジャレをランダムで取得する
    $sql = 'SELECT code, pun_inquiry, image_data, pun_original, nickname, image_path FROM table_pun_post WHERE delete_flag != 1 ORDER BY RAND() LIMIT 5';
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
    // 取得したダジャレを配列として入れる
    $rec= $stmt -> fetchAll(PDO::FETCH_ASSOC);

    // データベースログアウト
    $dbh = null;


    // ダジャレが入っている配列を順番に表示する
    foreach ($rec as $value) {

      // 関連画像がない場合
      if ($value['image_path'] == './upload/') {
              // カードレイアウトに収める ************************************
      print '<div class="card" style="width: 18rem;">';

      print '  <div class="card-body">';
      print '    <h6 class="card-title">No.'.$value['code'].'</h6>';
      print '    <p class="card-text">'.$value['pun_inquiry'].'</p>';
      print '    <br>';  
      print '    <br>';  

      }else {
      // 関連画像がある場合 
      // カードレイアウトに収める ************************************
      print '<div class="card" style="width: 18rem;">';

      print '  <div class="card-body">';
      print '    <h6 class="card-title">No.'.$value['code'].'</h6>';
      print '    <p class="card-text">'.$value['pun_inquiry'].'</p>';
      print '    <img src="'.$value['image_path'].'" class="card-img-top" alt="...">';
      print '    <br>';  
      print '    <br>';  

      }
      // 削除と通報ボタン ************************************
      print '<div style="display:inline-flex">';
      // 削除用にデータを渡す
      print '   <form method="post" action="delete_check.php">';
      print '       <input type="submit" class="btn btn-outline-secondary btn-sm" value="削除する">';
      print '       <input type="hidden" name="delete_code" value="'.$value['code'].'">';  //入力内容を次のページへ飛ばす
      print '       <input type="hidden" name="delete_inquiry" value="'.$value['pun_inquiry'].'">';  //入力内容を次のページへ飛ばす
      print '       <input type="hidden" name="delete_image_path" value="'.$value['image_path'].'">';  //入力内容を次のページへ飛ばす
      print '   </form>';     

      // 通報用にデータを渡す
      print '   <form method="post" action="report_check.php">';
      print '       <input type="submit" class="btn btn-outline-warning btn-sm" value="通報する">';
      print '       <input type="hidden" name="report_code" value="'.$value['code'].'">';  //入力内容を次のページへ飛ばす
      print '       <input type="hidden" name="report_inquiry" value="'.$value['pun_inquiry'].'">';  //入力内容を次のページへ飛ばす
      print '       <input type="hidden" name="report_image_path" value="'.$value['image_path'].'">';  //入力内容を次のページへ飛ばす
      print '   </form>';
      print ' <br>';
      print '</div>';
      // 削除と通報ボタン・ここまで ************************************

      print ' <br>';
      print ' </div>';

      print '</div>';
      // カードレイアウトに収める・ここまで ************************************

      print '<br>'; 
    }
    
    // ダジャレ表示エリア・ここまで ***************************

    ?>

    
    <h5 class="text-info">投稿はこちらから</h4>
    <!-- <br> -->
    <!-- ダジャレ投稿フォーム -->
    <form method="POST" action="content_check.php" enctype="multipart/form-data">
        ダジャレ本文<br>
        <textarea name="pun_inquiry" id="" cols="30" rows="5"></textarea><br>

        関連画像<br>
        <input name="pun_image" type="file" accept="image/*"><br>

        ネタ元<br>
        <textarea name="pun_original" id="" cols="30" rows="5"></textarea><br>

        ニックネーム<br>
        <input name="nickname" type="text"><br>

        削除用パスワード<br>
        <input name="delete_password" type="password"><br>

        <input type="submit" value="内容確認画面へ"><br>
    </form>

    <br>
    <button type="button" class="btn btn-outline-success btn-sm" onclick="location.href='all_display.php'"><b>ダジャレ一覧はこちらのページ</b></button>

    <br>
    <br>

  </div>
</body>
</html>