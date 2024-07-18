$(function () {
  // 身份切换
  let $permit = $('.permit');
  let $payment = $('.payment');
  level($('input[name=level]:checked').val());
  $('input[name=level]').on('ifChecked', function () {
    level($(this).val());
  });
  function level (val) {
    switch (val) {
      case '1':
        $permit.hide();
        $payment.hide();
        break;
      case '2':
        $permit.show();
        $payment.hide();
        break;
      case '3':
        $permit.hide();
        $payment.show();
        break;
    }
  }
});
