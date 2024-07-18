<?php

namespace qiniu;

use qiniu\Http\Client;

class Config
{
    public const SDK_VER = '7.2.9';
    public const BLOCK_SIZE = 4194304;
    public const RS_HOST = 'rs.qiniu.com';

    public function getUpHost($accessKey, $bucket)
    {
        return 'http://' . Client::get('api.qiniu.com/v2/query?ak=' . $accessKey . '&bucket=' . $bucket)
            ->json()['up']['src']['main'][0];
    }
}
