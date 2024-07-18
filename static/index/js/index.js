$(function () {
  if (!$.cookie('referrer')) $.cookie('referrer', window.document.referrer || window.parent.document.referrer, {path: '/'});

  new Swiper('.banner', {
    loop: true,
    autoplay: true,
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
});
