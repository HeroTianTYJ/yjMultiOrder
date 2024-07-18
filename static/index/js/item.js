$(function () {
  // 右上角导航
  new IScroll('.header .container .top-nav', {scrollY: true, scrollbars: true, click: true});
  let $topNav = $('.header .container .top-nav');
  let $headerContainerSpan = $('.header .container span');
  $headerContainerSpan.on('click', function (e) {
    e.stopPropagation();
    let $this = $(this);
    if ($this.hasClass('hover')) {
      $this.removeClass('hover');
      $topNav.addClass('hidden');
    } else {
      $this.addClass('hover');
      $topNav.removeClass('hidden');
    }
  });
  $(document).on('click', function () {
    $topNav.addClass('hidden');
    $headerContainerSpan.removeClass('hover');
  });

  // 商品主图
  new Swiper('.banner', {
    pagination: {
      el: '.pagination',
      clickable: true
    },
    navigation: {
      prevEl: '.prev',
      nextEl: '.next'
    }
  }).on('click', function () {
  });

  // 促销倒计时
  let $countdown = $('.countdown');
  if ($countdown.length) {
    let timer = setInterval(function () {
      if (CONFIG['COUNTDOWN'] >= 0) {
        let day = Math.floor(CONFIG['COUNTDOWN'] / 86400);
        let hour = Math.floor((CONFIG['COUNTDOWN'] - day * 86400) / 3600);
        let minute = Math.floor((CONFIG['COUNTDOWN'] - day * 86400 - hour * 3600) / 60);
        let second = Math.floor(CONFIG['COUNTDOWN'] % 60);
        if (day < 10) day = '0' + day;
        if (hour < 10) hour = '0' + hour;
        if (minute < 10) minute = '0' + minute;
        if (second < 10) second = '0' + second;
        $countdown.html('<span>' + day + '</span>:' + '<span>' + hour + '</span>:<span>' + minute + '</span>:<span>' + second + '</span>');
        --CONFIG['COUNTDOWN'];
      } else {
        $countdown.html('<span>活动已经结束</span>');
        clearInterval(timer);
      }
    }, 1000);
  }

  // 栏目内容收起/展开
  $('.article h2').on('click', function () {
    let $this = $(this);
    $this.find('.fold').toggle();
    $this.find('.expand').toggle();
    $this.next().toggle('fast');
  });

  // 滚动评价
  let $commentScroll = $('.comment .scroll');
  if ($commentScroll.length) {
    let liHeight = $commentScroll.find('ul li').height();
    setInterval(function () {
      $commentScroll.find('ul').prepend($commentScroll.find('ul li:last').height(0).animate({height: liHeight + 'px'}, 'slow'));
    }, 3000);
  }

  // 用户留言提交
  let $message = $('.message');
  $message.on('submit', function (e) {
    $.ajax({
      type: 'POST',
      url: CONFIG['MESSAGE_ADD'],
      async: false,
      data: $(this).serialize(),
      success: function (data) {
        let json = JSON.parse(data);
        tip(json['content']);
        if (json['state'] === 1) {
          $message.find('input[type=text]').val('');
          $message.find('textarea').val('');
          $message.find('img').attr('src', $message.find('img').attr('src') + '&tm=' + Math.random());
          tip('留言提交成功，管理员审核后即可显示在留言区！');
        }
        e.preventDefault();
      }
    });
  });

  // 用户留言列表
  let $messageList = $('.message_list');
  if ($messageList.length) {
    paging(1);
    createPage();
  }
  function createPage () {
    $messageList.find('.page').createPage({
      total: CONFIG['TOTAL'],
      pageSize: CONFIG['PAGE_SIZE'],
      pageCount: Math.ceil(CONFIG['TOTAL'] / CONFIG['PAGE_SIZE']),
      paging: paging
    });
  }
  function paging (page) {
    $.ajax({
      type: 'POST',
      url: CONFIG['MESSAGE'],
      data: {
        page: page
      },
      success: function (data) {
        let json = JSON.parse(data);
        let html = '';
        if (json) {
          $messageList.find('.list').empty();
          $.each(json, function (index, value) {
            html += '<div><p class="content"><strong>' + value['name'] + '：</strong><span>' + value['content'] + '</span></p><p class="date">' + value['date'] + '</p>';
            if (value['reply']) html += '<p class="reply">▶回复：' + value['reply'] + '</p>';
            html += '</div>';
          });
          $messageList.find('.list').html(html);
        }
      }
    });
  }

  function tip (tip) {
    if (tip === '通过信息验证！') return;
    $('.tip').html(tip).show();
    setTimeout(function () {
      $('.tip').hide();
    }, 3000);
  }
});
