<?php
require_once './fn.php';
session_start();
if (isset($_SESSION['try']) && $_SESSION['try'] == '0') remove_limit_session();

$failed = false;

if ($page = @$_GET['page']) {
  if ($page != 'post') $page = null;
}

if (isset($_SESSION['role'])) {
  if ($_SESSION['role'] === 'admin') {
    if ($page && !isset($_SESSION['redirected_to_post'])) {
      $_SESSION['redirected_to_post'] = true;
      location_url('./post.php');
    } else {
      location_admin();
    }
  } 
  if ($_SESSION['role'] === 'user') {
    location_index();
  }
}
if (!isset($_SESSION['role'])) $_SESSION['role'] = '';
if (!isset($_SESSION['try'])) $_SESSION['try'] = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['token']) && isset($_SESSION['token']) && intval($_SESSION['token']) === intval($_POST['token'])) {
    $user = hText($_POST['user']);
    $pw = hashPW($user,$_POST['pw']);
    try {
      $_SESSION['token'] = mt_rand();
      $pdo = new PDO(DSN_JITECH_USER, USERNAME, PASSWORD, OPTIONS);
      $st = $pdo -> prepare('SELECT user, role FROM userList WHERE user = :user AND pw = :pw');
      $st -> execute(['user' => $user, 'pw' => $pw]);
      if ($row = $st -> fetch()) {
        $_SESSION['role'] = $row['role'];
        $_SESSION['try'] = 0;
        unset($_SESSION['nextTry']);
        unset($_SESSION['token']);
        if ($row['role'] === 'admin') {
          $_SESSION['session_limit'] = time() + SESSION_LIMIT;
          if ($page) {
            $_SESSION['redirected_to_post'] = true;
            location_url('./post.php');
          } else {
            location_admin();
          }
        } else {
          location_index();
        }
      } else {
        $_SESSION['try']++;
        $failed = true;
        if ($_SESSION['try'] >= TRY_LIMIT) {
          $count = $_SESSION['try'] - TRY_LIMIT + 1;
          $_SESSION['nextTry'] = time() + NEXT_TRY * $count;
        }
      }
    }
    catch (PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  } else {
    location_login();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/common.css">
  <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
  <title>login</title>
  <?php
  if (isset($_SESSION['nextTry']) && $_SESSION['nextTry'] > time()) {
    echo '
    <script defer>
      let limitTime = ' . $_SESSION['nextTry'] - time() . ';
      setInterval(() => {
      document.querySelector(\'.limit-time\').textContent = limitTime;
      if (limitTime < 0) window.location.reload();
    }, 1000);
    </script>
    ';
  }
  ?>
</head>
<body>
  <div class="login-wrap">
    <div class="login-content">
      <h1 class="login__h1">Login</h1>

<?php
if (isset($_SESSION['nextTry']) && $_SESSION['nextTry'] > time()) {
  echo '<p class="login-err-msg-limit">ログイン試行回数上限に達しました。<br><span class="limit-time">' . $_SESSION['nextTry'] - time() . '</span>秒後に再度お試しください。</p>';
} else {
  if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
  } else {
    $token = mt_rand();
    $_SESSION['token'] = $token;
  }
  $page = $page ? '?page=post' : null ;
  echo '<form class="login__form" action="./login.php'. $page .'" method="post" enctype="multipart/form-data">
          <input type="hidden" name="token" value="' . $token . '">
          <label class="login__label" for="user">User name</label>
          <input class="login__input" type="text" name="user" id="user" autocomplete="off" minlength="4" maxlength="50" required>
          <label class="login__label" for="pw">Password</label>
          <input class="login__input" type="password" name="pw" id="pw" autocomplete="off" required>
          <input class="login__input" type="submit" value="Login">
        </form>';
  echo $failed ? '<p class="err-msg">User name または Password が違います</p>' : '<div class="login-space"></div>';
}
?>
      <a class="to-top" href="./">トップページに戻る</a>
    </div>
  </div>
<footer><p class="footer__p">&copy; kaji 2025</p></footer>
</body>
</html>