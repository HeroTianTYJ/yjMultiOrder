$(function () {
  let $synchronize = $('.synchronize');
  let $progress = $('.progress');
  $synchronize.on('click', function () {
    $synchronize.addClass('disabled').attr('disabled', '');
    let time = 1;
    let timer = setInterval(function () {
      $progress.html('同步中，请耐心等待，不要刷新或关闭页面，已耗时：' + getTakeTime(time));
      time++;
    }, 1000);
    $.ajax({
      type: 'POST',
      url: CONFIG['QINIU_SYNCHRONIZE'] + '?action=do'
    }).then(function () {
      clearInterval(timer);
      $progress.html('同步完成，共耗时：' + getTakeTime(time));
      $synchronize.removeClass('disabled').removeAttr('disabled');
    });
  });
});
