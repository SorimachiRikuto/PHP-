<?php
///////////////////////////////////////////////////////////////////////////////
// クラス名称   : PDOクラス
// 処理内容     : SQLserverに接続・結果取得(MySQLを想定して記述されています)
// ファイル名称 : PDOclass.php
// 作成日付     : 2025/05 (基盤作成時)
// 備考         : このクラスはデータベース処理時における基本的な内容が記述されています。
//                アプリケーションに応じて適宜処理を変更してください
//                Ubuntu Serverで検証済み
///////////////////////////////////////////////////////////////////////////////

class Database
{
    
    private     $pdo;
    private     $stmt;
    
    /**********************************************************
      関数  ：__construct
      機能  ：初期化(データベース接続)
      引数  ：host              [IN] ホスト
              dbname            [IN] データベース名称
              user              [IN] ユーザー
              pass              [IN] パスワード
              charset           [IN] 文字コード
      戻り値：なし
      備考  ：接続時のエラー処理を追加してください。
              オプションを適宜追加してください。
    **********************************************************/
    public function     __construct($host, $dbname, $user, $pass, $charset='utf8mb4')
    {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラーは例外で処理
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 連想配列で取得
            PDO::ATTR_EMULATE_PREPARES   => false,                  // プリペアドステートメントのエミュレーション無効
        ];

        try 
        {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        }
        catch (PDOException $e)
        {
            // pass
        }
    }
   
    /**********************************************************
      関数  ：__destruct
      機能  ：終了時の処理
      引数  ：なし
      戻り値：なし
      備考  ：
    **********************************************************/
    public function     __destruct()
    {
        return;
    }
    
    /**********************************************************
      関数  ：bindParams
      機能  ：実行時のバインド処理
      引数  ：params             [IN] バインドする内容
      戻り値：なし
      備考  ：必要に応じてエラー処理を修正してください。
    **********************************************************/
    private function    bindParams(array $params = [])
    {
        foreach ($params as $key => $param)
        {
            if (!is_array($param) || count($param) < 2)
            {
                // 以下例外処理例:
                // throw new InvalidArgumentException("パラメータは ['値', 型] の形式で指定してください。key=$key");
                exit();
            }

            [$value, $type] = $param;

            if (is_int($key))
            {
                // プレースホルダが ? の場合
                $this->stmt->bindValue($key + 1, $value, $type); 
            }
            else
            {
                // プレースホルダが :name の場合
                $this->stmt->bindValue($key, $value, $type);
            }
        }
    }
    
    /**********************************************************
      関数  ：query
      機能  ：クエリの処理
      引数  ：sql                [IN] SQL文
            ：params              [IN] バインドする内容
      戻り値：実行済みのPDOStatement（fetch用などに使用可能）
      備考  ：
    **********************************************************/
    public function     query($sql, array $params = [])
    {
        try
        {
            $this->stmt = $this->pdo->prepare($sql);
            if (!empty($param))
            {
                $this->bindParams($params);   
            }
            $this->stmt->execute();
            
            return $this->stmt;
        }
        catch(PDOException $e)
        {
            exit();
        }
        
        return      false;
    }
    
    /**********************************************************
      関数  ：execute
      機能  ：SQLの実行
      引数  ：sql                [IN] SQL文
            ：params              [IN] バインドする内容
      戻り値：影響を受けた行数
      備考  ：
    **********************************************************/
    public function     execute($sql, array $params = [])
    {
        $this->query($sql, $params);
        
        return      $this->stmt->rowCount();
    }
    
    /**********************************************************
      関数  ：fetch
      機能  ：実行結果の取得
      引数  ：sql                [IN] SQL文
            ：params              [IN] バインドする内容
      戻り値：実行結果
      備考  ：最初に見つかった1行分のデータ
    **********************************************************/
    public function     fetch($sql, array $params = [])
    {
        return      $this->query($sql, $params)->fetch();
    }
    
    /**********************************************************
      関数  ：query
      機能  ：実行結果の取得
      引数  ：sql                [IN] SQL文
            ：params              [IN] バインドする内容
      戻り値：実行結果
      備考  ：全件の結果（連想配列の配列形式）
    **********************************************************/
    public function     fetchAll($sql, array $params = [])
    {
        return      $this->query($sql, $params)->fetchAll();
    }
    
    
    /**********************************************************
      関数  ：rollback
      機能  ：処理の巻き戻し
      引数  ：
      戻り値：
      備考  ：成功可否（bool）
    **********************************************************/
    public function rollback() 
    {
        return $this->pdo->rollBack();
    }
    
    
    /**********************************************************
        いかにAPKに応じて関数を追加
    **********************************************************/
    
    
    
    /**********************************************************

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }
    
    **********************************************************/

}

?>
