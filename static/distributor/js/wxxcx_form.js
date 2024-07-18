$(function () {
  let $qrcode = $('.qrcode');
  let $url = $('.url');
  let $tip = $('.tip');

  // 页面类型切换
  let $buttonTd = $('.button_td');
  let $select = $('.select');
  type($('input[name=type]:checked').val());
  $('input[name=type]').on('ifChecked', function () {
    reset();
    type($(this).val());
  });
  function type (val) {
    $buttonTd.attr('rowspan', 2);
    $select.hide();
    switch (val) {
      case '0':
        $select.eq(0).show();
        break;
      case '1':
        $buttonTd.attr('rowspan', 1);
        break;
      case '2':
        $select.eq(1).show();
        break;
      case '3':
        $select.eq(2).show();
        break;
      case '4':
        $select.eq(3).show();
        break;
    }
  }

  // 页面切换
  layui.use(['form'], function () {
    // 商品
    layui.form.on('select(product_id)', function () {
      reset();
    });
    // 品牌分类
    layui.form.on('select(category_id)', function () {
      reset();
    });
    // 品牌
    layui.form.on('select(brand_id)', function () {
      reset();
    });
    // 商品标签
    layui.form.on('select(product_label_id)', function () {
      reset();
    });
  });

  // 推广
  $('.button').on('click', function () {
    $.ajax({
      type: 'POST',
      url: CONFIG['GET_SHARE'],
      data: $('form.share').serialize()
    }).then(function (data) {
      let json = JSON.parse(data);
      if (json['state'] === 0) {
        showTip(json['content'], 0);
      } else if (json['state'] === 1) {
        $qrcode.html('<img src="' + json['content']['qrcode'] + '" alt="小程序码" style="width:250px;height:250px;">');
        $url.html('<a href="' + json['content']['url'] + '" target="_blank">' + json['content']['url'] + '</a>');
        $tip.find('td:nth-child(2)').html(LANG['wxxcx']['share_tip']);
      }
    });
  });

  // 重置界面
  function reset () {
    $qrcode.html('');
    $url.html('');
    $tip.find('td:nth-child(2)').html('');
  }
});
