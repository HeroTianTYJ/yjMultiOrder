<?php

namespace qiniu\Storage;

use Exception;
use qiniu\Config;

class UploadManager
{
    /**
     * @param $upToken
     * @param $filename
     * @param $filepath
     * @return array
     * @throws Exception
     */
    public function putFile($upToken, $filename, $filepath)
    {
        $file = fopen($filepath, 'rb');
        if ($file === false) {
            throw new Exception('file can not open', 1);
        }
        $stat = fstat($file);
        $size = $stat['size'];
        if ($size <= Config::BLOCK_SIZE) {
            $data = fread($file, $size);
            fclose($file);
            if ($data === false) {
                throw new Exception('file can not read', 1);
            }
            return FormUploader::put($upToken, $filename, $data, basename($filepath));
        }
        $upload = (new ResumeUploader($upToken, $filename, $file, $size))->upload(basename($filepath));
        fclose($file);
        return $upload;
    }
}
