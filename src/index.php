<?php
require_once './apcufn.php';
recordAccess();
$postData = getPostInfo();
// $postData = getPost();

function makePost($post) {
  $thumb = getThumbImg($post);
  $date = formatDate($post);
  $tmpDesc = !empty($post['description']) && $post['description'] ?
    '<div class="desc-content">
      <p class="desc-txt">'.hTextExceptBr($post['description']).'</p>
    </div>
    ' : '';

  echo '
  <div class="has-data top-link" data-href="'. $post['no'] .'">
    <div class="post-img-wrap">
      <img class="post-img" src="'.$thumb.'" alt="post-img">
      '.$tmpDesc.'
    </div>
    <div class="title-wrap">
      <h2 class="post-title">'. $post['title'] .'</h2>
      <p class="post-time">'. $date .'</p>
    </div>
  </div>
  ';
}
function makeShow($post, $postQuentity) {
  $otherPost = $postQuentity > 1 ? '<div class="has-data mid-link" data-href="'.$post['previous_no'].'">PREV</div><div class="has-data mid-link" data-href="'.$post['next_no'].'">NEXT</div>' : '';
  echo '
  <div class="show-wrap">
    <div class="show-header">
      <div class="has-data mid-link" data-href="./">TOP</div>
      ' . $otherPost . '
      <p class="">'.$post['title'].'</p>
    </div>
    <div class="iframe-wrap">
      <iframe class="show__iframe" src="'.glob('./'.UPLOAD_DIR.'/'.$post['parentDir'].'/index.*')[0].'" frameborder="0" loading="lazy" sandbox="allow-scripts"></iframe>
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
  <link rel="stylesheet" href="./css/common.css">
  <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
  <title>JI TECH</title>
  <script src="./js/main.js" defer></script>
</head>
<body>
  <main class="index__main">

<?php
if ( $postData ) {

  if ( @$_GET['no'] && preg_match('/^[0-9]{1,16}$/', @$_GET['no']) ) {
    $page = hText(@$_GET['no']);
    echo '<div class="show-content">';
    $pageNotFound = true;
  } else {
    echo '
    <div class="first-wrap">
      <h1 class="index__h1">JI TECH</h1>
    </div>
    <div class="post-content">
    ';
  }

  foreach ( $postData as $post ) {
    if ( !empty($page) && $page ) {
      if ( $post['no'] == $page ) {
        makeShow($post, count($postData));
        $pageNotFound = false;
        break;
      }
    } else {
      if ( $post['hidden'] ) continue;
      if ( !isDirExist('./'.UPLOAD_DIR.'/'.$post['parentDir'].'/') ) continue;
      makePost($post);
    }
  }

  if ( isset($pageNotFound) && $pageNotFound ) echo '
  <div class="not-found">
    お探しの投稿はありませんでした
    <a class="to-top" href="./">トップページに戻る</a>
  </div>';

  echo '</div>';

} else {
  echo '<h1 class="index__h1">JI TECH<br><span class="no-post">管理者が投稿するのを待ちましょう！</span></h1>';
}

?>
  </main>
  <footer><p class="footer__p">&copy; kaji 2025</p></footer>
</body>
</html>