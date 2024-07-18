<?php

namespace app\index\controller;

use think\facade\Config;
use think\facade\Request;
use think\facade\Session;

class Sms extends Base
{
    public function captcha()
    {
        if (Request::isAjax()) {
            if (
                !Config::get('app.demo') &&
                (!Config::get('system.sms_bao_user') || !Config::get('system.sms_bao_pass') ||
                    !Config::get('system.sms_captcha_content'))
            ) {
                    return showTip('本站短信设置不完整，无法发送验证码，如有疑问请联系本站客服！', 0);
            }
            $sessionKeyIndex = Config::get('system.session_key_index');
            if (time() - Session::get($sessionKeyIndex . '.sms_cache', 0) < 60) {
                return showTip('验证码发送过于频繁，请稍后再试！', 0);
            } else {
                if (!preg_match('/^\d{11}$/', Request::post('tel'))) {
                    return showTip('手机号码必须是11位的数字！', 0);
                }
                $captcha = rand(100000, 999999);
                Session::set($sessionKeyIndex . '.sms_cache', time());
                Session::set($sessionKeyIndex . '.sms_captcha_' . passEncode(Request::post('tel')), $captcha);
                if (Config::get('app.demo')) {
                    return showTip($captcha);
                } else {
                    $smsBao = (new \yjrj\Sms([
                        'user' => Config::get('system.sms_bao_user'),
                        'pass' => Config::get('system.sms_bao_pass')
                    ]))->smsBao(
                        Request::post('tel'),
                        str_replace('{captcha}', $captcha, Config::get('system.sms_captcha_content'))
                    );
                    if (!is_numeric($smsBao)) {
                        return showTip('短信发送失败，错误信息：' . $smsBao, 0);
                    }
                    return showTip('发送成功！');
                }
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }
}
