$(function () {
  $('.hash').on('click', function () {
    location.hash = $(this).attr('hash');
  });

  $('.qrcode').on('click', function () {
    let $this = $(this);
    // if (CONFIG['WXXCX'] === '0') {
    $.ajax({
      type: 'POST',
      url: CONFIG['QRCODE'],
      data: {qrcode: CONFIG['UPLOAD_DIR'] + $this.attr('img').replace(/\[img=/, '').replace(/]/, '')}
    }).then(function (data) {
      layer.confirm(
        data,
        {
          title: $this.attr('tip'),
          closable: true,
          area: '500px',
          resizable: false,
          buttons: false
        }
      );
    });
    /* } else {
      wx.miniProgram.navigateTo({
        url: '/pages/connect/connect'
      });
    } */
  });

  $('img.lazy').lazyload({
    'load': function () {
      $(this).removeAttr('width').removeAttr('height');
    }
  });

  if (typeof layui !== 'undefined') {
    layui.use(['form'], function () {
      layui.form.on('select(page)', function (data) {
        if (location.href !== data.value) {
          location.href = data.value;
        }
      });
    });
  }
});
