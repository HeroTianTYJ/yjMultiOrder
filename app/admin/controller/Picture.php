<?php

namespace app\admin\controller;

use app\admin\model;
use Exception;
use qiniu\Auth;
use qiniu\Storage\UploadManager;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Picture extends Base
{
    public function index()
    {
        $pictureDirAll = [];
        $dirUpload = ROOT_DIR . '/' . Config::get('dir.upload');
        if (is_dir($dirUpload)) {
            foreach (scandir($dirUpload) as $value) {
                if (!in_array($value, ['.', '..'])) {
                    $pictureDirAll[] = [
                        'id' => $value,
                        'name' => $value,
                        'total1' => count(scandir($dirUpload . '/' . $value)) - 2,
                        'total2' => count($this->getDbDirLostPicture($value, $dirUpload . '/' . $value)),
                        'total3' => count($this->getDirRedundancyPicture($value, $dirUpload . '/' . $value))
                    ];
                }
            }
        }
        $pictureDirAll = $this->filter($pictureDirAll);
        foreach ($pictureDirAll as $key => $value) {
            $pictureDirAll[$key] = $this->listItem($value);
        }
        rsort($pictureDirAll);
        if (Request::isAjax()) {
            $nowPage = intval(Request::post('page', 1));
            $nowPage = $nowPage > 0 ? $nowPage : 1;
            $firstRow = Config::get('app.page_size') * ($nowPage - 1);
            return $pictureDirAll ?
                json_encode(array_slice($pictureDirAll, $firstRow, Config::get('app.page_size'))) : '';
        }
        View::assign([
            'Total' => count($pictureDirAll),
            'QrcodeCount' => count(scandir(ROOT_DIR . '/download/qrcode/')) - 2
        ]);
        return $this->view();
    }

    public function picture()
    {
        if (Request::get('id')) {
            $dir = ROOT_DIR . '/' . Config::get('dir.upload') . Request::get('id');
            if (is_dir($dir)) {
                $dirPicture = $this->getDirPicture($dir);
                $dbDirPicture = $this->getDbDirPicture(Request::get('id'), $dir);
                $dbDirLostPicture = $this->getDbDirLostPicture(Request::get('id'), $dir);
                $dirRedundancyPicture = $this->getDirRedundancyPicture(Request::get('id'), $dir);
                switch (Request::get('type')) {
                    case 1:
                        $pictureAll = $dbDirPicture;
                        break;
                    case 2:
                        $pictureAll = $dbDirLostPicture;
                        break;
                    case 3:
                        $pictureAll = $dirRedundancyPicture;
                        break;
                    default:
                        $pictureAll = $dirPicture;
                }
                $pictureAll = $this->filter($pictureAll);
                foreach ($pictureAll as $key => $value) {
                    $pictureAll[$key] = $this->listItem($value);
                }
                if (Request::isAjax()) {
                    $nowPage = intval(Request::post('page', 1));
                    $nowPage = $nowPage > 0 ? $nowPage : 1;
                    $firstRow = Config::get('app.page_size') * ($nowPage - 1);
                    return $pictureAll ?
                        json_encode(array_slice($pictureAll, $firstRow, Config::get('app.page_size'))) : '';
                }
                View::assign([
                    'Total' => count($pictureAll),
                    'Type' => [
                        '目录中的图片（' . count($dirPicture) . '）',
                        '数据库中的图片（' . count($dbDirPicture) . '）',
                        '数据库中丢失的图片（' . count($dbDirLostPicture) . '）',
                        '目录中冗余的图片（' . count($dirRedundancyPicture) . '）'
                    ]
                ]);
                return $this->view();
            } else {
                return $this->failed('不存在此图片目录！');
            }
        } else {
            return $this->failed('非法操作！');
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id'))) {
            $dir = ROOT_DIR . '/' . Config::get('dir.upload') . Request::post('id');
            if (is_dir($dir)) {
                if (count(scandir($dir)) == 2) {
                    return rmdir($dir) ? showTip('图片目录删除成功！') : showTip('图片目录删除失败！', 0);
                } else {
                    return showTip('此目录下有图片，无法删除！', 0);
                }
            } else {
                return showTip('不存在此图片目录！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function clearPicture()
    {
        if (Request::isAjax() && Request::post('id')) {
            set_time_limit(0);
            $dir = ROOT_DIR . '/' . Config::get('dir.upload') . Request::post('id');
            if (is_dir($dir)) {
                $dirRedundancyPicture = $this->getDirRedundancyPicture(Request::post('id'), $dir);
                if ($dirRedundancyPicture) {
                    foreach ($dirRedundancyPicture as $value) {
                        unlink($dir . '/' . $value['name']);
                    }
                    return showTip('冗余图片清理成功！');
                } else {
                    return showTip('此目录下没有冗余图片！', 0);
                }
            } else {
                return showTip('不存在此图片目录！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function pictureDir()
    {
        if (Request::isAjax()) {
            $pictureDir = ROOT_DIR . '/' . Config::get('dir.upload');
            $pictureDirAll = [];
            if (is_dir($pictureDir)) {
                foreach (scandir($pictureDir) as $key => $value) {
                    if (!in_array($value, ['.', '..', 'avatar'])) {
                        $pictureDirAll[$key]['name'] = $value;
                        $pictureDirAll[$key]['total'] = count(scandir($pictureDir . '/' . $value)) - 2;
                    }
                }
                rsort($pictureDirAll);
                return $pictureDirAll ? json_encode($pictureDirAll) : '';
            }
            return '';
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function pictureList()
    {
        if (Request::isAjax()) {
            $nowPage = intval(Request::get('page', 1));
            $nowPage = $nowPage > 0 ? $nowPage : 1;
            $firstRow = 10 * ($nowPage - 1);
            $dirPicture = $this->getDirPicture(ROOT_DIR . '/' . Config::get('dir.upload') . Request::get('id'));
            return $dirPicture ? json_encode(array_slice($dirPicture, $firstRow, 10)) : '';
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function clearQrcode()
    {
        if (Request::isAjax()) {
            $dir = ROOT_DIR . '/download/qrcode/';
            $scanDir = scandir($dir);
            if (count($scanDir) == 2) {
                return showTip('您的服务器中当前不存在微信小程序码，无需清理！', 0);
            }
            foreach ($scanDir as $value) {
                if (!in_array($value, ['.', '..'])) {
                    unlink($dir . '/' . $value);
                }
            }
            return showTip('微信小程序码清理成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function qiniuSynchronize()
    {
        if (Request::isAjax()) {
            if (
                !Config::get('system.qiniu_access_key') ||
                !Config::get('system.qiniu_secret_key') ||
                !Config::get('system.qiniu_bucket')
            ) {
                return showTip('无法同步，请先检查系统设置中的七牛云存储设置是否完整！', 0);
            }
            if (Request::get('action') == 'do') {
                set_time_limit(0);
                $dirUpload = ROOT_DIR . '/' . Config::get('dir.upload');
                if (is_dir($dirUpload)) {
                    $UploadManager = new UploadManager();
                    $uploadToken = (new Auth(
                        Config::get('system.qiniu_access_key'),
                        Config::get('system.qiniu_secret_key')
                    ))->uploadToken(Config::get('system.qiniu_bucket'));
                    foreach (scandir($dirUpload) as $value) {
                        if (!in_array($value, ['.', '..'])) {
                            foreach (scandir($dirUpload . '/' . $value) as $v) {
                                if (!in_array($v, ['.', '..'])) {
                                    try {
                                        $UploadManager->putFile(
                                            $uploadToken,
                                            $value . '/' . $v,
                                            $dirUpload . $value . '/' . $v
                                        );
                                    } catch (Exception $e) {
                                        echo $e->getMessage();
                                    }
                                }
                            }
                        }
                    }
                }
                return showTip('成功！');
            }
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function getDbDirPicture($id, $dir)
    {
        $pictureAll = [];
        foreach ($this->getDbAllPicture() as $key => $value) {
            if (substr($value, 0, strpos($value, '/')) == $id && file_exists($dir . '/../' . $value)) {
                $pictureAll[$key]['id'] = $pictureAll[$key]['name'] = substr(strstr($value, '/'), 1);
            }
        }
        return $pictureAll;
    }

    private function getDirPicture($dir)
    {
        $pictureAll = [];
        foreach (scandir($dir) as $key => $value) {
            if (!in_array($value, ['.', '..'])) {
                $pictureAll[$key]['id'] = $pictureAll[$key]['name'] = $value;
            }
        }
        rsort($pictureAll);
        return $pictureAll;
    }

    private function getDbDirLostPicture($id, $dir)
    {
        $pictureAll = [];
        foreach ($this->getDbAllPicture() as $key => $value) {
            if (substr($value, 0, strpos($value, '/')) == $id && !file_exists($dir . '/../' . $value)) {
                $pictureAll[$key]['id'] = $pictureAll[$key]['name'] = substr(strstr($value, '/'), 1);
            }
        }
        rsort($pictureAll);
        return $pictureAll;
    }

    private function getDirRedundancyPicture($id, $dir)
    {
        $pictureAll = $dbDirPicture = [];
        foreach ($this->getDbDirPicture($id, $dir) as $value) {
            $dbDirPicture[] = $value['id'];
        }
        foreach ($this->getDirPicture($dir) as $key => $value) {
            if (!in_array($value['name'], $dbDirPicture)) {
                $pictureAll[$key]['id'] = $pictureAll[$key]['name'] = $value['name'];
            }
        }
        rsort($pictureAll);
        return $pictureAll;
    }

    private function getDbAllPicture()
    {
        $pictures = $pictureArr = [];
        foreach (
            array_merge(
                (new model\Brand())->picture(),
                (new model\BrandPage())->picture(),
                (new model\CategoryPage())->picture(),
                (new model\Item())->picture(),
                (new model\Lists())->picture(),
                (new model\Text())->picture()
            ) as $value
        ) {
            foreach ($value as $v) {
                if ($v) {
                    preg_match_all('/\[img=(.*)]/U', $v, $pictures[]);
                }
            }
        }
        foreach ($pictures as $value) {
            if (is_array($value)) {
                $picture = $value[1];
                foreach ($picture as $v) {
                    foreach (explode(',', $v) as $v2) {
                        $pictureArr[] = $v2;
                    }
                }
            } else {
                $pictureArr[] = substr(strstr($value, '/'), 1);
            }
        }
        asort($pictureArr);
        return array_unique($pictureArr);
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        return $item;
    }

    private function filter($data)
    {
        if (!Request::get('keyword')) {
            return $data;
        }
        $result = [];
        foreach ($data as $key => $value) {
            if (strstr($value['name'], Request::get('keyword'))) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
