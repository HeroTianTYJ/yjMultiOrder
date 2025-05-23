<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use yjrj\QQWry;
use yjrj\Wechat;

class System extends Base
{
    public function index()
    {
        if (Request::isPost()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，系统设置无法修改！', 0);
            }
            $System = new model\System();
            $systemForm = $System->form();
            if (is_numeric($systemForm)) {
                $output = "<?php

return [
    'session_key_admin' => '" . Config::get('system.session_key_admin') . "',  //主后台session key
    'session_key_distributor' => '" . Config::get('system.session_key_distributor') . "',  //分销商后台session key
    'session_key_index' => '" . Config::get('system.session_key_index') . "',  //前台session key
    'pass_key' => '" . Config::get('system.pass_key') . "',  //密码的盐
    'reset_pass_key' => '" . Config::get('system.reset_pass_key') . "',  //重置密码的密钥

    'openid' => '" . str_replace("'", "\'", Request::post('openid')) . "',  //OpenID
    'web_name' => '" . str_replace("'", "\'", Request::post('web_name')) . "',  //站点名称
    'admin_mail' => '" . str_replace("'", "\'", Request::post('admin_mail')) . "',  //管理员邮箱
    'www' => '" . str_replace("'", "\'", Request::post('www', 0)) . "',  //是否强制www
    'https' => '" . str_replace("'", "\'", Request::post('https', 0)) . "',  //是否强制https
    'manager_enter' => '" . str_replace("'", "\'", Request::post('manager_enter')) . "',  //管理员后台入口
    'distributor_enter' => '" . str_replace("'", "\'", Request::post('distributor_enter')) . "',  //分销商后台入口
    'copyright_backend' => '" . str_replace("'", "\'", Request::post('copyright_backend', '', null))
                    . "',  //登录页版权
    'register' => '" . str_replace("'", "\'", Request::post('register')) . "',  //是否开放注册
    'register_verify' => '" . str_replace("'", "\'", Request::post('register_verify')) . "',  //注册审核
    'home_page' => '" . str_replace("'", "\'", Request::post('home_page')) . "',  //首页显示
    'lists_id' => '" . str_replace("'", "\'", Request::post('lists_id', 0)) . "',  //列表页id
    'item_id' => '" . str_replace("'", "\'", Request::post('item_id', 0)) . "',  //商品页id
    'index_php' => '" . (Request::post('index_php') == 0 ? 'index.php/' : '') . "',  //隐藏index.php
    'big_pic' => '" . str_replace("'", "\'", Request::post('big_pic')) . "',  //点击图片看大图
    'code' => '" . str_replace("'", "\'", Request::post('code', '', null)) . "',  //全局第三方代码
    'order_time' => '" . str_replace("'", "\'", Request::post('order_time')) . "',  //防刷单间隔
    'order_search' => '" . str_replace("'", "\'", Request::post('order_search')) . "',  //订单查询
    'order_search_step' => '" . str_replace("'", "\'", Request::post('order_search_step')) . "',  //跨模板查询
    'alipay_app_id' => '" . str_replace("'", "\'", Request::post('alipay_app_id')) . "',  //支付宝APPID
    'alipay_merchant_private_key' => '" . str_replace("'", "\'", Request::post('alipay_merchant_private_key')) .
                    "',  //支付宝应用私钥
    'alipay_public_key' => '" . str_replace("'", "\'", Request::post('alipay_public_key')) . "',  //支付宝公钥
    'wechat_pay_app_secret' => '" . str_replace("'", "\'", Request::post('wechat_pay_app_secret')) .
                    "',  //微信支付AppSecret
    'wechat_pay_mch_id' => '" . str_replace("'", "\'", Request::post('wechat_pay_mch_id')) . "',  //微信支付商户号
    'wechat_pay_key' => '" . str_replace("'", "\'", Request::post('wechat_pay_key')) . "',  //微信支付商户密钥
    'wechat_pay_cert_serial_number' => '" . str_replace("'", "\'", Request::post('wechat_pay_cert_serial_number')) .
                    "',  //微信支付证书序列号
    'wechat_pay_cert_private_key' => '" . str_replace("'", "\'", Request::post('wechat_pay_cert_private_key')) .
                    "',  //微信支付证书私钥
    'qq_app_id' => '" . str_replace("'", "\'", Request::post('qq_app_id')) . "',  //QQ互联AppID
    'qq_app_key' => '" . str_replace("'", "\'", Request::post('qq_app_key')) . "',  //QQ互联AppKey
    'qq_bridge_domain' => '" . Config::get('system.qq_bridge_domain') . "',  //QQ互联回调域名
    'mail_order_subject' => '" . str_replace("'", "\'", Request::post('mail_order_subject')) . "',  //订单提醒邮件标题
    'mail_order_content' => '" . str_replace("'", "\'", Request::post('mail_order_content', '', null)) .
                    "',  //订单提醒邮件内容
    'mail_pay_subject' => '" . str_replace("'", "\'", Request::post('mail_pay_subject')) . "',  //支付提醒邮件标题
    'mail_pay_content' => '" . str_replace("'", "\'", Request::post('mail_pay_content', '', null)) .
                    "',  //支付提醒邮件内容
    'mail_send_subject' => '" . str_replace("'", "\'", Request::post('mail_send_subject')) . "',  //发货提醒邮件标题
    'mail_send_content' => '" . str_replace("'", "\'", Request::post('mail_send_content', '', null)) .
                    "',  //发货提醒邮件内容
    'sms_bao_user' => '" . str_replace("'", "\'", Request::post('sms_bao_user')) . "',  //短信宝账号
    'sms_bao_pass' => '" . str_replace("'", "\'", Request::post('sms_bao_pass')) . "',  //短信宝密码
    'sms_captcha_content' => '" . str_replace("'", "\'", Request::post('sms_captcha_content')) . "',  //验证码短信内容
    'sms_notify_content' => '" . str_replace("'", "\'", Request::post('sms_notify_content')) . "',  //短信通知内容
    'sms_backend_content' => '" . str_replace("'", "\'", Request::post('sms_backend_content')) . "',  //后台短信内容
    'wechat_app_id' => '" . str_replace("'", "\'", Request::post('wechat_app_id')) . "',  //微信公众平台AppID
    'wechat_app_secret' => '" . str_replace("'", "\'", Request::post('wechat_app_secret')) . "',  //微信公众平台AppSecret
    'wechat_bridge_domain' => '" . Config::get('system.wechat_bridge_domain') . "',  //微信公众平台回调域名
    'wechat_open_app_id' => '" . str_replace("'", "\'", Request::post('wechat_open_app_id')) . "',  //微信开放平台AppID
    'wechat_open_app_secret' => '" . str_replace("'", "\'", Request::post('wechat_open_app_secret')) .
                    "',  //微信开放平台AppSecret
    'wechat_open_bridge_domain' => '" . Config::get('system.wechat_open_bridge_domain') . "',  //微信开放平台回调域名
    'qiniu_access_key' => '" . str_replace("'", "\'", Request::post('qiniu_access_key')) . "',  //七牛云AccessKey
    'qiniu_secret_key' => '" . str_replace("'", "\'", Request::post('qiniu_secret_key')) . "',  //七牛云SecretKey
    'qiniu_bucket' => '" . str_replace("'", "\'", Request::post('qiniu_bucket')) . "',  //七牛云存储空间名称
    'qiniu_domain' => '" . str_replace("'", "\'", Request::post('qiniu_domain')) . "',  //七牛云存储空间域名
];
";

                if (file_put_contents(ROOT_DIR . '/config/diy/system.php', $output)) {
                    if (
                        Request::post('www', 0) != Config::get('system.www') ||
                        Request::post('https', 0) != Config::get('system.https')
                    ) {
                        file_put_contents(ROOT_DIR . '/.htaccess', '<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On' . (Request::post('www', 0) ? '
  RewriteCond %{HTTP_HOST} !^www\.(.*)
  RewriteRule (.*) http://www.%{HTTP_HOST}/$1 [R=301,L]' : '') . (Request::post('https', 0) ? '
  RewriteCond %{SERVER_PORT} !^443$
  RewriteRule ^.*$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R]' : '') . '
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php' . (strstr(php_uname('s'), 'Windows') ? ' [L,E=PATH_INFO:$1]' : '/$1 [QSA,PT,L]') . '
</IfModule>');
                    }

                    if (Request::post('manager_enter') != Config::get('system.manager_enter')) {
                        return
                            rename(
                                ROOT_DIR . '/' . Config::get('system.manager_enter'),
                                ROOT_DIR . '/' . Request::post('manager_enter')
                            ) ? showTip(['msg' => '管理员后台入口修改成功，即将跳转到新入口！',
                                         'url' => Config::get('url.web1') . Request::post('manager_enter')]) :
                                showTip('管理员后台入口修改失败，请检查系统根目录权限！', 0);
                    } else {
                        return showTip('系统设置修改成功！');
                    }
                } else {
                    return showTip('系统设置修改失败，请检查config目录权限！', 0);
                }
            } else {
                return showTip($systemForm, 0);
            }
        }
        Html::lists(Config::get('system.lists_id'));
        Html::item(Config::get('system.item_id'));
        View::assign(['IpVersion' => QQWry::getVersion()]);
        return $this->view();
    }

    public function wechatIp()
    {
        $Wechat = new Wechat([
            'app_id' => Config::get('system.wechat_app_id'),
            'app_secret' => Config::get('system.wechat_app_secret')
        ]);
        $Wechat->getAccessToken();
        $html = '<meta charset="utf-8">';
        if ($Wechat->errMsg == 'no access') {
            $html .= '<p>IP白名单配置正常，无需再配置</p>';
        } elseif (preg_match('/invalid ip (.*) ipv6 (.*), not in whitelist rid:/', $Wechat->errMsg, $ip)) {
            $html .= '<p>IPv4地址：' . $ip[1] . '</p><p>IPv6地址：' . $ip[2] . '（如果IPv6地址前包含::ffff:，则无需配置IPv6地址）</p>';
        } else {
            $html .= '<p>发生其它错误，错误信息：' . $Wechat->errMsg . '，如有疑问请联系客服处理</p>';
        }
        return $html;
    }
}
