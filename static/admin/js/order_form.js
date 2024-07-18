$(function () {
  // 商品属性
  let $textareaAttr = $('textarea[name=attr]');
  let $price = $('input[name=price]');
  let $commission = $('input[name=commission]');
  let $attr1 = $('.attr1');
  let $attr2 = $('.attr2');
  if ($price.val() === '') attr($('.product_id'));
  layui.use(['form'], function () {
    layui.form.on('select(product_id)', function (data) {
      attr($(data.elem));
    });
  });
  function attr (element) {
    let $pro = element.find('option:selected');
    let html = '';
    let attr = $pro.attr('attr');
    let ownPrice = $pro.attr('own_price');
    if (attr) {
      $.each(attr.split('|'), function (index, value) {
        let temp = value.split(':');
        html += '<tr><td>' + temp[0] + '：</td><td>';
        $.each(temp[1].split(','), function (index2, value2) {
          html += '<div class="radio-box"><label><input type="radio" name="attr_v' + index + '" value="' + index2 + '" val="' + temp[0] + '：' + value2 + '" class="attr_v' + (ownPrice ? ' own_price' : '') + '">' + value2 + '</label></div>';
        });
        html += '</td></tr>';
      });
      $attr1.html(html);
      iCheck();
      $attr2.removeClass('none');
    } else {
      $attr1.html('');
      $attr2.addClass('none');
    }
    if (ownPrice) {
      $price.val('').attr('placeholder', '请选全商品属性后查看总价');
      $commission.val('');
    } else {
      $price.val($pro.attr('price'));
      $commission.val(($pro.attr('price') * $pro.attr('commission') / 100).toFixed(2));
    }
    $textareaAttr.val('');
  }
  $attr1.on('ifChecked', '.attr_v', function () {
    let attr = '';
    $.each($('.attr_v:checked'), function () {
      attr += $(this).attr('val') + '\r\n';
    });
    $textareaAttr.val(attr.substring(0, attr.length - 2));
  });
  $attr1.on('ifChecked', '.own_price', function () {
    let $ownPriceChecked = $('.own_price:checked');
    if ($ownPriceChecked.length === $attr1.find('tr').length) {
      let $pro = $('.product_id option:selected');
      let length = $pro.attr('attr').split('|').length;
      let json = JSON.parse($pro.attr('own_price').replace(new RegExp(/'/g), '"'));
      let price = 0;
      if (length === 1) {
        price = json[$ownPriceChecked.eq(0).val()] || $pro.attr('price');
      } else if (length === 2) {
        price = json[$ownPriceChecked.eq(0).val()][$ownPriceChecked.eq(1).val()] || $pro.attr('price');
      } else if (length === 3) {
        price = json[$ownPriceChecked.eq(0).val()][$ownPriceChecked.eq(1).val()][$ownPriceChecked.eq(2).val()] || $pro.attr('price');
      } else if (length === 4) {
        price = json[$ownPriceChecked.eq(0).val()][$ownPriceChecked.eq(1).val()][$ownPriceChecked.eq(2).val()][$ownPriceChecked.eq(3).val()] || $pro.attr('price');
      }
      $price.val(price);
      $commission.val((price * $pro.attr('commission') / 100).toFixed(2));
    }
  });

  // 所在地区类型切换
  let $district1 = $('.district1');
  let $district2 = $('.district2');
  districtType($('input[name=district_type]:checked').val());
  $('input[name=district_type]').on('ifChecked', function () {
    districtType($(this).val());
  });
  function districtType (val) {
    switch (val) {
      case '0':
        $district1.show();
        $district2.hide();
        break;
      case '1':
        $district1.hide();
        $district2.show();
        break;
    }
  }
});
