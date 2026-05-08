FROM php:8.2-apache

# MySQLへの接続に必要な拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql mysqli

# ApacheのRewriteモジュールを有効化（必要に応じて）
RUN a2enmod rewrite