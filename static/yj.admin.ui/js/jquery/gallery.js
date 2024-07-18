/*
@Name：《昱杰后台UI框架》
@Author：风形火影
@Site：https://www.yjrj.cn
*/
function Gallery (args = {}) {
  args.beforeCallback = args.beforeCallback ? args.beforeCallback : function () {};
  args.insertCallback = args.insertCallback ? args.insertCallback : function () {};
  args.dragCallback = args.dragCallback ? args.dragCallback : function () {};
  args.deleteCallback = args.deleteCallback ? args.deleteCallback : function () {};
  let $input = $(args.input);
  let $galleryInsert = $(args.pick.id).parent().parent().parent().find('.gallery-insert');

  // 上传
  args.uploadConfig.pick = args.pick;
  let webUploader = WebUploader.create(args.uploadConfig);
  webUploader.on('uploadStart', function () {
    if (args.beforeCallback($input.length ? $input.val().split(',') : []) === false) {
      webUploader.reset();
    }
  });
  webUploader.on('uploadSuccess', function (file, response) {
    let json = JSON.parse(response._raw);
    if (json['state'] === 1) {
      let fileName = json['content'];
      if (args.text && $input.length) {
        $input.val($input.val() + '[img=' + fileName + ']');
      } else {
        if (args.pick.multiple || args.pick.multiple === undefined) {
          $input.val($.trim($input.val()) + ($.trim($input.val()) === '' ? '' : ',') + fileName);
          $galleryInsert.find('ul').append('<li><img src="' + args.uploadConfig.uploadDir + fileName + '" _src="' + fileName + '" alt="' + fileName + '"><a href="javascript:" class="delete">删除</a></li>');
        } else {
          $input.val(fileName);
          $galleryInsert.find('ul').html('<li><img src="' + args.uploadConfig.uploadDir + fileName + '" _src="' + fileName + '" alt="' + fileName + '"><a href="javascript:" class="delete">删除</a></li>');
        }
      }
      args.insertCallback($input.length ? $input.val().split(',') : fileName);
    } else {
      showTip(json['content'], 0);
    }
  });
  webUploader.on('error', uploadValidate);
  $galleryInsert.find('.buttons').on({
    mouseover: function () {
      $(this).find('.local,.choose').show();
      webUploader.refresh();
    },
    mouseleave: function () {
      $(this).find('.local,.choose').hide();
    }
  });

  // 删除
  $galleryInsert.find('ul').on('click', '.delete', function () {
    let $this = $(this);
    let pictures = $input.val().split(',');
    pictures.splice($.inArray($this.parent().find('img').attr('_src'), pictures), 1);
    $input.val(pictures.join(','));
    $this.parent().remove();
    args.deleteCallback(pictures);
  });

  // 移动
  if ($galleryInsert.find('.draggable').length) {
    $galleryInsert.find('.draggable').sortable({
      cursor: 'move',
      items: 'li',
      opacity: 0.6,
      update: function () {
        let pictures = [];
        $.each($galleryInsert.find('.draggable li'), function () {
          pictures.push($(this).find('img').attr('_src'));
        });
        $input.val(pictures.join(','));
        args.dragCallback(pictures);
      }
    });
  }
}
Gallery.prototype.dialog = function (args = {}) {
  args.beforeCallback = args.beforeCallback ? args.beforeCallback : function () {};
  args.insertCallback = args.insertCallback ? args.insertCallback : function () {};
  let that = this;
  $.ajax({
    type: 'POST',
    url: args.pictureDir
  }).then(function (data) {
    if (data === '') {
      alert('图片库中暂无图片，请通过本地上传方式插入图片！');
      return;
    }
    let $input = $(args.input);
    let html = '<form class="gallery layui-form"><input type="hidden" name="pictures" value="' + (args.text ? '' : $input.val()) + '">';
    let json = JSON.parse(data);
    if (json) {
      html += '<div class="dir">图片目录：<select lay-filter="picture_dir" lay-search>';
      $.each(json, function (index, value) {
        html += '<option value="' + value['name'] + '" total="' + value['total'] + '">' + value['name'] + '</option>';
      });
      html += '</select>' + (args.multiple ? '（图片将按照您选择的顺序依次添加到对应的表单中，切换图片目录或翻页不影响已选择的图片）' : '') + '</div>';
      args.name = json[0]['name'];
      args.total = json[0]['total'];
    }
    html += '<ul class="items"></ul><p style="clear:both;"></p><div class="pagination"></div></form>';
    layer.confirm(
      html,
      {
        title: '图片库',
        area: '800px',
        resizable: false
      },
      function (index) {
        let pictures = $('.gallery input[name=pictures]').val();
        let picturesArr = pictures.split(',');
        if (args.beforeCallback(picturesArr) === false) return false;
        if (args.text) {
          $.each(picturesArr, function (index, value) {
            $input.val($input.val() + '[img=' + value + ']');
          });
        } else {
          let $galleryInsert = $(args.id).parent().parent().parent().find('.gallery-insert');
          if (args.multiple) {
            let html = '';
            if (pictures) {
              $.each(picturesArr, function (index, value) {
                html += '<li><img src="' + args.uploadDir + value + '" _src="' + value + '" alt="' + value + '"><a href="javascript:" class="delete">删除</a></li>';
              });
            }
            $input.val(picturesArr.join(','));
            $galleryInsert.find('ul').html(html);
          } else {
            $input.val(picturesArr[0]);
            if (pictures) $galleryInsert.find('ul').html('<li><img src="' + args.uploadDir + picturesArr[0] + '" _src="' + picturesArr[0] + '" alt="' + picturesArr[0] + '"><a href="javascript:" class="delete">删除</a></li>');
          }
        }
        if (pictures) args.insertCallback(picturesArr);
        layer.close(index);
      }
    );
    that.paging(1, args);
    if (args.total > 0) that.createPage(args);
    layui.use(['form'], function () {
      layui.form.render();
      layui.form.on('select(picture_dir)', function (data) {
        args.name = data.value;
        args.total = $(data.elem).find('option:selected').attr('total');
        that.paging(1, args);
        if (args.total > 0) that.createPage(args);
      });
    });
  });

  $('body').off('click').on('click', '.gallery .items li', function () {
    let $this = $(this);
    let $gallery = $('.gallery');
    let $pictures = $gallery.find('input[name=pictures]');
    let pictures = $pictures.val() ? $pictures.val().split(',') : [];
    if (args.multiple) {
      if ($this.hasClass('active')) {
        $this.removeClass('active');
        pictures.splice($.inArray($this.find('img').attr('_src'), pictures), 1);
      } else {
        $this.addClass('active');
        pictures.push($this.find('img').attr('_src'));
      }
      $pictures.val(pictures.join(','));
    } else {
      $gallery.find('.items li').removeClass('active');
      $this.addClass('active');
      $pictures.val($this.find('img').attr('_src') + ',');
    }
  });
};
Gallery.prototype.createPage = function (args = {}) {
  let that = this;
  let $gallery = $('.gallery');
  $gallery.find('.pagination').html('<ul></ul>');
  $gallery.find('.pagination ul').createPage({
    active: 1,
    total: args.total,
    pageSize: 10,
    pageCount: Math.ceil(args.total / 10),
    paging: function (page) {
      that.paging(page, args);
    }
  });
};
Gallery.prototype.paging = function (page, args = {}) {
  $.ajax({
    type: 'GET',
    url: args.pictureList,
    data: {
      page: page,
      id: args.name
    }
  }).then(function (data) {
    let $gallery = $('.gallery');
    let html = '';
    if (data) {
      let pictures = $gallery.find('input[name=pictures]').val().split(',');
      $.each(JSON.parse(data), function (index, value) {
        html += '<li' + ($.inArray(args.name + '/' + value.name, pictures) !== -1 ? ' class="active"' : '') + '><dl><dd><img src="' + args.uploadDir + args.name + '/' + value.name + '" _src="' + args.name + '/' + value.name + '" alt="' + value.name + '"></dd><dt>' + value.name + '</dt></dl></li>';
      });
      $gallery.find('.pagination').show();
    } else {
      html = '<li class="nothing">此目录暂无图片</li>';
      $gallery.find('.pagination').hide();
    }
    $gallery.find('.items').html(html);
  });
};
