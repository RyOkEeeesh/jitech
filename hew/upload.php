<?php

// require_once 'fn.php';
require_once './apcufn.php';

define('HTML_PATTERN', '/\.html/');
define('PHP_PATTERN', '/\.php/');
// define('PATH_PATTERN', '/=\"(?!\/\/)(\.\/|\.\.\/|\/)/');

define('INSERT_OVERVIEW', 'INSERT INTO overview (title, description) VALUES (?, ?)');
define('INSERT_FILES', 'INSERT INTO dir ( parentDir ) VALUES (?)');
define('INSERT_TIME', 'INSERT INTO time ( postTime ) VALUES (?)');

define('IMG_EXT', [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/gif' => '.gif'
]);

function moveUploadedFile($tmpName, $filePath) {
  if (!move_uploaded_file($tmpName, $filePath)) {
    throw new Exception("Failed to upload $filePath");
  }
}

function getHtmlFileName($html) {
  return str_replace('.html', '', $html);
}

try {
  $pdo = new PDO(DSN_JITECH, USERNAME, PASSWORD, OPTIONS);

  $stno = $pdo->prepare("SHOW TABLE STATUS LIKE 'overview';");
  $stno->execute();
  $status = $stno->fetch(PDO::FETCH_ASSOC);
  $no = $status['Auto_increment'];

  $unitime = time();
  $RemoveDir = false;

  // process post request
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['title']) || empty($_FILES['files'])) {
      throw new Exception('Title and files are required.');
    }
    if ( !empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
      throw new Exception('Failed to upload thumbnail.');
    }
    if ( $_FILES['files']['error'][0] !== UPLOAD_ERR_OK ) {
      throw new Exception('Failed to upload files.');
    }
    foreach ($_FILES['files']['error'] as $error) {
      if ($error !== UPLOAD_ERR_OK) {
        throw new Exception('Failed to upload files.');
      }
    }

    createDirectory(UPLOAD_DIR);
    createDirectory(THUMBNAIL_DIR);

    $tmpPath = $_FILES['files']['full_path'][0];
    $tmpList = explode('/', $tmpPath);
    $parentDir = array_shift($tmpList);
    $originalParentPath = "./" . UPLOAD_DIR . "/{$parentDir}/";

    if (isDirExist($originalParentPath)) {
      throw new Exception('This file is already posted.');
    }

    // process title, description
    $title = hText($_POST['title']);
    $description = !empty($_POST['description']) ? hText($_POST['description']) : null ;

    if ( empty($title) || empty($unitime) ) {
      throw new Exception('Failed to post.');
    }

    // process thumbnail
    $RemoveDir = true;
    if ( !empty($_FILES['thumbnail']['name']) ) {
      $thumbnailName = $no . '_' . $unitime . IMG_EXT[$_FILES['thumbnail']['type']];
      $thumbnailPath = './' . THUMBNAIL_DIR . '/' . $thumbnailName;
      moveUploadedFile($_FILES['thumbnail']['tmp_name'], $thumbnailPath);
    }

    // process files
    $pageFiles = false;

    foreach ($_FILES['files']['name'] as $key => $name) {
      // if (preg_match(PHP_PATTERN, $name)) {
      //   throw new Exception('PHP file is not supported.');
      // }

      if ( (preg_match(HTML_PATTERN, $name) || preg_match(PHP_PATTERN, $name)) ) $pageFiles = true;

      $dir = str_replace($name, '', $_FILES['files']['full_path'][$key]);
      $filePath = UPLOAD_DIR . '/' . $dir . basename($name);
      $tmpName = $_FILES['files']['tmp_name'][$key];

      createDirectory(dirname($filePath));
      moveUploadedFile($tmpName, $filePath);

      echo "File $name uploaded successfully.\n";
    }

    if ( !$pageFiles ) {
      throw new Exception('There is no HTML file or PHP file.');
    }

    // process db

    $overview = $pdo->prepare(INSERT_OVERVIEW);
    $overview->execute([$title, $description]);

    $time = $pdo->prepare(INSERT_TIME);
    $time->execute([$unitime]);

    $files = $pdo->prepare(INSERT_FILES);
    $files->execute([$parentDir]);

    echo "\nProcessing completed successfully!";
    delCache();

  } else {
    location_login();
  }
} catch (Exception $e) {
  if ( $RemoveDir && !empty($originalParentPath) && isDirExist($originalParentPath) ) {
    remove_directory($originalParentPath);
  }
  if ( $RemoveDir && !empty($_FILES['thumbnail']['name']) && !empty($thumbnailPath) && isDirExist($thumbnailPath) ) {
    remove_directory($thumbnailPath);
  }
  echo hText($e->getMessage());
  echo "\nPlease try again because it was not processed correctly ;(";
}

?>