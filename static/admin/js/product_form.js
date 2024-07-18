$(function () {
  // 销售属性
  let $attrParent = $('.attr_parent');
  // 添加
  let key = 4;
  $attrParent.on('click', '.add_attr', function () {
    $('.own_price,.msg').show();
    let length = $('.attr .append div').length;
    $($('.attr_inner').html().replace(/name="attr_k"/g, 'name="attr_k[' + key + ']"').replace(/name="attr_v"/g, 'name="attr_v[' + key + '][]"')).appendTo('.attr .append');
    key++;
    if (length >= 3) $('.add_attr').addClass('disabled').attr('disabled', '');
    $('.layui-layer-content').animate({scrollTop: 515});
  });
  // 删除
  $attrParent.on('click', '.delete_attr', function () {
    $(this).parent().parent().remove();
    $('.add_attr').removeClass('disabled').removeAttr('disabled');
    if ($('.attr .append div').length === 0) {
      $('.own_price,.msg,.interval').hide();
      $('.tree').html('');
    }
  });
  // 设置独立价格
  $attrParent.on('click', '.add_own_price', function () {
    let $tree = $('.tree');
    if ($tree.html() && !confirm('检测到已经有独立价格的设置数据，如果点击确定按钮将清空之前设置的数据，并重新生成价格列表')) return;

    let html = '';
    let $attrAppendDiv = $('.attr .append div');
    let length = $attrAppendDiv.length;
    let scrollTop = 0;

    if (length === 1) {
      $.each($attrAppendDiv.eq(0).find('.attr_v'), function () {
        if ($(this).val()) html += '<p>' + $('.attr_k').eq(0).val() + ' - ' + $(this).val() + '：<input type="text" name="own_price[]" value="' + $('input[name=price]').val() + '" class="text"></p>';
      });
      scrollTop = 690;
    } else if (length === 2) {
      $.each($attrAppendDiv.eq(0).find('.attr_v'), function (index) {
        if ($(this).val()) {
          html += '<p>' + $('.attr_k').eq(0).val() + ' - ' + $(this).val() + '</p><div class="tree1">';
          $.each($attrAppendDiv.eq(1).find('.attr_v'), function () {
            if ($(this).val()) html += '<p>' + $('.attr_k').eq(1).val() + ' - ' + $(this).val() + '：<input type="text" name="own_price[' + index + '][]" value="' + $('input[name=price]').val() + '" class="text"></p>';
          });
          html += '</div>';
        }
      });
      scrollTop = 805;
    } else if (length === 3) {
      $.each($attrAppendDiv.eq(0).find('.attr_v'), function (index) {
        if ($(this).val()) {
          html += '<p>' + $('.attr_k').eq(0).val() + ' - ' + $(this).val() + '</p><div class="tree1">';
          $.each($attrAppendDiv.eq(1).find('.attr_v'), function (index2) {
            if ($(this).val()) {
              html += '<p>' + $('.attr_k').eq(1).val() + ' - ' + $(this).val() + '</p><div class="tree2">';
              $.each($attrAppendDiv.eq(2).find('.attr_v'), function () {
                if ($(this).val()) html += '<p>' + $('.attr_k').eq(2).val() + ' - ' + $(this).val() + '：<input type="text" name="own_price[' + index + '][' + index2 + '][]" value="' + $('input[name=price]').val() + '" class="text"></p>';
              });
              html += '</div>';
            }
          });
          html += '</div></div>';
        }
      });
      scrollTop = 920;
    } else if (length === 4) {
      $.each($attrAppendDiv.eq(0).find('.attr_v'), function (index) {
        if ($(this).val()) {
          html += '<p>' + $('.attr_k').eq(0).val() + ' - ' + $(this).val() + '</p><div class="tree1">';
          $.each($attrAppendDiv.eq(1).find('.attr_v'), function (index2) {
            if ($(this).val()) {
              html += '<p>' + $('.attr_k').eq(1).val() + ' - ' + $(this).val() + '</p><div class="tree2">';
              $.each($attrAppendDiv.eq(2).find('.attr_v'), function (index3) {
                if ($(this).val()) {
                  html += '<p>' + $('.attr_k').eq(2).val() + ' - ' + $(this).val() + '</p><div class="tree3">';
                  $.each($attrAppendDiv.eq(3).find('.attr_v'), function () {
                    if ($(this).val()) html += '<p>' + $('.attr_k').eq(3).val() + ' - ' + $(this).val() + '：<input type="text" name="own_price[' + index + '][' + index2 + '][' + index3 + '][]" value="' + $('input[name=price]').val() + '" class="text"></p>';
                  });
                  html += '</div>';
                }
              });
              html += '</div>';
            }
          });
          html += '</div></div>';
        }
      });
      scrollTop = 1035;
    }

    $tree.html(html);
    $('.delete_own_price,.interval').show();
    $('.layui-layer-content').animate({scrollTop: scrollTop});
  });
  // 删除独立价格
  $attrParent.on('click', '.delete_own_price', function () {
    if (confirm('您将删除您设置的独立价格数据，此操作不可恢复，您确定删除么？')) {
      $('.tree').html('');
      $(this).hide();
      $('.interval').hide();
    }
  });
});
