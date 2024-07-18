$(function () {
  layui.use(['date'], function () {
    // 时间
    layui.date.render({
      type: 'datetime',
      elem: 'input[name=date]'
    });
  });
});
