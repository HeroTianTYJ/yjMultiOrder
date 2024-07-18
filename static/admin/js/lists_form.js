$(function () {
  // tab切换
  tabSwitch();

  // 颜色选择器
  $('input[name=bg_color]').colorPicker();

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

  // 版权信息插入图片
  let pickerId1 = '.copyright_picker';
  if ($(pickerId1).length) {
    let input = 'textarea[name=copyright]';
    let gallery = new Gallery({
      pick: {
        id: pickerId1,
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
      galleryConfig.id = pickerId1;
      gallery.dialog(galleryConfig);
    });
  }

  // 商品页类型切换
  let $item = $('.item');
  itemType($('input[name=item_type]:checked').val());
  $('input[name=item_type]').on('ifChecked', function () {
    itemType($(this).val());
  });
  function itemType (val) {
    switch (val) {
      case '0':
        $item.hide();
        break;
      case '1':
        $item.show();
        break;
    }
  }

  // 商品页多选
  let itemSelect = xmSelect.render({
    el: '.item_select',
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
      let itemIds = [];
      $.each(data['arr'], function (index, value) {
        itemIds.push(value.value);
      });
      itemIds.sort((num1, num2) => num1 - num2);
      $('input[name=item_ids]').val(itemIds.join(','));
    }
  });
  $.ajax({
    type: 'POST',
    url: CONFIG['AJAX'],
    data: {
      item_ids: $('input[name=item_ids]').val()
    },
    success: function (data) {
      itemSelect.update({
        data: JSON.parse(data)
      });
    }
  });

  // 插入轮播图
  let $banner = $('textarea[name=banner]');
  let pickerId2 = '.banner_picker1';
  if ($(pickerId2).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId2,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      text: true,
      insertCallback: function (value) {
        bannerText('[img=' + value + ']');
      }
    });
    $('.banner_choose1').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.text = true;
      galleryConfig.id = pickerId2;
      galleryConfig.insertCallback = function (value) {
        $.each(value, function (i, v) {
          bannerText('[img=' + v + ']');
        });
      };
      gallery.dialog(galleryConfig);
    });
  }
  let pickerId3 = '.banner_picker2';
  if ($(pickerId3).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId3,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      text: true,
      insertCallback: function (value) {
        bannerText('[img=' + value + ']|http://这里输入链接地址');
      }
    });
    $('.banner_choose2').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.text = true;
      galleryConfig.id = pickerId3;
      galleryConfig.insertCallback = function (value) {
        $.each(value, function (i, v) {
          bannerText('[img=' + v + ']|http://这里输入链接地址');
        });
      };
      gallery.dialog(galleryConfig);
    });
  }
  let pickerId4 = '.banner_picker3';
  if ($(pickerId4).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId4,
        label: '本地上传'
      },
      uploadConfig: uploadConfig,
      text: true,
      insertCallback: function (value) {
        bannerText('[img=' + value + ']|http://这里输入链接地址|这里输入描述信息');
      }
    });
    $('.banner_choose3').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.text = true;
      galleryConfig.id = pickerId4;
      galleryConfig.insertCallback = function (value) {
        $.each(value, function (i, v) {
          bannerText('[img=' + v + ']|http://这里输入链接地址|这里输入描述信息');
        });
      };
      gallery.dialog(galleryConfig);
    });
  }
  function bannerText (text) {
    $banner.val($banner.val().trim() + ($banner.val().trim() === '' ? '' : '\n') + text);
  }

  // 底部导航
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
      case 3:
        navText('<a href="javascript:" class="hash" hash="top"><strong>回到顶部</strong></a>');
        break;
      case 4:
        value = prompt('请输入链接到的地址：');
        value2 = prompt('请输入链接的文字：');
        if (value && value2) navText('<a href="' + value + '"><strong>' + value2 + '</strong></a>');
        break;
      case 5:
        value = prompt('请输入链接到的地址：');
        value2 = prompt('请输入链接的文字：');
        if (value && value2) navText('<a href="' + value + '" target="_blank"><strong>' + value2 + '</strong></a>');
        break;
    }
  });
  function navText (text) {
    $nav.val($nav.val().trim() + ($nav.val().trim() === '' ? '' : '\n') + text);
  }
  let pickerId5 = '.qrcode_picker';
  if ($(pickerId5).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId5,
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
      galleryConfig.id = pickerId5;
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
  let pickerId6 = '.icon_picker';
  if ($(pickerId6).length) {
    let gallery = new Gallery({
      pick: {
        id: pickerId6,
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
      galleryConfig.id = pickerId6;
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
  let pickerId7 = '.share_pic_picker';
  if ($(pickerId7).length) {
    let input = 'input[name=share_pic]';
    let gallery = new Gallery({
      pick: {
        id: pickerId7,
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
      galleryConfig.id = pickerId7;
      gallery.dialog(galleryConfig);
    });
  }
});
