<?php

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

  require_once './fn.php';
  $logs = @file(ACCESS_LOG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  if ( !$logs ) die('ログファイルの読み込みに失敗しました。');

  $date = $_POST['date'];
  if ( !preg_match('/\d{4}-\d{2}-\d{2}/', $date) ) die('日付の形式が違うため、正常に処理できませんでした。');
  $hourlyAccessCounts = array_fill(0, 24, 0);

  foreach($logs as $log) {
    preg_match('/(' . preg_quote($date, '/') . ' \d{2}):\d{2}:\d{2}/', $log, $matches);
    if (isset($matches[1])) {
      $hour = (int)substr($matches[1], 11, 2);
      $hourlyAccessCounts[$hour]++;
    }
  }

  echo json_encode($hourlyAccessCounts);
}

?>