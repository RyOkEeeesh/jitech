<?php
require_once './fn.php';
admin_session();

function makePostTable($accessCounter, $post) {
  echo '
  <tr>
    <td class="table-center"><a class="to-top" href="?no=' . $post['no']. '">編集</a></td>
    <td class="table-center"><a class="to-top" href="./?no=' . $post['no']. '">' . $post['no'] . '</a></td>
    <td>' . $post['title'] . '</td>
    <td>' . hTextExceptBr($post['description']) . '</td>
    <td class="table-center">' . ($accessCounter[$post['no']] ?? '0') . '</td>
  </tr>
  ';
}
function makeEditArea($post) {
  $thumb = getThumbImg($post);
  $date = formatDate($post);
  $desc = !empty($post['description']) && $post['description'] ? hTextExceptBr($post['description']) : null ;
  $tmpDesc = $desc ? '
    <div class="desc-content">
      <p class="desc-txt">'. $desc .'</p>
    </div>
    ' : '
    <div class="desc-content" style="display: none;">
      <p class="desc-txt"></p>
    </div>
    ' ;
  $hidden = $post['hidden'] ? 'checked' : '' ;
  echo '
    <div class="edit-wrap">
      <label class="edit__label" for="title">タイトル<span class="require">必須</span></label><input class="edit__input" type="text" id="title" autocomplete="off" value="' . $post['title'] . '"  maxlength="100">
      <label class="edit__label" for="description">説明</label><textarea class="edit__textarea" id="description">' . str_replace('&lt;br /&gt;', '', $post['description']) . '</textarea>
      <label class="show-hidden__label" for="sh"><input class="sh__input" id="sh" type="checkbox"' . $hidden . '>投稿を非表示にする</label>
      <button type="button" class="del-btn">削除</button>
      <button type="button" class="reset-btn">リセット</button>
      <button type="button" class="edit-btn">保存</button>
    </div>
    <div class="edit-preview-wrap">
      <div class="prev-content">
        <div class="post-img-wrap">
          <img class="post-img" src="'.$thumb.'" alt="post-img">
          '.$tmpDesc.'
        </div>
        <div class="title-wrap">
          <h2 class="edit-title">'. $post['title'] .'</h2>
          <p class="edit-time">'. $date .'</p>
        </div>
      </div>
    </div>
  ';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="./css/common.css">
  <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
  <title>Document</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="./js/adm.js" defer></script>
</head>
<body>
<header class="admin__header">
  <?php echo @$_GET['no'] && preg_match('/^[0-9]{1,16}$/', @$_GET['no']) ? '<a class="to-admin" href="./admin.php"><i class="fa-solid fa-arrow-left"></i></a>' : '<a class="admin__a" href="./post.php">投稿ページへ</a>' ?>
  <h1 class="admin__h1">JI TECH</h1>
  <a class="admin__a" href="./logout.php">ログアウト</a>
</header>
<main class="admin__main">
<?php

$postData = getAllPost();

if ( @$_GET['no'] && preg_match('/^[0-9]{1,16}$/', @$_GET['no']) ) {
  $notFound = true;
  $page = hText(@$_GET['no']);
  foreach( $postData as $post ) {
    if ( $post['no'] == $page ) {
      makeEditArea($post);
      $notFound = false;
      break;
    }
  };
  if ( $notFound ) location_admin();
} else {

  $logs = file(ACCESS_LOG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $accessCounter = [];

  foreach($logs as $log) {
    if (preg_match('/\?no=(\d+)/', $log, $matches)) {
      $key = $matches[1];
      if (isset($accessCounter[$key])) $accessCounter[$key]++;
      else $accessCounter[$key] = 1;
    }
  }

  echo '
  <div class="table-wrap">
    <table class="admin__table">
      <thead>
        <tr>
          <th>編集</th><th>No.</th><th>タイトル</th><th>説明</th><th>アクセス数</th>
        </tr>
      </thead>
      <tbody>
  ';
  foreach( $postData as $post ) makePostTable($accessCounter, $post);
  echo '
      </tbody>
    </table>
  </div>
  <div class="graph-wrap">
      <input type="date" class="admin-date" name="date" id="date" value="' . date('Y-m-d') . '">
    <canvas id="accessChart"></canvas>
  </div>
  ';
}

?>

</main>

</body>
</html>