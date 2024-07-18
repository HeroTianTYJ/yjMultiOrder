$(function () {
  // 交互密钥
  let $submitKey = $('input[name=submit_key]');
  if ($submitKey.val() === '') $submitKey.val(getKey(40));
  $('.submit_key').on('click', function () {
    $submitKey.val(getKey(40));
  });

  // 首页类型切换
  let $lists = $('.lists');
  let $item = $('.item');
  type($('input[name=type]:checked').val());
  $('input[name=type]').on('ifChecked', function () {
    type($(this).val());
  });
  function type (val) {
    switch (val) {
      case '0':
        $lists.show();
        $item.hide();
        break;
      case '1':
        $lists.hide();
        $item.show();
        break;
      case '2':
        $lists.hide();
        $item.hide();
        break;
    }
  }
});
