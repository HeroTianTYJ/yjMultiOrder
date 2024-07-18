$(function () {
  // 注册验证
  $('.form').Validform({
    tiptype: function (msg) {
      if (msg === '通过信息验证！' || msg === '正在提交数据…') return;
      showTip(msg, 0);
    },
    showAllError: false,
    dragonfly: true,
    tipSweep: true
  }).addRule([{
    ele: 'input[name=name]',
    datatype: /^[\w\W]{1,20}$/,
    nullmsg: LANG['register']['validate']['name'],
    errormsg: LANG['register']['validate']['name2']
  }, {
    ele: 'input[name=pass]',
    datatype: '*',
    nullmsg: LANG['register']['validate']['pass']
  }, {
    ele: 'input[name=repass]',
    datatype: '*',
    recheck: 'pass',
    nullmsg: LANG['register']['validate']['repass'],
    errormsg: LANG['register']['validate']['repass2']
  }]);
});
