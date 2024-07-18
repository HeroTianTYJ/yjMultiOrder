$(function () {
  // 类型切换
  let $length = $('.length');
  type($('input[name=type]:checked').val());
  $('input[name=type]').on('ifChecked', function () {
    type($(this).val());
  });
  function type (val) {
    switch (val) {
      case '0':
        $length.show();
        break;
      case '1':
        $length.show();
        break;
      case '2':
        $length.hide();
        break;
    }
  }
});
