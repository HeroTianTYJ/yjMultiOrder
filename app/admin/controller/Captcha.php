<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Captcha extends Base
{
    private array $type = ['英文验证码', '中文验证码', '算术验证码'];
    private array $useImgBg = ['不添加', '添加'];
    private array $useCurve = ['不添加', '添加'];
    private array $useNoise = ['不添加', '添加'];

    public function index()
    {
        $captchaAll = Config::get('captcha');
        arsort($captchaAll);
        if (Request::isAjax()) {
            foreach ($captchaAll as $key => $value) {
                $captchaAll[$key] = $this->listItem($value, $key);
            }
            $nowPage = intval(Request::post('page', 1));
            $nowPage = $nowPage > 0 ? $nowPage : 1;
            $firstRow = Config::get('app.page_size') * ($nowPage - 1);
            return $captchaAll ? json_encode(array_slice($captchaAll, $firstRow, Config::get('app.page_size'))) : '';
        }
        View::assign(['Total' => count($captchaAll)]);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $captchaForm = (new model\Captcha())->form();
                if (is_array($captchaForm)) {
                    $captcha = Config::get('captcha');
                    end($captcha);
                    $captcha[key($captcha) + 1] = $captchaForm;
                    return $this->save($captcha) ? showTip('验证码添加成功！') : showTip('验证码添加失败！', 0);
                } else {
                    return showTip($captchaForm, 0);
                }
            }
            Html::typeRadio($this->type);
            Html::useImgBg($this->useImgBg);
            Html::useCurve($this->useCurve);
            Html::useNoise($this->useNoise);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $captcha = Config::get('captcha');
            $captchaOne = $captcha[Request::post('id')];
            if (!isset($captchaOne)) {
                return showTip('不存在此验证码！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo') && Request::post('id') == 1) {
                    return showTip('演示站，id为1的验证码无法修改！', 0);
                }
                $captchaForm = (new model\Captcha())->form();
                if (is_array($captchaForm)) {
                    $captcha[Request::post('id')] = $captchaForm;
                    $this->save($captcha);
                    return showTip(['msg' => '验证码修改成功！', 'data' => $this->listItem($captchaForm, Request::post('id'))]);
                } else {
                    return showTip($captchaForm, 0);
                }
            }
            Html::typeRadio($this->type, $captchaOne['type']);
            Html::useImgBg($this->useImgBg, $captchaOne['useImgBg']);
            Html::useCurve($this->useCurve, $captchaOne['useCurve']);
            Html::useNoise($this->useNoise, $captchaOne['useNoise']);
            View::assign(['One' => $captchaOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，验证码无法删除！', 0);
            }
            $captcha = Config::get('captcha');
            if (Request::post('id')) {
                if (!isset($captcha[Request::post('id')])) {
                    return showTip('不存在此验证码！', 0);
                }
                unset($captcha[Request::post('id')]);
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!isset($captcha[$value])) {
                        return showTip('不存在您勾选的验证码！', 0);
                    }
                    unset($captcha[$value]);
                }
            }
            return $this->save($captcha) ? showTip('验证码删除成功！') : showTip('验证码删除失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function save($config = [])
    {
        $output = '<?php

return [';
        foreach ($config as $key => $value) {
            $output .= '
    ' . $key . " => [
        'name' => '" . $value['name'] . "',
        'type' => " . $value['type'] . ',';
            if ($value['type'] == 0) {
                $output .= "
        'useZh' => 0,
        'math' => 0,";
            } elseif ($value['type'] == 1) {
                $output .= "
        'useZh' => 1,
        'math' => 0,";
            } elseif ($value['type'] == 2) {
                $output .= "
        'useZh' => 0,
        'math' => 1,";
            }
            $output .= "
        'length' => " . $value['length'] . ",
        'fontSize' => " . $value['fontSize'] . ",
        'imageW' => " . $value['imageW'] . ",
        'imageH' => " . $value['imageH'] . ",
        'bg' => [" . (isset($value['bg']) ? $value['bg'][0] . ', ' . $value['bg'][1] . ', ' . $value['bg'][2] :
                    $value['bgR'] . ', ' . $value['bgG'] . ', ' . $value['bgB']) . "],
        'useImgBg' => " . $value['useImgBg'] . ",
        'useCurve' => " . $value['useCurve'] . ",
        'useNoise' => " . $value['useNoise'] . "
    ],";
        }
        $output = trim($output, ',') . '
];
';
        return file_put_contents(ROOT_DIR . '/config/captcha.php', $output);
    }

    private function listItem($item, $id)
    {
        $item['id'] = $id;
        $item['type'] = $this->type[$item['type']];
        $item['length'] = $item['type'] != 2 ? $item['length'] : '-';
        $item['useImgBg'] = $this->useImgBg[$item['useImgBg']];
        $item['useCurve'] = $this->useCurve[$item['useCurve']];
        $item['useNoise'] = $this->useNoise[$item['useNoise']];
        return $item;
    }
}
