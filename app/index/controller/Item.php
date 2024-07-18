<?php

namespace app\index\controller;

use app\common\controller\Template;
use app\index\library\Tool;
use app\index\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\facade\View;
use yjrj\Wechat;

class Item extends Base
{
    public function index($id = 0)
    {
        $id = $id ?: Request::param('id');
        if ($id) {
            $Item = new model\Item();
            $itemOne = $Item->one($id);
            if (!$itemOne) {
                return $this->failed(Request::param('id') ? '不存在此商品！' : '后台 - 系统设置 - 前台设置指定的首页已被删除，或被设置为了前台不显示，请重新设置！');
            }

            Tool::click($itemOne['id'], 'item');

            if (
                Config::get('system.wechat_app_id') && Config::get('system.wechat_app_secret') &&
                in_array(device(), ['androidWechat', 'iphoneWechat', 'windowsWechat', 'macWechat'])
            ) {
                $Wechat = new Wechat([
                    'app_id' => Config::get('system.wechat_app_id'),
                    'app_secret' => Config::get('system.wechat_app_secret')
                ]);
                $itemOne['share'] = $Wechat->getShareConfig();
            } else {
                $itemOne['share'] = false;
            }

            if ($itemOne['tag']) {
                $itemOne['tag'] = explode("\r\n", $itemOne['tag']);
            }
            $itemOne['sale'] = $itemOne['sale_minute'] ? ($itemOne['sale'] + ceil((time() - $itemOne['date']) / 60 /
                    $itemOne['sale_minute']) * $itemOne['sale_count']) : 0;
            $itemOne['save'] = ($itemOne['price1'] ?: 0) - ($itemOne['price2'] ?: 0);

            $Text = new model\Text();
            $itemOne['code'] = $Text->content($itemOne['text_id_code']);
            $itemOne['buy'] = $Text->content($itemOne['text_id_buy']);
            $itemOne['procedure'] = $Text->content($itemOne['text_id_procedure']);
            $itemOne['introduce'] = $Text->content($itemOne['text_id_introduce']);
            $itemOne['service'] = $Text->content($itemOne['text_id_service']);
            $itemOne['comment'] = $Text->content($itemOne['text_id_comment']);
            $itemOne['column_content1'] = $Text->content($itemOne['text_id_column_content1']);
            $itemOne['column_content2'] = $Text->content($itemOne['text_id_column_content2']);
            $itemOne['column_content3'] = $Text->content($itemOne['text_id_column_content3']);
            $itemOne['column_content4'] = $Text->content($itemOne['text_id_column_content4']);
            $itemOne['column_content5'] = $Text->content($itemOne['text_id_column_content5']);
            $itemOne['nav'] = $Text->content($itemOne['text_id_nav']);

            $pictureStr = '';
            if ($itemOne['picture']) {
                $picture = explode(',', $itemOne['picture']);
                $pictureStr = '<div class="banner"><ul class="swiper-wrapper">';
                foreach ($picture as $value) {
                    $pictureStr .= '<li class="swiper-slide"><img src="' . (Config::get('system.qiniu_domain') ?
                            Config::get('system.qiniu_domain') : Config::get('dir.upload')) . $value . '?' .
                        staticCache() . '" alt="商品主图"></li>';
                }
                $pictureStr .= '</ul><span class="arrow prev">&lt;</span><span class="arrow next">&gt;</span>' .
                    '<div class="pagination"></div></div>';
            }
            $itemOne['picture_str'] = $pictureStr;

            $comment = '';
            if ($itemOne['comment_type'] != '') {
                $commentType = explode(',', $itemOne['comment_type']);
                if (in_array(0, $commentType)) {
                    $comment .= $this->comment($itemOne['comment']);
                }
                if (in_array(1, $commentType) || in_array(2, $commentType)) {
                    $messageBoardOne = (new model\MessageBoard())->one($itemOne['message_board_id']);
                    if ($messageBoardOne) {
                        if (in_array(1, $commentType)) {
                            $comment .= $this->messageBoard($messageBoardOne);
                        }
                        if (in_array(2, $commentType)) {
                            $comment .= $this->message($messageBoardOne);
                        }
                    }
                }
            }

            $order = ['', ''];
            if ($itemOne['template_id'] && (new model\Template())->one($itemOne['template_id'])) {
                preg_match(
                    '/<body>([\w\W]*)<\/body>/U',
                    (new Template())->html($itemOne['template_id'], [
                        'product_type' => $itemOne['product_type'],
                        'product_sort_ids' => $itemOne['product_sort_ids'],
                        'product_ids' => $itemOne['product_ids'],
                        'product_default' => $itemOne['product_default'],
                        'product_view_type' => $itemOne['product_view_type']
                    ]),
                    $order
                );
            }

            $itemOne['columnArr'] = [
                $itemOne['buy'] ? '
    <a class="anchor" id="buy"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>抢购描述</h2>
    <div class="content">
      ' . escapePic($itemOne['buy']) . '
    <a class="anchor" id="procedure"></a>
    </div><p class="clear"></p>' : '',
                $itemOne['procedure'] ? '
	<h2><span class="expand">▼</span><span class="fold">▶</span>购买流程</h2>
    <div class="content">
      ' . escapePic($itemOne['procedure']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['introduce'] ? '
    <a class="anchor" id="introduce"></a>
	<h2 id="introduce"><span class="expand">▼</span><span class="fold">▶</span>商品介绍</h2>
    <div class="content">
      ' . escapePic($itemOne['introduce']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['service'] ? '
    <a class="anchor" id="service"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>客户服务</h2>
    <div class="content">
      ' . escapePic($itemOne['service']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['tel'] || $itemOne['sms'] || $itemOne['qq'] ? '
    <a class="anchor" id="connect"></a>
    <h2><span class="expand">▼</span><span class="fold">▶</span>联系方式</h2>
    <div class="content btn">
      ' . ($itemOne['tel'] ?
                        '<a href="tel:' . $itemOne['tel'] . '" class="tel">咨询热线：' . $itemOne['tel'] . '</a>' : '') . '
      ' . ($itemOne['sms'] ?
                        '<a href="sms:' . $itemOne['sms'] . '" class="sms">短信订购：' . $itemOne['sms'] . '</a>' : '') . '
      ' . ($itemOne['qq'] ? '<a href="https://wpa.qq.com/msgrd?v=3&uin=' . $itemOne['qq'] .
                        '" target="_blank" class="qq">QQ咨询：' . $itemOne['qq'] . '</a>' : '') . '
    </div><p class="clear"></p>' : '',
                $comment ? '
    <a class="anchor" id="comment"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>客户评价</h2>
    <div class="content comment">' . $comment . '</div><p class="clear"></p>' : '',
                $itemOne['column_name1'] || $itemOne['column_content1'] ? '
    <a class="anchor" id="diy1"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>' . $itemOne['column_name1'] . '</h2>
    <div class="content">
      ' . escapePic($itemOne['column_content1']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['column_name2'] || $itemOne['column_content2'] ? '
    <a class="anchor" id="diy2"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>' . $itemOne['column_name2'] . '</h2>
    <div class="content">
      ' . escapePic($itemOne['column_content2']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['column_name3'] || $itemOne['column_content3'] ? '
    <a class="anchor" id="diy3"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>' . $itemOne['column_name3'] . '</h2>
    <div class="content">
      ' . escapePic($itemOne['column_content3']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['column_name4'] || $itemOne['column_content4'] ? '
    <a class="anchor" id="diy4"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>' . $itemOne['column_name4'] . '</h2>
    <div class="content">
      ' . escapePic($itemOne['column_content4']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['column_name5'] || $itemOne['column_content5'] ? '
    <a class="anchor" id="diy5"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>' . $itemOne['column_name5'] . '</h2>
    <div class="content">
      ' . escapePic($itemOne['column_content5']) . '
    </div><p class="clear"></p>' : '',
                $itemOne['template_id'] && isset($order[1]) ? '
    <a class="anchor" id="order"></a>
	<h2><span class="expand">▼</span><span class="fold">▶</span>在线订购</h2>
    <div class="content">' . $order[1] . '
    </div><p class="clear"></p>' : ''
            ];
            $itemOne['columnNav'] = [
                $itemOne['buy'] ? ['buy', '抢购描述'] : [],
                $itemOne['procedure'] ? ['procedure', '购买流程'] : [],
                $itemOne['introduce'] ? ['introduce', '商品介绍'] : [],
                $itemOne['service'] ? ['service', '客户服务'] : [],
                $itemOne['tel'] || $itemOne['sms'] || $itemOne['qq'] ? ['connect', '联系方式'] : [],
                $comment ? ['comment', '客户评价'] : [],
                $itemOne['column_name1'] || $itemOne['column_content1'] ? ['diy1', $itemOne['column_name1']] : [],
                $itemOne['column_name2'] || $itemOne['column_content2'] ? ['diy2', $itemOne['column_name2']] : [],
                $itemOne['column_name3'] || $itemOne['column_content3'] ? ['diy3', $itemOne['column_name3']] : [],
                $itemOne['column_name4'] || $itemOne['column_content4'] ? ['diy4', $itemOne['column_name4']] : [],
                $itemOne['column_name5'] || $itemOne['column_content5'] ? ['diy5', $itemOne['column_name5']] : [],
                $itemOne['template_id'] ? ['order', '在线订购'] : []
            ];

            $itemOne['sort'] = explode(',', $itemOne['sort']);

            $navStr = '';
            if ($itemOne['nav']) {
                $nav = explode("\r\n", $itemOne['nav']);
                $icon = explode("\r\n", $itemOne['icon']);
                $navStr = '<nav class="nav"><ul>';
                foreach ($nav as $key => $value) {
                    if (isset($icon[$key]) && strstr($icon[$key], '[img=')) {
                        preg_match('/\[img=(.*)]/U', $icon[$key], $img);
                        $value = str_replace(
                            '<strong>',
                            '<strong><span style="background:url(' . (Config::get('system.qiniu_domain') ?
                                Config::get('system.qiniu_domain') :
                                Config::get('url.web1') . Config::get('dir.upload')) . $img[1] .
                            ') no-repeat;"></span>',
                            $value
                        );
                    } else {
                        $value = str_replace('<strong>', '<strong><span class="' . ($icon[$key] ?? '') .
                            '"></span>', $value);
                    }
                    $navStr .= '<li>' . $value . '</li>';
                }
                $navStr .= '</ul></nav>';
            }
            $itemOne['nav_str'] = $navStr;

            View::assign(['One' => $itemOne]);
            return $this->view();
        } else {
            return $this->failed('非法操作！');
        }
    }

    private function comment($comment = '')
    {
        $html = '';
        if ($comment) {
            $html .= '<div class="scroll"><ul>';
            foreach (explode("\r\n", $comment) as $key => $value) {
                $html .= $key % 2 == 0 ?
                    '<li class="left"><span class="head"></span><span class="radius"></span><section>' . $value .
                    '</section></li>' :
                    '<li class="right"><section>' . $value .
                    '</section><span class="radius"></span><span class="head"></span></li>';
            }
            $html .= '</ul></div>';
        }
        return $html;
    }

    private function messageBoard($messageBoardOne)
    {
        $fieldArr = explode(',', $messageBoardOne['field']);
        $html = '<script type="text/javascript">
CONFIG["MESSAGE_ADD"] = "' . Route::buildUrl('/message/add') . '";
</script>
<div class="board">
  <form method="post" action="" class="message">
    <input type="hidden" name="message_board_id" value="' . $messageBoardOne['id'] . '">
    <dl>';
        if (in_array(0, $fieldArr)) {
            $html .= '<dd><span class="left">真实姓名：</span><span class="right">' .
                '<input type="text" name="name" class="text"></span></dd>';
        }
        if (in_array(1, $fieldArr)) {
            $html .= '<dd><span class="left">联系电话：</span><span class="right">' .
                '<input type="text" name="tel" class="text"></span></dd>';
        }
        if (in_array(2, $fieldArr)) {
            $html .= '<dd><span class="left">电子邮箱：</span><span class="right">' .
                '<input type="text" name="email" class="text"></span></dd>';
        }
        if (in_array(3, $fieldArr)) {
            $html .= '<dd class="textarea"><span class="left">留言内容：</span><span class="right">' .
                '<textarea name="content"></textarea></span></dd>';
        }
        if ($messageBoardOne['captcha_id']) {
            $captcha = Config::get('captcha');
            if (isset($captcha[$messageBoardOne['captcha_id']])) {
                $html .= '<dd><span class="left">验 证 码：</span><span class="right">' .
                    '<input type="text" name="captcha" class="text text2"></span></dd>' .
                    '<dd class="captcha"><span class="left"></span><span class="right"><img src="' .
                    Config::get('system.index_php') . 'common/captcha?id=' . $messageBoardOne['captcha_id'] .
                    '" alt="验证码" onClick="this.src=\'' . Config::get('system.index_php') . 'common/captcha?id=' .
                    $messageBoardOne['captcha_id'] . '&tm=\'+Math.random();" title="看不清？换一张"></span></dd>';
            }
        }
        $html .= '<dd class="info">您的个人联系信息不会被公开</dd>
      <dd class="submit"><input type="submit" value="提交" class="submit"></dd>
    </dl>
  </form>
</div>';
        return $html;
    }

    private function message($messageBoardOne)
    {
        $messageTotalCount = (new model\Message())->totalCount($messageBoardOne['id']);
        return $messageTotalCount ? '<script type="text/javascript" src="static/yj.admin.ui/js/jquery/page.js?' .
            staticCache() . '"></script>
<script type="text/javascript">
CONFIG["MESSAGE"] = "' . Route::buildUrl('message/index', ['message_board_id' => $messageBoardOne['id']]) . '";
CONFIG["PAGE_SIZE"] = ' . $messageBoardOne['page'] . ';
CONFIG["TOTAL"] = ' . $messageTotalCount . ';
</script>
<div class="message_list">
  <div class="list"></div>
  <ul class="page"></ul>
  <p class="clear"></p>
</div>' : '';
    }
}
