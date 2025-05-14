<?php

require_once './fn.php';
define('CACHE_POST','hew_post');
define('CACHE_LIMIT', 600);

function isApcuEnabled(): bool {
  return function_exists('apcu_enabled') && apcu_enabled();
  }
function getCache() {
  if ( !isApcuEnabled() ) return;
  return apcu_fetch(CACHE_POST);
  }
function setCache($value): void {
  if ( !isApcuEnabled() ) return;
  apcu_store(CACHE_POST, $value, CACHE_LIMIT);
  }
function delCache(): void {
  if ( !isApcuEnabled() ) return;
  apcu_delete(CACHE_POST);
  }
function getPost() {
  if ( !$data = getCache() ) $data = getPostInfo();
    setCache($data);
    return $data;
  }

?>