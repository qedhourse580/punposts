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


// ページネーション *********************************************************************
$books_num = count($rec); // トータルデータ件数

// ダジャレの表示件数とページネーションボタンの数
$view=5;

$pages = ceil($books_num / $view); // トータルページ数※ceilは小数点を切り捨てる関数

$current=filter_input(INPUT_GET,"p",FILTER_VALIDATE_INT,
["options"=>["default"=>1,"min_range"=>1,"max_range"=>$pages]
]);

/*$viewが偶数のときは左寄り*/
$offset_left=(int) (($view-1)/2); 
$offset_right=$view - $offset_left -1;

$pos_start=$current - $offset_left;
$pos_end=$current + $offset_right;

if($current-$offset_left<1){
  $pos_start=1;
  $pos_end=$pos_start+($view<$pages?$view:$pages)-1;
}elseif($current+$offset_right>$pages){
  $pos_end=$pages;
  $pos_start=$pages-($view<$pages?$view:$pages)+1;
}

print '<nav aria-label="Page navigation example">';
print '<ul class="pagination">';

if($current>1){print '<li class="page-item"><a class="page-link" href="?p='.($current-1).'">prev</a></li>';}
for($i=$pos_start;$i<=$pos_end;$i++){
  if($i==$current){
    echo '<li class="page-item"><a class="page-link">'.$i.'</a></li>';
  }else{
    echo '<li class="page-item"><a class="page-link" href="?p='.$i.'">'.$i.'</a></li>';
  }
}
if($current<$pages){print '<li class="page-item"><a class="page-link" href="?p='.($current+1).'">next</a></li>';}

print '</ul>';
print '</nav>';

$start_no = ($current - 1) * $view; // 配列の何番目から取得すればよいか
 
// array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
$disp_data = array_slice($rec, $start_no, $view, true);
 
foreach($disp_data as $value){ // データ表示
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

    // ネタ元がある場合
    if ($value['pun_original'] != '') {
      print '    <h6 class="card-subtitle">ネタ元</h6>';
      print '    <p class="card-text">'.$value['pun_original'].'</p>';
    }

    // ニックネーム
    print '    <h6 class="card-subtitle">投稿者</h6>';
    print '    <p class="card-text">'.$value['nickname'].'さん</p>';

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

print '<nav aria-label="Page navigation example">';
print '<ul class="pagination">';

if($current>1){print '<li class="page-item"><a class="page-link" href="?p='.($current-1).'">prev</a></li>';}
for($i=$pos_start;$i<=$pos_end;$i++){
  if($i==$current){
    echo '<li class="page-item"><a class="page-link">'.$i.'</a></li>';
  }else{
    echo '<li class="page-item"><a class="page-link" href="?p='.$i.'">'.$i.'</a></li>';
  }
}
if($current<$pages){print '<li class="page-item"><a class="page-link" href="?p='.($current+1).'">next</a></li>';}

print '</ul>';
print '</nav>';


?>

<br>

<button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href='index.php'"><b>トップページへ戻る</b></button>
<br>
<br>

  </div>
</body>
</html>