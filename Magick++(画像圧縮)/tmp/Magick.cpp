#include <Magick++.h>
#include <iostream>
#include <string>
#include <filesystem>

#include "MagickClass.hpp"

#define MAX_PATH 128

namespace fs = std::filesystem;
using namespace std;
using namespace Magick;

/**********************************************************
  関数  ：errorOutput（private）
  機能  ：エラーログの出力
  引数  ：log               [IN] エラーログ
  戻り値：なし
  備考  ：
**********************************************************/
void    MagickCompressor::errorOutput(const char  *log)
{
    cerr << "画像圧縮中にエラーが発生しました。" << log << endl;
}

/**********************************************************
  関数  ：safe_strcpy（private）
  機能  ：安全なstrcpy（MS版のstrcpy_sを再現）
  引数  ：dest              [OUT] 対象文字列
        ：dest_size         [IN] 対象文字列サイズ
        ：src               [IN] 元の文字列
  戻り値：成否
  備考  ：
**********************************************************/
bool    MagickCompressor::safe_strcpy(char  *dest, size_t  dest_size, const char  *src)
{
    if (dest_size <= 0)    return  false;
    strncpy(dest, src, dest_size - 1);
    dest[dest_size - 1] = '\0';
    
    return true;
}

/**********************************************************
  関数  ：fileCheck（private）
  機能  ：ファイルが存在するかの確認
  引数  ：path               [IN] ファイルパス
  戻り値：成否
  備考  ：
**********************************************************/
bool    MagickCompressor::fileCheck(const char *path)
{
    return fs::is_regular_file(path);
}

/**********************************************************
  関数  ：imageRead（private）
  機能  ：画像を読み込む
  引数  ：path               [IN] ファイルパス
  戻り値：なし
  備考  ：引数が不正だった場合、エラーが出力されます
        ：エラー処理で動作を停止したくない場合はcatchしてください
**********************************************************/
void    MagickCompressor::imageRead()
{
    this->image.read(this->inputPath);  // 失敗時、MagickCompressor::Exception を throwされます
    
    return;
}

/**********************************************************
  関数  ：MagickCompressor（コンストラクター）
  機能  ：初期化
  引数  ：inputPath         [IN] 読み込むパス
        ：outputPath        [IN] 出力するパス
        ：compressionRatio  [IN] 圧縮率
  戻り値：なし
  備考  ：引数が不正だった場合、エラーが出力されます
        ：エラー処理で動作を停止したくない場合はcatchしてください
**********************************************************/
MagickCompressor::MagickCompressor(const char  *inputPath, const char   *outputPath, int   compressionRatio)
{
    // 初期化
    InitializeMagick(nullptr);
    
    // 引数の確認
    if (compressionRatio < 1 || compressionRatio > 100)      throw invalid_argument("圧縮率の値が規定値外です");
    if (!this->fileCheck(inputPath))                         throw invalid_argument("読み込むファイルが存在しないです");
    
    // 引数を格納(なお、バッファオーバーラン防止)
    this->safe_strcpy(this->inputPath, sizeof(this->inputPath), inputPath);
    this->safe_strcpy(this->outputPath, sizeof(this->inputPath), outputPath);
    this->compressionRatio = compressionRatio;
}

/**********************************************************
  関数  ：MagickCompressor（デストラクター）
  機能  ：初期化
  引数  ：なし
  戻り値：なし
  備考  ：
**********************************************************/
MagickCompressor::~MagickCompressor()
{
    return;
}

/**********************************************************
  関数  ：Run（Public）
  機能  ：画像圧縮の実行
  引数  ：なし
  戻り値：なし
  備考  ：エラーが発生した場合スローされます
        ：エラー処理で動作を停止したくない場合はcatchしてください
**********************************************************/
void    MagickCompressor::Run()
{
    this->imageRead();
    this->quality(this->compressionRatio);     // 圧縮率の指定
    this->write(this->outputPath);     // 失敗時、MagickCompressor::Exception を throwされます
    if (!this->fileCheck(this->outputPath))     throw   Magick::Exception("ファイルを出力できませんでした。");  // filesystemを使用してダブルチェック
    
    return;
}
