///////////////////////////////////////////////////////////////////////////////
//このコードはAPIの仕組みを理解するためのサンプルコードです。(version1.0)
//実際に機能はしません
//(2025/05)
// --memo--
// 参考サイト:https://ugcj.com/magick%E3%83%A9%E3%82%A4%E3%83%96%E3%83%A9%E3%83%AA/#google_vignette
// Unix系OSでの環境構築:https://imagemagick.org/script/magick++.php
//
// 実行時は、PHP側でexec()等を使用します。
// 実行時のcmdの例は[./ImageCompressor input.jpg output.jpg 80]
///////////////////////////////////////////////////////////////////////////////
#include <Magick++.h> 
#include <iostream>
#include <string>

using namespace std;
using namespace Magick;

int main(int argc, char* argv[])
{   
    // 引数の数を確認する(この時の引数とは、オブジェクト実行時に渡される引数)
    if (argc != 4)
    {
        cerr << "Usage: ImageCompressor <inputFile> <outputFile> <quality>" << endl;
        return 1;
    }

    // 引数を格納
    string inputPath = argv[1];
    string outputPath = argv[2];
    int quality = stoi(argv[3]);

    // 引数で渡された圧縮率の確認(1 ～ 100)
    if (quality < 1 || quality > 100)
    {
        cerr << "Error: Quality must be between 1 and 100." << endl;
        return 2;
    }

    // 圧縮開始
    try
    {
        // 初期化
        InitializeMagick(*argv);

        // 画像読み込み
        Image image;
        image.read(inputPath);

        // 品質設定（対応する形式のみ）
        image.quality(quality);

        // 書き出し
        image.write(outputPath);

    }
    catch (Exception &error_)
    {
        cerr << "Image processing error: " << error_.what() << endl;
        return 3;
    }

    return 0; // 成功
}