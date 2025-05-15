#include <Magick++.h>
#include <iostream>
#include <string>
#include <filesystem>

#define MAX_PATH 128

using namespace std;
using namespace Magick;

class MagickCompressor
{
private:
    Image   image;
    char    inputPath[MAX_PATH], outputPath[MAX_PATH];
    size_t  compressionRatio;
    
    void    errorOutput(const char  *log);
    bool    safe_strcpy(char  *dest, size_t  dest_size, const char  *src);
    bool    fileCheck(const char *path);
    void    imageRead(const char *path);

public:
    explicit MagickCompressor(const char  *inputPath, const char   *outputPath, int   compressionRatio);
    ~MagickCompressor();
    
    void    Run();
}