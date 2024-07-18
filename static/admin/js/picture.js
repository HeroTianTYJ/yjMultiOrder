$(function () {
  let moduleName = '';

  if (CONFIG['TYPE'] === 'directory') {
    moduleName = '图片目录';
    let $tool = $('.tool');

    // 删除
    remove(moduleName);

    // 清理小程序码
    $tool.on('click', '.clear_qrcode', function () {
      if (CONFIG['QRCODE_COUNT'] > 0) {
        confirmLayer(
          CONFIG['CLEAR_QRCODE'],
          {},
          '<h3><span>？</span>确定要清理微信小程序码吗？</h3><p>您的服务器中当前存在' + CONFIG['QRCODE_COUNT'] + '张微信小程序码，清理后，分销商将通过微信小程序接口重新获取小程序码。</p>',
          function (json, layerIndex) {
            if (json['state'] === 1) layer.close(layerIndex);
          }
        );
      } else {
        showTip('您的服务器中当前不存在微信小程序码，无需清理！');
      }
    });

    // 同步图片至七牛云
    $tool.on('click', '.qiniu_synchronize', function () {
      ajaxMessageLayer(CONFIG['QINIU_SYNCHRONIZE'], '同步图片至七牛云', {}, function (index) {
        layer.close(index);
      });
    });
  } else if (CONFIG['TYPE'] === 'picture') {
    moduleName = '图片';

    // 清理冗余
    $('.group .clear-picture').on('click', function () {
      confirmLayer(
        CONFIG['CLEAR_PICTURE'],
        {id: CONFIG['ID']},
        '<h3><span>？</span>确定要清理冗余图片吗？</h3><p>清理冗余图片之后，无法进行恢复。</p>',
        function (json, layerIndex) {
          if (json['state'] === 1) {
            layer.close(layerIndex);
            setTimeout(function () {
              window.location.reload(true);
            }, 3000);
          }
        }
      );
    });
  }

  // 列表
  list(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
});

function listItem (item) {
  let html = '';
  if (CONFIG['TYPE'] === 'directory') {
    let control = [];
    if (isPermission('picture')) control.push('<a href="' + CONFIG['PICTURE'] + '?id=' + item['id'] + '">进入目录</a>');
    if (isPermission('delete') && parseInt(item['total1']) === 0) control.push('<a href="javascript:" class="delete">删除</a>');
    html = '<tr class="item' + item['id'] + '"><td class="none"><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['name'] + '</td><td>' + item['total1'] + '张</td><td>' + item['total2'] + '张</td><td>' + item['total3'] + '张</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
  } else if (CONFIG['TYPE'] === 'picture') {
    html = '<li><dl><dd><img src="' + (CONFIG['PICTURE_TYPE'] === '2' ? 'static/admin/images/cleaned_picture.png' : CONFIG['DIR_UPLOAD'] + CONFIG['ID'] + '/' + item['id']) + '" alt="' + item['id'] + '"></dd><dt>' + item['name'] + '</dt></dl></li>';
  }
  return html;
}
