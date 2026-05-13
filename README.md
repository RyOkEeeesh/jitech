# 1年次HEW作品

## セットアップ
1. ダウンロード
```bash
curl -LO https://github.com/RyOkEeeesh/jitech/releases/download/docker/jitech.zip

unzip jitech.zip -x '__MACOSX/*' '*.DS_Store'

cd jitech  # 解凍後のディレクトリ名は適宜合わせてください
```
または、[こちら](https://github.com/RyOkEeeesh/jitech/releases/tag/docker) からダウンロードして展開。

2. Docker コンテナの起動
プロジェクトのルートディレクトリで以下のコマンドを実行します。
```bash
docker compose up -d
```

3. ブラウザで確認
起動が完了したら、以下のURLにアクセスしてください。
管理者のユーザ名、パスワードは `admin` です。
- **トップページ**: [http://localhost:8080](http://localhost:8080)
- **管理ページ**: [http://localhost:8080/admin.php](http://localhost:8080/admin.php)
- **投稿ページ**: [http://localhost:8080/post.php](http://localhost:8080/post.php)