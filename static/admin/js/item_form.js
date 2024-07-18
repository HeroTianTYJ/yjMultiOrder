$(function () {
  // tab切换
  tabSwitch();

  // 图片插入配置
  let uploadConfig = {
    uploadDir: CONFIG['UPLOAD_DIR'],
    auto: true,
    server: CONFIG['UPLOAD_SERVER'],
    fileSingleSizeLimit: 10240000,
    threads: 1,
    accept: {
      extensions: 'bmp,gif,jpg,jpeg,png',
      mimeTypes: '.bmp,.gif,.jpg,.jpeg,.png'
    },
    compress: false,
    resize: false,
    duplicate: true
  };
  let galleryConfig = {
    pictureDir: CONFIG['PICTURE_DIR'],
    pictureList: CONFIG['PICTURE_LIST'],
    uploadDir: CONFIG['UPLOAD_DIR']
  };

  // 插入预览图
  let pickerId1 = '.preview_picker';
  if ($(pickerId1).length) {
    let input = 'input[name=preview]';
    let gallery = new Gallery({
      pick: {
        id: pickerId1,
        label: '本地上传',
        multiple: false
      },
      uploadConfig: uploadConfig,
      input: input
    });
    $('.preview_choose').on('click', function () {
      galleryConfig.multiple = false;
      galleryConfig.input = input;
      galleryConfig.id = pickerId1;
      gallery.dialog(galleryConfig);
    });
  }

  // 商品价格显示/隐藏
  let $price = $('.price');
  price($('input[name=is_show_price]:checked').val());
  $('input[name=is_show_price]').on('ifChecked', function () {
    price($(this).val());
  });
  function price (val) {
    switch (val) {
      case '0':
        $price.hide();
        break;
      case '1':
        $price.show();
        break;
    }
  }

  // 颜色选择器
  $('input[name=bg_color]').colorPicker();

  // 版权信息插入图片
  let pickerId2 = '.copyright_picker';
  if ($(pickerId2).length) {
    let input = 'textarea[name=copyright]';
    let gallery = new Gallery({
      pick: {
        id: pickerId2,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      input: input,
      text: true
    });
    $('.copyright_choose').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.input = input;
      galleryConfig.text = true;
      galleryConfig.id = pickerId2;
      gallery.dialog(galleryConfig);
    });
  }

  // 插入商品主图
  let pickerId3 = '.picture_picker';
  if ($(pickerId3).length) {
    let input = 'input[name=picture]';
    let gallery = new Gallery({
      pick: {
        id: pickerId3,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      input: input,
      beforeCallback: function (value) {
        if (value.length >= 9) {
          alert('最多只能插入9张商品主图');
          return false;
        }
      }
    });
    $('.picture_choose').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.input = input;
      galleryConfig.id = pickerId3;
      galleryConfig.beforeCallback = function (value) {
        if (value.length > 9) {
          alert('最多只能插入9张商品主图');
          return false;
        }
      };
      gallery.dialog(galleryConfig);
    });
  }

  // 文本编辑器插入图片
  let pickerId = ['buy', 'procedure', 'introduce', 'service', 'column1', 'column2', 'column3', 'column4', 'column5'];
  for (let i = 0; i < pickerId.length; i++) {
    if ($('.' + pickerId[i] + '_picker').length) {
      let gallery = new Gallery({
        pick: {
          id: '.' + pickerId[i] + '_picker',
          label: '本地上传'
        },
        text: true,
        input: '',
        uploadConfig: uploadConfig,
        beforeCallback: function () {
          if (CKEDITOR.instances['TextArea' + (i + 1)].mode !== 'wysiwyg') {
            alert('请在设计模式下插入图片');
          }
        },
        insertCallback: function (value) {
          CKEDITOR.instances['TextArea' + (i + 1)].insertHtml('<img alt="图片" src="' + CONFIG['WEB_URL'] + CONFIG['UPLOAD_DIR'] + value + '" />');
        }
      });
      $('.' + pickerId[i] + '_choose').on('click', function () {
        galleryConfig.multiple = true;
        galleryConfig.text = true;
        galleryConfig.id = '.' + pickerId[i] + '_picker';
        galleryConfig.beforeCallback = function () {
          if (CKEDITOR.instances['TextArea' + (i + 1)].mode !== 'wysiwyg') {
            alert('请在设计模式下插入图片');
          }
        };
        galleryConfig.insertCallback = function (value) {
          if (value) {
            $.each(value, function (i2, v) {
              CKEDITOR.instances['TextArea' + (i + 1)].insertHtml('<img alt="图片" src="' + CONFIG['WEB_URL'] + CONFIG['UPLOAD_DIR'] + v + '" />');
            });
          }
        };
        gallery.dialog(galleryConfig);
      });
    }
  }

  // 文本编辑器变更监听
  setTimeout(function () {
    let textareaId = ['buy', 'procedure', 'introduce', 'service', 'column_content1', 'column_content2', 'column_content3', 'column_content4', 'column_content5'];
    for (let i = 0; i < pickerId.length; i++) {
      CKEDITOR.instances['TextArea' + (i + 1)].on('change', function () {
        $('textarea[name=' + textareaId[i] + ']').val(this.getData());
      });
    }
  }, 100);

  // 商品分类切换
  let $productSort1 = $('.product_sort1');
  let $product1 = $('.product1');
  let $productDefault1 = $('.product_default1');
  let $productSort2 = $('.product_sort2');
  let $product2 = $('.product2');
  let $productDefault2 = $('.product_default2');
  let $productTypeChecked = $('input[name=product_type]:checked');
  productType($productTypeChecked.val());
  $('input[name=product_type]').on('ifChecked', function () {
    productType($(this).val());
  });
  function productType (val) {
    switch (val) {
      case '0':
        $productSort1.show();
        $product1.show();
        $productDefault1.show();
        $productSort2.hide();
        $product2.hide();
        $productDefault2.hide();
        break;
      case '1':
        $productSort1.hide();
        $product1.hide();
        $productDefault1.hide();
        $productSort2.show();
        $product2.show();
        $productDefault2.show();
        break;
    }
  }

  // 商品单分类
  let $selectProductDefault1 = $('select[name=product_default1]');
  let $productIds1 = $('input[name=product_ids1]');
  let $productDefault = $('.product_default');
  product();
  layui.use(['form'], function () {
    layui.form.on('select(product_sort_id)', function () {
      product();
      $selectProductDefault1.html('');
      layui.form.render();
    });
  });
  let productIds1 = xmSelect.render({
    el: '.product_ids1',
    toolbar: {
      show: true
    },
    theme: {
      color: '#0059FF',
      hover: '#E4EBFF'
    },
    filterable: true,
    autoRow: true,
    on: function (data) {
      let html = '';
      let productIds = [];
      $.each(data['arr'], function (index, value) {
        html += '<option value="' + value['value'] + '" style="color:' + value['color'] + ';">' + value['name'] + '</option>';
        productIds.push(value['value']);
      });
      $selectProductDefault1.html(html);
      layui.use(['form'], function () {
        layui.form.render();
      });
      productIds.sort((num1, num2) => num1 - num2);
      $productIds1.val(productIds.join(','));
    }
  });
  function product () {
    $.ajax({
      type: 'POST',
      url: CONFIG['AJAX'],
      data: {
        product_sort_id: $('select[name=product_sort_id] option:selected').val(),
        product_ids1: $productIds1.val()
      },
      success: function (data) {
        productIds1.update({
          data: JSON.parse(data)
        });
        default1();
      }
    });
  }
  function default1 () {
    let html = '';
    $.each(productIds1.getValue(), function (index, value) {
      if ($.inArray(value['value'] + '', $productIds1.val().split(',')) !== -1 && $productTypeChecked.val() === '0') html += '<option value="' + value['value'] + '"' + (value['value'] + '' === $productDefault.val() ? 'selected' : '') + ' style="color:' + value['color'] + ';">' + value['name'] + '</option>';
    });
    $selectProductDefault1.html(html);
    layui.use(['form'], function () {
      layui.form.render();
    });
  }

  // 商品多分类
  let $selectProductDefault2 = $('select[name=product_default2]');
  let $productSortIds = $('input[name=product_sort_ids]');
  let $productIds2 = $('input[name=product_ids2]');
  product2();
  let productIds2 = xmSelect.render({
    el: '.product_ids2',
    toolbar: {
      show: true
    },
    theme: {
      color: '#0059FF',
      hover: '#E4EBFF'
    },
    filterable: true,
    autoRow: true,
    on: function (data) {
      let html = '';
      let productSortId = '';
      let productIds = [];
      $.each(data['arr'], function (index, value) {
        if (!new RegExp('<optgroup label="' + value['parent_name'] + '" style="color:' + value['parent_color'] + ';">').test(html)) {
          html += '<optgroup label="' + value['parent_name'] + '" style="color:' + value['parent_color'] + ';">';
          productSortId += value['parent_value'] + ',';
        }
        html += '<option value="' + value['value'] + '" style="color:' + value['color'] + ';"' + (value['value'] + '' === $productDefault.val() ? ' selected' : '') + '>' + value['name'] + '</option>';
        productIds.push(value['value']);
      });
      $selectProductDefault2.html(html);
      layui.use(['form'], function () {
        layui.form.render();
      });
      $productSortIds.val(productSortId.substring(0, productSortId.length - 1));
      productIds.sort((num1, num2) => num1 - num2);
      $productIds2.val(productIds.join(','));
    }
  });
  function product2 () {
    $.ajax({
      type: 'POST',
      url: CONFIG['AJAX2'],
      data: {
        product_ids2: $productIds2.val()
      },
      success: function (data) {
        productIds2.update({
          data: JSON.parse(data)
        });
        default2();
      }
    });
  }
  function default2 () {
    let html = '';
    let productSortId = '';
    $.each(productIds2.getValue(), function (index, value) {
      if (!new RegExp('<optgroup label="' + value['parent_name'] + '" style="color:' + value['parent_color'] + ';">').test(html)) {
        html += '<optgroup label="' + value['parent_name'] + '" style="color:' + value['parent_color'] + ';">';
        productSortId += value['parent_value'] + ',';
      }
      html += '<option value="' + value['value'] + '" style="color:' + value['color'] + ';"' + (value['value'] + '' === $productDefault.val() ? ' selected' : '') + '>' + value['name'] + '</option>';
    });
    $selectProductDefault2.html(html);
    $productSortIds.val(productSortId.substring(0, productSortId.length - 1));
    layui.use(['form'], function () {
      layui.form.render();
    });
  }

  // 客户评价
  let $comment = $('.comment');
  let $messageBoard = $('.message_board');
  comment();
  $('.comment_type').on('ifChecked', comment).on('ifUnchecked', comment);
  function comment () {
    $('.comment_type[value=0]').is(':checked') ? $comment.show() : $comment.hide();
    if ($('.comment_type[value=1]').is(':checked')) {
      $messageBoard.show();
      return;
    } else {
      $messageBoard.hide();
    }
    $('.comment_type[value=2]').is(':checked') ? $messageBoard.show() : $messageBoard.hide();
  }

  // 栏目排序
  $('.column_sort ul').sortable({
    cursor: 'move',
    items: 'li',
    opacity: 0.6
  });

  // 底部导航
  let nav = [
    '',
    '',
    '',
    '<a href="javascript:" class="hash" hash="connect"><strong>联系方式</strong></a>',
    '<a href="javascript:" class="hash" hash="order"><strong>在线订购</strong></a>',
    '<a href="javascript:" class="hash" hash="top"><strong>回到顶部</strong></a>',
    '<a href="javascript:" class="hash" hash="pic"><strong>商品主图</strong></a>',
    '<a href="javascript:" class="hash" hash="section"><strong>价格信息</strong></a>',
    '<a href="javascript:" class="hash" hash="buy"><strong>抢购描述</strong></a>',
    '<a href="javascript:" class="hash" hash="procedure"><strong>购买流程</strong></a>',
    '<a href="javascript:" class="hash" hash="introduce"><strong>商品介绍</strong></a>',
    '<a href="javascript:" class="hash" hash="service"><strong>客户服务</strong></a>',
    '<a href="javascript:" class="hash" hash="comment"><strong>客户评价</strong></a>',
    '',
    '<a href="javascript:" class="hash" hash="diy1"><strong>自定义1</strong></a>',
    '<a href="javascript:" class="hash" hash="diy2"><strong>自定义2</strong></a>',
    '<a href="javascript:" class="hash" hash="diy3"><strong>自定义3</strong></a>',
    '<a href="javascript:" class="hash" hash="diy4"><strong>自定义4</strong></a>',
    '<a href="javascript:" class="hash" hash="diy5"><strong>自定义5</strong></a>',
    '<a href="javascript:" class="hash" hash="footer"><strong>到底部</strong></a>',
    '',
    '',
    '',
    ''
  ];
  let $nav = $('textarea[name=nav]');
  $('.nav a').on('click', function () {
    if ($nav.val().trim().split('\n').length >= 3) {
      alert('最多支持3个导航链接');
      return;
    }
    let value = '';
    let value2 = '';
    switch ($(this).index()) {
      case 0:
        value = prompt('请输入QQ号码：');
        if (value) navText('<a href="https://wpa.qq.com/msgrd?v=3&uin=' + value + '" target="_blank"><strong>QQ咨询</strong></a>');
        break;
      case 1:
        value = prompt('请输入电话号码：');
        if (value) navText('<a href="tel:' + value + '"><strong>电话咨询</strong></a>');
        break;
      case 2:
        value = prompt('请输入短信号码：');
        if (value) navText('<a href="sms:' + value + '"><strong>短信咨询</strong></a>');
        break;
      case 20:
        value = prompt('请输入链接到的锚点：');
        value2 = prompt('请输入链接的文字：');
        if (value && value2) navText('<a href="javascript:" class="hash" hash="' + value + '"><strong>' + value2 + '</strong></a>');
        break;
      case 21:
        value = prompt('请输入链接到的地址：');
        value2 = prompt('请输入链接的文字：');
        if (value && value2) navText('<a href="' + value + '"><strong>' + value2 + '</strong></a>');
        break;
      case 22:
        value = prompt('请输入链接到的地址：');
        value2 = prompt('请输入链接的文字：');
        if (value && value2) navText('<a href="' + value + '" target="_blank"><strong>' + value2 + '</strong></a>');
        break;
      default:
        navText(nav[$(this).index()]);
    }
  });
  function navText (text) {
    $nav.val($nav.val().trim() + ($nav.val().trim() === '' ? '' : '\n') + text);
  }
  let pickerId4 = '.qrcode_picker';
  if ($(pickerId4).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId4,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      text: true,
      beforeCallback: function () {
        if ($nav.val().trim().split('\n').length >= 3) {
          alert('最多支持3个导航链接');
          return false;
        }
      },
      insertCallback: function (value) {
        navText('<a href="javascript:" class="qrcode" tip="使用微信扫一扫联系客服" img="[img=' + value + ']"><strong>微信客服</strong></a>');
      }
    });
    $('.qrcode_choose').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.text = true;
      galleryConfig.id = pickerId4;
      galleryConfig.beforeCallback = function (value) {
        if ($nav.val().trim().split('\n').length + value.length > 3) {
          alert('最多支持3个导航链接');
          return false;
        }
      };
      galleryConfig.insertCallback = function (value) {
        $.each(value, function (i, v) {
          navText('<a href="javascript:" class="qrcode" tip="使用微信扫一扫联系客服" img="[img=' + v + ']"><strong>微信客服</strong></a>');
        });
      };
      gallery.dialog(galleryConfig);
    });
  }

  // 导航图标
  let $icon = $('textarea[name=icon]');
  let icon = ['qq', 'tel', 'sms', 'home', 'cart', 'list', 'wx'];
  $('.icon a').on('click', function () {
    if ($icon.val().trim().split('\n').length >= 3) {
      alert('最多支持3个导航图标');
      return;
    }
    iconText(icon[$(this).index()]);
  });
  let pickerId5 = '.icon_picker';
  if ($(pickerId5).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId5,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      text: true,
      beforeCallback: function () {
        if ($icon.val().trim().split('\n').length >= 3) {
          alert('最多支持3个导航图标');
          return false;
        }
      },
      insertCallback: function (value) {
        iconText('[img=' + value + ']');
      }
    });
    $('.icon_choose').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.text = true;
      galleryConfig.id = pickerId5;
      galleryConfig.beforeCallback = function (value) {
        if ($icon.val().trim().split('\n').length + value.length > 3) {
          alert('最多支持3个导航图标');
          return false;
        }
      };
      galleryConfig.insertCallback = function (value) {
        $.each(value, function (i, v) {
          iconText('[img=' + v + ']');
        });
      };
      gallery.dialog(galleryConfig);
    });
  }
  function iconText (text) {
    $icon.val($icon.val().trim() + ($icon.val().trim() === '' ? '' : '\n') + text);
  }

  // 插入微信分享预览图
  let pickerId6 = '.share_pic_picker';
  if ($(pickerId6).length) {
    let input = 'input[name=share_pic]';
    let gallery = new Gallery({
      pick: {
        id: pickerId6,
        label: '本地上传',
        multiple: false
      },
      uploadConfig: uploadConfig,
      input: input,
      text: false
    });
    $('.share_pic_choose').on('click', function () {
      galleryConfig.multiple = false;
      galleryConfig.input = input;
      galleryConfig.text = false;
      galleryConfig.id = pickerId6;
      gallery.dialog(galleryConfig);
    });
  }
});
