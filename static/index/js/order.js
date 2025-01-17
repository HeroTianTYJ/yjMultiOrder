$(function () {
  if (!$.cookie('referrer')) $.cookie('referrer', window.document.referrer || window.parent.document.referrer, {path: '/'});
  let $referrer = $('input[name=referrer]');
  if ($referrer.val() === '') $referrer.val($.cookie('referrer'));

  let $district1 = $('.district1');
  let $district2 = $('.district2');
  districtType($('input[name=district_type]:checked').val());
  $('input[name=district_type]').on('click', function () {
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

  $('form.form').Validform({
    tiptype: function (msg) {
      tip(msg);
    },
    showAllError: false,
    dragonfly: true,
    tipSweep: true,
    // ignoreHidden: true,
    datatype: {
      'province': function (gets) {
        return $('input[name=district_type]:checked').val() === '1' ? true : gets !== '';
      },
      'county': function (gets) {
        return $('input[name=district_type]:checked').val() === '1' ? true : gets !== '';
      },
      'city': function (gets) {
        return $('input[name=district_type]:checked').val() === '1' ? true : gets !== '';
      },
      /* 'town': function(gets){
        return $('input[name=district_type]:checked').val() === '1' ? true : gets !== '';
      }, */
      'province2': function (gets) {
        return $('input[name=district_type]:checked').val() === '0' ? true : !(gets.length < 2 || gets.length > 10);
      },
      'county2': function (gets) {
        return $('input[name=district_type]:checked').val() === '0' ? true : !(gets.length < 2 || gets.length > 15);
      },
      'city2': function (gets) {
        return $('input[name=district_type]:checked').val() === '0' ? true : !(gets.length < 2 || gets.length > 15);
      },
      'town2': function (gets) {
        return $('input[name=district_type]:checked').val() === '0' ? true : gets.length <= 25;
      },
      'note': function (gets) {
        return gets.length <= 255;
      },
      'email': function (gets) {
        return !(gets !== '' && !/^[\w\-.]+@[\w\-.]+(.\w+)+$/.test(gets));
      }
    }
  }).addRule([{
    ele: 'input[name=count]',
    datatype: /^\d+$/,
    nullmsg: '请填写订购数量！',
    errormsg: '订购数量必须是数字！'
  }, {
    ele: 'input[name=name]',
    datatype: /^[\w\W]{2,20}$/,
    nullmsg: '请填写姓名！',
    errormsg: '姓名不得小于2位或大于20位！'
  }, {
    ele: 'input[name=tel]',
    datatype: /^[\d-]{7,20}$/,
    nullmsg: '请填写联系电话！',
    errormsg: '联系电话必须是数字和-号，且不得小于7位或大于20位！'
  }, {
    ele: 'input[name=province]',
    datatype: 'province',
    nullmsg: '请选择省份！'
  }, {
    ele: 'input[name=city]',
    datatype: 'city',
    nullmsg: '请选择城市！'
  }, {
    ele: 'input[name=county]',
    datatype: 'county',
    nullmsg: '请选择区/县！'
  },
  /* {
    ele: 'input[name=town]',
    datatype: 'town',
    nullmsg: '请选择乡镇/街道！'
  } */
  {
    ele: 'input[name=province2]',
    datatype: 'province2',
    nullmsg: '请填写省份！',
    errormsg: '省份不得小于2位或大于10位！'
  }, {
    ele: 'input[name=city2]',
    datatype: 'city2',
    nullmsg: '请填写城市！',
    errormsg: '城市不得小于2位或大于15位！'
  }, {
    ele: 'input[name=county2]',
    datatype: 'county2',
    nullmsg: '请填写区/县！',
    errormsg: '区/县不得小于2位或大于15位！'
  }, {
    ele: 'input[name=town2]',
    datatype: 'town2',
    errormsg: '乡镇/街道不得大于25位！'
  }, {
    ele: 'input[name=address]',
    datatype: /^[\w\W]{5,200}$/,
    nullmsg: '请填写详细地址！',
    errormsg: '详细地址不得小于5位或大于200位！'
  }, {
    ele: 'input[name=note]',
    datatype: 'note',
    errormsg: '备注不得大于255位！'
  }, {
    ele: 'input[name=email]',
    datatype: 'email',
    errormsg: '电子邮箱格式不合法！'
  }, {
    ele: 'input[name=verify]',
    datatype: '*',
    nullmsg: '请填写验证码！'
  }, {
    ele: 'input[name=sms_verify]',
    datatype: '*',
    nullmsg: '请填写短信验证码！'
  }]);

  $('.search').Validform({
    tiptype: function (msg) {
      tip(msg);
    },
    showAllError: false,
    dragonfly: true,
    tipSweep: true
  }).addRule([{
    ele: 'input[name=keyword]',
    datatype: '*',
    nullmsg: '请填写查询关键词！'
  }]);

  function getDateStr (addDayCount) {
    let day = new Date();
    day.setDate(day.getDate() + addDayCount);
    return day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
  }

  let $proSelectOption = $('.pro select option');
  let pro = $proSelectOption.length ? $proSelectOption : $('.pro label');
  function getList () {
    let str = '';
    for (let i = 0; i < 22; i++) {
      let addressRand = Math.floor(Math.random() * 22 + 1) - 1;
      let address = ['北京', '上海', '天津', '湖南', '湖北', '湖北', '广东', '广西', '重庆', '四川', '山东', '河南', '河北', '山西', '贵州', '黑龙江', '福建', '浙江', '江苏', '江西', '海南', '陕西'];
      let nameRand = Math.floor(Math.random() * 22 + 1) - 1;
      let name = ['张女士', '刘先生', '周女士', '朱先生', '陈女士', '田先生', '钟女士', '马先生', '韩女士', '吴先生', '顾女士', '王先生', '李女士', '卢先生', '崔女士', '段先生', '胡女士', '陈先生', '林女士', '代先生', '潘女士', '苏先生'];
      let telRand = Math.floor(Math.random() * 4 + 1) - 1;
      let tel = ['13' + Math.floor(Math.random() * 10) + '****' + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + '', '15' + Math.floor(Math.random() * 10) + '****' + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + '', '13' + Math.floor(Math.random() * 10) + '****' + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + '', '18' + Math.floor(Math.random() * 10) + '****' + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + '', '13' + Math.floor(Math.random() * 10) + '****' + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10) + ''];
      let logisticsRand = Math.floor(Math.random() * 4 + 1) - 1;
      let logistics = ['邮政EMS', '顺丰速递', '申通快递', '中通速递'];
      let productRand = Math.floor(Math.random() * pro.length + 1) - 1;
      str += '<dl><dt>' + getDateStr(-1) + ' ' + address[addressRand] + '的' + name[nameRand] + '（' + tel[telRand] + '）</dt><dd>您订购的 ' + pro.eq(productRand).text().replace(/（[\d.]+元）/, '').replace(/（[\d.]+元～[\d.]+元）/, '').replace(/└—/, '') + ' [' + logistics[logisticsRand] + '] 已发货，请注意查收</dd></dl>';
    }
    return str;
  }

  let $list = $('.list');
  let $list1 = $('.list1');
  let $list2 = $('.list2');
  if ($list.length && $list1.length && $list2.length) {
    $list1.html(getList());
    $list.height($('.left').height() - 100);
    $list2.html($list1.html());
    let mar = setInterval(marquee, 80);
    $list.on({
      mouseover: function () {
        clearInterval(mar);
      },
      mouseout: function () {
        mar = setInterval(marquee, 80);
      }
    });
  }
  function marquee () {
    if ($list2.get(0).offsetHeight - $list.get(0).scrollTop <= 0) {
      $list.get(0).scrollTop -= $list1.get(0).offsetHeight;
    } else {
      $list.get(0).scrollTop++;
    }
  }

  let $new = $('.new');
  let marqueeArr = ['张**（130****3260）在1', '李**（136****7163）在3', '赵**（139****1955）在5', '刘**（180****6999）在2', '张**（151****2588）在4', '王**（133****4096）在6'];
  let marquee1 = 0;
  let marquee2 = 1;
  let marquee3 = 2;
  if ($new.length) {
    newList();
    setInterval(newList, 2000);
  }
  function newList () {
    if (marquee1 > marqueeArr.length - 1) marquee1 = 0;
    if (marquee2 > marqueeArr.length - 1) marquee1 = 0;
    if (marquee3 > marqueeArr.length - 1) marquee1 = 0;
    marquee2 = marquee1 + 1;
    marquee3 = marquee2 + 1;
    $new.html('<p>[最新购买]：' + marqueeArr[marquee1] + '分钟前订购了【' + pro.eq(Math.floor(Math.random() * pro.length + 1) - 1).text().replace(/（[\d.]+元）/, '').replace(/（[\d.]+元～[\d.]+元）/, '').replace(/└—/, '') + '】</p>' + '<p>[最新购买]：' + marqueeArr[marquee2] + '分钟前订购了【' + pro.eq(Math.floor(Math.random() * pro.length + 1) - 1).text().replace(/（[\d.]+元）/, '').replace(/（[\d.]+元～[\d.]+元）/, '').replace(/└—/, '') + '】</p>' + '<p>[最新购买]：' + marqueeArr[marquee3] + '分钟前订购了【' + pro.eq(Math.floor(Math.random() * pro.length + 1) - 1).text().replace(/（[\d.]+元）/, '').replace(/（[\d.]+元～[\d.]+元）/, '').replace(/└—/, '') + '】</p>');
    marquee1++;
    marquee2++;
    marquee3++;
  }

  let $price = $('input[name=price]');
  let $count = $('input[name=count]');
  let $attr = $('.attr');
  let $total = $('.total');
  // total();
  $count.on({
    keyup: total,
    blur: total
  });
  function total () {
    $total.html($price.val() ? '<span class="price">' + ($price.val() * $count.val()) + '元</span>' : '请选全产品属性后查看总价');
  }

  attr();
  $('.pro label input').on({click: attr});
  layui.use(['form'], function () {
    layui.form.on('select(pro)', function () {
      attr();
    });
  });
  function attr () {
    let html = '';
    let $pro = $proSelectOption.length ? $('.pro select option:selected') : $('.pro label input:checked');
    let attr = $pro.attr('attr');
    let ownPrice = $pro.attr('own_price');
    if (attr) {
      $.each(attr.split('|'), function (index, value) {
        let temp = value.split(':');
        let attrV = temp[1].split(',');
        html += '<dd><span class="left">' + temp[0] + '：</span><span class="right">';
        $.each(attrV, function (index2, value2) {
          html += '<label><input type="radio" name="attr_v[' + index + ']" value="' + index2 + '" datatype="*" nullmsg="请选择' + temp[0].replace(new RegExp(/ /g), '').replace(new RegExp(/　/g), '') + '！" val="' + temp[0] + '：' + value2 + '" class="attr_v' + (ownPrice ? ' own_price' : '') + '">' + value2 + '</label>';
        });
        html += '</span></dd>';
      });
      $attr.html(html);
    } else {
      $attr.html('');
    }
    if (ownPrice) {
      $price.val('');
      $total.html('请选全产品属性后查看总价');
    } else {
      $price.val($pro.attr('price'));
      $total.html('<span class="price">' + ($pro.attr('price') * $count.val()) + '元</span>');
    }
    $('input[name=attr]').val('');
  }

  $attr.on('click', '.attr_v', function () {
    let attr = '';
    $.each($('.attr_v:checked'), function () {
      attr += $(this).attr('val') + '\r\n';
    });
    $('input[name=attr]').val(attr.substring(0, attr.length - 2));
  }).on('click', '.own_price', function () {
    let price = 0;
    let $ownPriceChecked = $('.own_price:checked');
    if ($ownPriceChecked.length === $('.attr dd').length) {
      let $pro = $('.pro select option').length ? $('.pro select option:selected') : $('.pro label input:checked');
      let length = $pro.attr('attr').split('|').length;
      let json = JSON.parse($pro.attr('own_price').replace(new RegExp(/'/g), '"'));
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
    }
    $total.html($price.val() ? '<span class="price">' + (price * $count.val()) + '元</span>' : '请选全产品属性后查看总价');
  });

  let $tel = $('form.form input[name=tel]');
  $('.sms_verify .button').on('click', function () {
    if (!/^\d{11}$/.test($tel.val().toString())) {
      tip('请先填写正确的手机号码！');
      return;
    }
    let $this = $(this);
    $.ajax({
      type: 'POST',
      url: CONFIG['SMS_CAPTCHA'],
      data: {
        tel: $tel.val()
      },
      success: function (data) {
        let json = JSON.parse(data);
        if (json['state'] === 0) {
          tip(json['content']);
        } else {
          let second = 60;
          $this.attr('disabled', 'true').val('重新发送(' + second + 'S)');
          let timer = setInterval(function () {
            if (second === 0) {
              clearInterval(timer);
              $this.removeAttr('disabled').val('重新发送验证码');
              second = 60;
            } else {
              second--;
              $this.val('重新发送' + second + 'S)');
            }
          }, 1000);
        }
      }
    });
  });

  function tip (tip) {
    if (tip === '通过信息验证！') return;
    $('.tip').html(tip).show();
    setTimeout(function () {
      $('.tip').hide();
    }, 3000);
  }
});
