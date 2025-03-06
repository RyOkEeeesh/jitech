<?php
define('LOGS_DIR', 'logs');
define('ACCESS_LOG_PATH', './logs/access.log');
define('EDIT_LOG_PATH', './logs/edit.log');

define('DSN_JITECH', 'mysql:host=127.0.0.1;dbname=jitech;charset=utf8');

define('DSN_HEW_USER', 'mysql:host=127.0.0.1;dbname=hew_user;charset=utf8');
define('USERNAME', 'root');
define('PASSWORD', '');
define('OPTIONS', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

define('GET_POST_ROWS', '
WITH nos AS (
  SELECT
    no,
    LAG(no) OVER (ORDER BY no DESC) AS previous_no,
    LEAD(no) OVER (ORDER BY no DESC) AS next_no
  FROM
    overview
  WHERE
    hidden = 0 OR hidden IS NULL
),
first_last AS (
  SELECT
    MIN(no) AS last_no,
    MAX(no) AS first_no
  FROM
    overview
  WHERE
    hidden = 0 OR hidden IS NULL
),
combined AS (
  SELECT
    nos.no,
    COALESCE(nos.previous_no, first_last.last_no) AS previous_no,
    COALESCE(nos.next_no, first_last.first_no) AS next_no
  FROM
    nos, first_last
)
SELECT 
  o.no,
  o.title,
  o.description,
  o.hidden,
  d.parentDir,
  t.postTime,
  c.previous_no,
  c.next_no
FROM
  overview o
LEFT JOIN
  dir d ON o.no = d.no
LEFT JOIN
  time t ON o.no = t.no
LEFT JOIN
  combined c ON o.no = c.no
WHERE
  o.hidden = 0 OR o.hidden IS NULL
GROUP BY
  o.no, c.previous_no, c.next_no
HAVING
  d.parentDir IS NOT NULL
ORDER BY
  o.no DESC;
');

define('TRY_LIMIT', 5);
define('NEXT_TRY', 60);
define('SESSION_LIMIT', 600);

define('UPLOAD_DIR', 'uploads');
define('THUMBNAIL_DIR', 'thumbnails');



// common
function createDirectory($dir) {
  if (!is_dir($dir)) {
    if (!mkdir($dir, 0777, true)) {
      throw new Exception("Failed to create directory: $dir");
    }
  } elseif (!is_writable($dir)) {
    throw new Exception("Directory exists but is not writable: $dir");
  }
}
function recordAccess(): void {
  createDirectory(LOGS_DIR);

  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $host = $_SERVER['HTTP_HOST'];
  $requestUri = $_SERVER['REQUEST_URI'];
  $currentUrl = $protocol . $host . $requestUri;

  $ipAddress = $_SERVER['REMOTE_ADDR'];
  $userAgent = $_SERVER['HTTP_USER_AGENT'];

  $handle = fopen(ACCESS_LOG_PATH, 'a');
  if ($handle) {
      $logMsg = date('Y-m-d H:i:s') . ' Page ' . $currentUrl . ' IP ' . $ipAddress . ' Agent ' . $userAgent . "\n";
      fwrite($handle, $logMsg);
      fclose($handle);
  } else {
      error_log("Could not open log file for writing: " . ACCESS_LOG_PATH);
  }
}
function hText($str): string {
  return htmlentities(nl2br($str), ENT_QUOTES, "UTF-8");
}
function hTextExceptBr($str): string {
  return str_replace('&lt;br /&gt;', '<br>', $str);
}
function remove_limit_session(): void {
  if (!empty($_SESSION['session_limit']) && $_SESSION['session_limit'] < time()) {
    $_SESSION['role'] = '';
  }
}
function isDirExist($dir): bool {
  return file_exists($dir) && is_dir($dir);
}
function getPostInfo() {
  try {
    $pdo = new PDO(DSN_JITECH, USERNAME, PASSWORD, OPTIONS);
    $st = $pdo->prepare(GET_POST_ROWS);
    $st -> execute();
    if( $row = $st->fetchAll() ) return $row;
    return null;
  } catch( PDOException $e ) {
    echo $e;
  }
}
function getThumbImg($list) {
  return glob('./'.THUMBNAIL_DIR.'/'.$list['no'] . '_' . $list['postTime'].'.*')[0] ?? './'.THUMBNAIL_DIR.'/post.png';
}
function formatDate($list) {
  return date('Y/m/d', $list['postTime']);
}

// only login
function makeSalt($name): string {
  return $name . $name;
}
function hashPW($name, $pw): string {
  $salt = makeSalt($name);
  return hash_hmac('sha3-256', $pw.$salt, 'secret', false);
}


// only admin
function location_admin(): never {
  header('Location: ./admin.php');
  exit;
}
function location_index(): never {
  header('Location: ./index.php');
  exit;
}
function location_login(): never {
  header('Location: ./login.php');
  exit;
}
function location_url($url): never {
  header("Location: $url");
  exit();
}
function admin_session($page = ''): void {
  session_start();
  remove_limit_session();
  if (!isset($_SESSION['role']) || $_SESSION['role'] === '') {
    $page = $page ? '?page='.$page : null ;
    location_url('./login.php'.$page);
  } else {
    if ($_SESSION['role'] !== 'admin') {
      location_index();
    }
  }
  $_SESSION['session_limit'] = time() + SESSION_LIMIT;
}
function remove_directory($dir) {
  if (!is_dir($dir)) return;
  $files = scandir($dir);
  if ($files === false) return;
  $files = array_diff($files, ['.', '..']);
  foreach ($files as $file) {
    $filePath = "$dir/$file";
    if (is_dir($filePath)) {
      remove_directory($filePath);
    } else {
      unlink($filePath);
    }
  }
  rmdir($dir);
}
function getAllPost() {
  try {
    $sql = '
    SELECT
      o.no,
      o.title,
      o.description,
      o.hidden,
      d.parentDir,
      t.postTime
    FROM
      overview o
    LEFT JOIN
      dir d ON o.no = d.no
    LEFT JOIN
      time t ON o.no = t.no
    GROUP BY
      o.no
    HAVING
      d.parentDir IS NOT NULL
    ORDER BY
      o.no DESC;
    ';
    $pdo = new PDO(DSN_JITECH, USERNAME, PASSWORD, OPTIONS);
    $st = $pdo -> prepare($sql);
    $st -> execute();
    if( $row = $st->fetchAll() ) return $row;
    return null;
  } catch( PDOException $e ) {
    echo $e;
  }
}

?>