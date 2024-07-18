$(function () {
  if (!$.cookie('referrer')) $.cookie('referrer', window.document.referrer || window.parent.document.referrer, {path: '/'});

  $('.left').height($(window).height() - 87);
  $('.right').css('min-height', $(window).height() - 87);

  let myScroll = new IScroll('.left', {scrollY: true, scrollbars: true, click: true});
  /*
  let $ul = $('.container .left ul');
  let $li = $('.container .left ul li');
  let $liActive = $('.container .left ul li.active');
  let $bg = $('#bg');
  myScroll.scrollToElement('.action', true, true);
  myScroll.on('scrollEnd', function () {
    let transform = $ul.css('transform').split(',');
    let top = $liActive.attr('index') * 42;
    $bg.css('top', top + parseInt(transform[transform.length - 1].replace(')', '')));
  });

  let bg = document.getElementById('bg');
  let transform = $ul.css('transform').split(',');
  let top = $liActive.attr('index') * 42;
  $bg.css('top', top + parseInt(transform[transform.length - 1].replace(')', '')));
  $li.on('mouseover', function () {
    let transform = $ul.css('transform').split(',');
    move(bg, $(this), $(this).attr('index') * 42 + parseInt(transform[transform.length - 1].replace(')', '')));
    $li.removeClass('action');
  });
  $ul.on('mouseout', function () {
    let transform = $ul.css('transform').split(',');
    move(bg, $liActive, top + parseInt(transform[transform.length - 1].replace(')', '')));
  });

  function move (oDiv, action, target) {
    clearInterval(oDiv.timer);
    $li.removeClass('action');
    oDiv.timer = setInterval(function () {
      let top = parseInt(oDiv.style.top);
      let speed = (target - top) / 5;
      speed = speed > 0 ? Math.ceil(speed) : Math.floor(speed);
      if (Math.abs(target - top) < 2) {
        oDiv.style.top = target + 'px';
        action.addClass('action');
        clearInterval(oDiv.timer);
      } else {
        oDiv.style.top = top + speed + 'px';
      }
    }, 30);
  } */
});
