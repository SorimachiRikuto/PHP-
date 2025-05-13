<?php
///////////////////////////////////////////////////////////////////////////////
// クラス名称   : FireClass
// 処理内容     : ファイルの読み込み・書き込み・削除
// ファイル名称 : FireClass.php
// 作成日付     : 2025/05 (基盤作成時)  ver:1.0
// 備考         : Unix系OSで動作確認済み
//              : なお、インタプリンタ言語はオーバヘッドが大きいので留意すること
//              : 基本的にファイル操作に失敗した場合はエラーをスローします。必要に応じて変更してください。
//              : 詳細はは別途資料を参考してください
///////////////////////////////////////////////////////////////////////////////

class FileClass
{
    private string      $baseDir;   // 操作を行うディレクトリ
    
     /**********************************************************
      関数  ：__construct
      機能  ：初期化(データベース接続)
      引数  ：host              [IN] 影響を与えるディレクトリ
      戻り値：成否
      備考  ：ディレクトリの生存が確認できない場合、エラーとなります。
    **********************************************************/
    function __construct (string $baseDir)
    {
        clearstatcache(); // キャッシュノクリア
        
        // ディレクトリが存在しない場合
        if (!is_dir($baseDir))      throw new InvalidArgumentException("引数で渡されたディレクトリがエラーです。: $baseDir");
        
        // ディレクトリの保存
        $this->baseDir = $baseDir;
        
        return;
    }
    
    /**********************************************************
      関数  ：__destruct
      機能  ：終了時の処理
      引数  ：なし
      戻り値：なし
      備考  ：
    **********************************************************/
    function     __destruct()
    {
        return;
    }
    
    /**********************************************************
      関数  ：aliveFile
      機能  ：ファイルが存在するか確認
      引数  ：fullFilePath          [IN] ファイルフルネーム
      戻り値：成否
            ：
      備考  ：
    **********************************************************/
    private function    aliveFile(string $fullFilePath)
    {
        return file_exists($fullFilePath);
    }

     /**********************************************************
      関数  ：extensionCheck
      機能  ：ファイルの拡張子チェック
      引数  ：extension         [IN] 拡張子
      戻り値：なし
            ：
      備考  ：保存可能なタイプの拡張子か検査する
            ：なお、セキュリティ対策でのファイルのタイプチェックに使用しないこと（意味がない）
    **********************************************************/
    private function    extensionCheck(string $fileExtension)
    {
        $permission_list = ['txt', 'dat'];  // 許可される拡張子
        
        if (in_array($fileExtension, $permission_list))
        {
            throw new InvalidArgumentException("引数で渡された拡張子は許可されていません");
        }
        
        return;
    }
    
    /**********************************************************
      関数  ：textWrite
      機能  ：テキストベースのファイルを書き込み
      引数  ：fullFilePath         [IN] ファイルフルネーム
      戻り値：書き込んだバイト数
            ：
      備考  ：テキストベースのみ
            ：ファイル取り扱い時にエラーが発生した場合、エラーになります。
    **********************************************************/
    private function    textWrite(string $fullFilePath, string $mode, string $content)
    {
        $fp = fopen($fullFilePath, $mode);
        
        try
        {
            // チェック
            if (!$fp && !flock($fp, LOCK_EX))   throw new RuntimeException('ファイルを開けません' . $fullFilePath);
            
            // 書き込み
            if(!fwrite($fp, $content))          throw new RuntimeException('ファイルを書き込めません' . $fullFilePath);
        }
        catch(RuntimeException $e)
        {
            if (is_resource($fp))
            {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            throw $e;
        }
        fflush($fp);
        
        // 後処理
        flock($fp, LOCK_UN);
        fclose($fp);
        
        return;
    }
    
    /**********************************************************
      関数  ：save
      機能  ：ファイルを保存する（新規保存）
      引数  ：fullFilePath          [IN] ファイルネーム
            ：content           [IN] 書き込む内容
      戻り値：なし（失敗時にエラーが発生）
            ：
      備考  ：既存のファイルが存在する場合エラーとなる
            ：テキストベースのみ
    **********************************************************/
    public function     save(string $fullFilePath, string $extension, string $content)
    {
        $fullFilePath = $fullFilePath . '.' . $extension;
        
        // ファイルのチェック
        $this->extensionCheck($extension);
        if (aliveFile($fullFilePath))       throw new InvalidArgumentException("引数で渡されたファイルは既に存在します");
        
        // ファイルの書き込み
        textWrite($fullFilePath, 'w', $content);    // 書き出しのみでオープン
    }
    
    /**********************************************************
      関数  ：overwrite
      機能  ：ファイルを保存する（上書き保存）
      引数  ：fullFilePath          [IN] ファイルネーム
            ：extension         [IN] ファイル拡張子
            ：content           [IN] 書き込む内容
      戻り値：なし（失敗時にエラーが発生）
            ：
      備考  ：既存のファイルが存在しない場合エラーとなる
            ：テキストベースのみ
    **********************************************************/
    public function     overwrite(string $fullFilePath, string $extension, string $content)
    {
        $fullFilePath = $fullFilePath . '.' . $extension;
        
        // ファイルのチェック
        $this->extensionCheck($extension);
        if (!aliveFile($fullFilePath))       throw new InvalidArgumentException("引数で渡されたファイルは存在しません");
        
        // ファイルの書き込み
        $this->textWrite($fullFilePath, 'w', $content);    // 書き出しのみでオープン
    }
    
    /**********************************************************
      関数  ：read
      機能  ：ファイルを読み込む
      引数  ：fullFilePath          [IN] ファイルネーム
            ：
      戻り値：なし（失敗時にエラーが発生）
    
    
            ：
      備考  ：既存のファイルが存在しない場合エラーとなる
            ：テキストベースのみ
    **********************************************************/
    public function read(string $fullFilePath, resource $stream = 0, int $length = null)
    {
        $fp = fopen($fullFilePath, 'r');
        
        try
        {
            if (!$this->aliveFile($fullFilePath) || !$fp || !flock($fp, LOCK_SH))      throw new InvalidArgumentException('ファイルが存在しないか、操作できません。' . $fullFilePath);
        }
        catch(InvalidArgumentException $e)
        {
            if (is_resource($fp))
            {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            throw $e;
        }        
        
        // 読み込み開始
        try 
        {
            if (fseek($fp, $stream) !== 0)        throw new RuntimeException("ファイルポインタの移動に失敗しました");

            if ($length === null)
            {
                $length = filesize($fullFilePath);
                if ($length === false)            throw new RuntimeException("ファイルサイズの取得に失敗しました");
            }

            $data = ($length > 0) ? fread($fp, $length) : '';
            if ($data === false) {
                throw new RuntimeException("ファイルの読み込みに失敗しました");
            }
        }
        catch (RuntimeException $e)
        {
            throw $e;
        }
        finally
        {
            if (is_resource($fp))
            {
                flock($fp, LOCK_UN);
                fclose($fp);   
            }     
        }

        return $data;

?>
