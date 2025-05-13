# PHPクラス設計図：FileClass

------------------------------

## 目的:
ファイルの保存・読み込み・書き込み・削除などを扱うユーティリティクラス

## クラス名:
FileClass

## プロパティ:
- private string $baseDir  
  操作を行うディレクトリ（対象ファイルのルート）

## コンストラクタ:
- __construct(string $baseDir)  
  → 操作対象のディレクトリを指定（存在しない場合はエラー）

## メソッド一覧:

1. public function save(string $fileName, string $fileExtension, string $content): void  
  - 説明: 指定ファイルが存在しない場合に新規作成する（存在すればエラー）

2. public function overwrite(string $fileName, string $fileExtension, string $content): void  
  - 説明: 指定ファイルが存在する場合に内容を上書きする（存在しなければエラー）

3. public function read(string $fullFilePath, resource $streamPosition = 0, int $length = null): string  
  - 説明: ファイル内容を取得する（指定位置・長さの読み込みも可能）

4. private function aliveFile(string $fullFilePath): bool  
  - 説明: ファイルが存在するか確認

5. private function extensionCheck(string $fileExtension): void  
  - 説明: 許可されていない拡張子の場合はエラーとする

6. private function textWrite(string $fullFilePath, string $mode, string $content): void  
  - 説明: ファイルにテキスト内容を書き込む（書き込み・追記）

---

## 使用例:
```php
$fm = new FileClass('/var/www/files/');

$fm->save('log', 'txt', "初期化ログ\n");
$fm->overwrite('log', 'txt', "上書きログ\n");
echo $fm->read('/var/www/files/log.txt');
```

---

## 備考:
- パスの安全性確認（../など）を内部で行うこと
- file_exists / is_writable チェックを活用
- try-catch によるエラー処理を実装することで堅牢に
