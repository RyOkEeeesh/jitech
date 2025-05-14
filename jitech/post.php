<?php
require_once './fn.php';
admin_session('post');

$userAgent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($userAgent, 'Edg') === false && strpos($userAgent, 'Chrome') === false && strpos($userAgent, 'Opera') === false && strpos($userAgent, 'OPR') === false ) {
  die("このブラウザはサポートしていません");
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
  <title>JI TECH post</title>
  <script src="./js/post.js" defer></script>
</head>
<body>
<header class="admin__header">
  <a class="admin__a" href="./admin.php">管理者ページへ</a>
  <h1 class="admin__h1">JI TECH</h1>
  <a class="admin__a" href="./logout.php">ログアウト</a>
</header>
<main class="post__main">
  <div class="main-wrap">
    <div class="form-wrap">
      <div class="from-content">
        <label class="post__label" for="title">タイトル<span class="require">必須</span></label><input class="post__input" type="text" id="title" autocomplete="off" maxlength="100">
        <label class="post__label" for="description">説明</label><textarea class="post__textarea" id="description"></textarea>
        <p class="input-txt">サムネ<span class="require">必須</span></p>
        <input class="df__input" type="checkbox" name="default-thumbnail" id="df-thumb"><label class="df__label" for="df-thumb">デフォルトのサムネイルを使用</label>
        <input class="file__input" type="file" id="thumbnail" style="display: none;"  accept="image/*">
        <div class="thumb-area thumb-open">
          <div class="thumb-area-inner thumb-open">
            <div class="thumb-drop">
              <div class="thumb-drag-leave show-drop-area">
                <div class="drop-txt-wrap">
                  <p class="icon__p"><i class="fa-regular fa-file-image"></i></p>
                  <p class="drop__p">画像ファイルをドラッグアンドドロップ</p>
                  <p class="drop-label__p">または <label class="file__label" for="thumbnail">ファイルを選択</label></p>
                </div>
                <div class="post-file-wrap thumb-list"></div>
              </div>
              <div class="thumb-drag-enter dsp-none">
                <p class="icon__p"><i class="fa-regular fa-file-image"></i></p>
                <p class="drop__p">ここにドロップ</p>
              </div>
            </div>
          </div>
        </div>
        <p class="input-txt">投稿フォルダ<span class="require">必須</span></p>
        <input class="file__input" type="file" id="fileInput" webkitdirectory directory multiple>
        <div class="file-drop">
          <div class="file-drag-leave show-drop-area">
            <div class="drop-txt-wrap">
              <p class="icon__p"><i class="fa-regular fa-folder"></i></p>
              <p class="drop__p">投稿フォルダをドラッグアンドドロップ</p>
              <p class="drop-label__p">または <label class="file__label" for="fileInput">フォルダを選択</label></p>
            </div>
            <div class="post-file-wrap file-list"></div>
          </div>
          <div class="file-drag-enter dsp-none">
            <p class="icon__p"><i class="fa-regular fa-folder"></i></p>
            <p class="drop__p">ここにドロップ</p>
          </div>
        </div>
        <div class="btn-wrap">
          <button class="reset-btn" id="resetBtn" type="button">リセット</button>
          <div class="btn-content">
            <button class="submit-btn" id="submitBtn" type="button"><i class="fa-solid fa-arrow-up-from-bracket"></i>アップロード</button>
          </div>
        </div>
        <div class="spacer">
          <div class="space-inner">
            <div class="space"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="preview-wrap">
      <p class="content-txt">Preview</p>
      <div class="prev-content">
        <div class="post-img-wrap">
          <img class="post-img preview-img" src alt="preview" style="display: none;">
          <div class="desc-content">
            <p class="desc-txt"></p>
          </div>
        </div>
        <div class="title-wrap">
          <h2 class="post-title"></h2>
          <p class="post-time"></p>
        </div>
      </div>
    </div>
    <div class="result-wrap">
      <p class="content-txt">Result</p>
      <pre class="result"></pre>
    </div>
  </div>
</main>
<footer><p class="footer__p">&copy; kaji 2025</p></footer>
</body>
</html>