$(function () {
  height();
  $(window).on({resize: height});

  function height () {
    $('.tip').height($(window).height() - 100);
  }
});
