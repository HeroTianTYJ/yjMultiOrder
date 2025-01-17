<?php

namespace app\admin\controller;

use Exception;
use qiniu\Auth;
use qiniu\Storage\UploadManager;
use think\facade\Config;
use think\facade\Filesystem;
use think\facade\Request;
use yjrj\QQWry;

class Common extends Base
{
    //图片上传
    public function upload()
    {
        if (Request::isPost()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，无法上传图片！', 0);
            }
            try {
                validate(['file' => 'fileExt:bmp,gif,jpg,jpeg,png'])->check(Request::file());
                $file = Filesystem::putFile(date('Y-m'), Request::file('file'), function () {
                    return date('dHis') . rand(1000, 9999);
                });
                if (
                    Config::get('system.qiniu_access_key') && Config::get('system.qiniu_secret_key') &&
                    Config::get('system.qiniu_bucket')
                ) {
                    (new UploadManager())->putFile(
                        (new Auth(Config::get('system.qiniu_access_key'), Config::get('system.qiniu_secret_key')))
                            ->uploadToken(Config::get('system.qiniu_bucket')),
                        $file,
                        ROOT_DIR . '/' . Config::get('dir.upload') . $file
                    );
                }
                return showTip($file);
            } catch (Exception $e) {
                return showTip($e->getMessage(), 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    //上传IP数据库
    public function qqwry()
    {
        if (Request::isPost()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，无法上传！', 0);
            }
            try {
                validate(['file' => 'fileExt:dat'])->check(Request::file());
                rename(ROOT_DIR . '/' . Config::get('dir.upload') . Filesystem::putFile(
                    date('Y-m'),
                    Request::file('file'),
                    function () {
                        return date('dHis') . rand(1000, 9999);
                    }
                ), ROOT_DIR . '/data/qqwry.dat');
                return showTip(QQWry::getVersion());
            } catch (Exception $e) {
                return showTip($e->getMessage(), 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }
}
