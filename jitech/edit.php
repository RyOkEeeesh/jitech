<?php
require_once './apcufn.php';

function processEdit($post) {
  $no = $post['no'];

  if ( !empty($_POST['delete']) && $_POST['delete'] ) {
    $sql = '
    DELETE
      o, d, t
    FROM
      overview o
    LEFT JOIN
      dir d ON o.no = d.no
    LEFT JOIN
      time t ON o.no = t.no
    WHERE
      o.no = ' . $no . ';';
    $parentDirPath = './' . UPLOAD_DIR . '/' . $post['parentDir'];
    $thumbPath = getThumbImg($post);
    remove_directory($parentDirPath);
    if ($thumbPath !== './'.THUMBNAIL_DIR.'/post.png') unlink($thumbPath);

  } else {
    if ( hText($_POST['no']) != $no ) return 'This post cannot be found.';
    $isSameAsDefault = true;
    $sqlParts = [];

    if ( !$title = hText(rtrim($_POST['title'], "\xE3\x80\x80\x20")) ) return 'No title entered.';
    $desc = !empty($_POST['desc']) ? hText($_POST['desc']) : null ;
    $hidden = isset($_POST['hidden']) ? (bool)$_POST['hidden'] : null;

    if ( $post['title'] != $title ) {
      $isSameAsDefault = false;
      $sqlParts[] = "title = '$title'";
    }

    if ( $post['description'] != $desc ) {
      $isSameAsDefault = false;
      $sqlParts[] = "description = '$desc'";
    }

    if ( ( $post['hidden'] != $hidden ) && !( is_null($post['hidden']) && $hidden === false ) ) {
      $isSameAsDefault = false;
      $sqlParts[] = "hidden = " . ($hidden ? '1' : '0');
    }

    if ( $isSameAsDefault ) return 'OK';

    $sql = 'UPDATE overview SET ' . implode(', ', $sqlParts) . ' WHERE no = ' . $no . ';';
  }

  try {
    if ( !isset($sql) ) throw new Exception('Not processed properly');
    $pdo = new PDO(DSN_JITECH, USERNAME, PASSWORD, OPTIONS);
    $st = $pdo -> prepare($sql);
    $st -> execute();
    delCache();
    return 'OK';
  } catch (Exception $e) {
    return $e->getMessage();
  }
}

try {

  if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $postData = getAllPost();
    $notFound = true;
    foreach($postData as $post) {
      if ( $post['no'] == hText($_POST['no']) ) {
        $notFound = false;
        $result = processEdit($post);
        break;
      }
    }
    if ( $notFound ) throw new Exception('This post cannot be found.');

    if ( $result === 'OK' ) echo 'Processed successfully';
    else throw new Exception($result);

  } else {
    location_index();
  }

}  catch (Exception $e) {
  echo 'Error : ' . $e->getMessage();
}


?>