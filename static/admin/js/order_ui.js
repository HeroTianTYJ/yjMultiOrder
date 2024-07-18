$(function () {
  // 修改
  $('.form').on('submit', function (e) {
    $.ajax({
      type: 'POST',
      async: false,
      data: $(this).serialize(),
      success: function (data) {
        let json = JSON.parse(data);
        showTip(json['content'], json['state']);
        e.preventDefault();
      }
    });
  });

  // 拖拽
  $('.main ul').sortable({
    cursor: 'move',
    items: 'li',
    opacity: 0.6
  });

  // 恢复默认
  $('.restore').on('click', function () {
    commonAjax(CONFIG['RESTORE'], {}, true);
  });
});
