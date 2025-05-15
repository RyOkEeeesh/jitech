## 2025 HEW 作品

### 1. このサイトについて
一年生の進級制作展で作成したサイトです。スマホには対応していません。  
トップページで、管理者の投稿したサイトをまとめてみることができます。  
管理者ページでは、投稿や編集ができ、アクセス数が可視化されています。
- 使用言語
  - PHP
  - js
  - MySQL

### 2. 操作
- 一般ユーザ
  - トップページ  
  管理者が入力したサイトについての説明は、サムネイルにカーソルを重ねると見ることができます。複数投稿がある場合は、PREV、NEXTを押して前後の投稿を見ることができます。


- 管理者
  - ログインページ  
  ユーザ名、パスワードは ```admin``` です。  

  - 管理者ページ  
  投稿のタイトルや説明、表示非表示、削除ができます。  
  それぞれの投稿のアクセス数を確認でき、時間帯別のアクセス数をグラフで表示しています。  

  - 投稿ページ  
  タイトル、説明、サムネイル、作成したファイルの入ったフォルダを用意し、指定の場所へ入力またはドラッグ&ドロップします。


### 3. このサイトを使用するには
- 環境
  - windows11
  - xampp 3.3.0
  - Apache 2.4.58
  - MariaDB 10.4.32
  - PHP 8.2.12
  - Google Chrome 136.0.7103.94  


[sql/setting.sql](https://github.com/RyOkEeeesh/jitech/blob/main/sql/setting.sql) を、MySQLで実行してください。  
リモートホストで実行する場合は、[fn.php](https://github.com/RyOkEeeesh/jitech/blob/main/jitech/fn.php) の7行目
``` PHP
define('DSN_JITECH', 'mysql:host=127.0.0.1;dbname=jitech;charset=utf8');
define('DSN_HEW_USER', 'mysql:host=127.0.0.1;dbname=jitech_user;charset=utf8');
```
```mysql:host=127.0.0.1``` のIPアドレスを、リモートホストのIPアドレスかドメイン名に変更してください。  
また、MySQLのユーザ名、パスワードを変更する場合は [fn.php](https://github.com/RyOkEeeesh/jitech/blob/main/jitech/fn.php) の10行目
```PHP
define('USERNAME', 'root');
define('PASSWORD', '');
```
を、変更してください。  


データベースサーバの負荷を減らすため、PHPの拡張機能である[APCu](https://www.php.net/manual/ja/book.apcu.php)を使っています。APCuがPHPにインストールされていなくても正常に動作します。  
使用したい場合は、[こちら](https://pecl.php.net/package/apcu)から、dllファイルをインストールするか```pecl install apcu ```を、実行してください。  
インストール完了後、php.ini に ```extension=php_apcu.dll``` を追加してください。  

[jitech](https://github.com/RyOkEeeesh/jitech/tree/main/jitech)をドキュメントルートに配置すると、使用できると思います。
