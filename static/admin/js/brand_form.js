$(function () {
  // 图片插入配置
  let uploadConfig = {
    uploadDir: CONFIG['UPLOAD_DIR'],
    auto: true,
    server: CONFIG['UPLOAD_SERVER'],
    fileSingleSizeLimit: 10240000,
    threads: 1,
    accept: {
      extensions: 'bmp,gif,jpg,jpeg,png',
      mimeTypes: '.bmp,.gif,.jpg,.jpeg,.png'
    },
    compress: false,
    resize: false,
    duplicate: true
  };
  let galleryConfig = {
    pictureDir: CONFIG['PICTURE_DIR'],
    pictureList: CONFIG['PICTURE_LIST'],
    uploadDir: CONFIG['UPLOAD_DIR']
  };

  // 单个插入logo
  let pickerId1 = '.logo_picker';
  if ($(pickerId1).length) {
    let input = 'input[name=logo]';
    let gallery = new Gallery({
      pick: {
        id: pickerId1,
        label: '本地上传',
        multiple: false
      },
      uploadConfig: uploadConfig,
      input: input
    });
    $('.logo_choose').on('click', function () {
      galleryConfig.multiple = false;
      galleryConfig.input = input;
      galleryConfig.id = pickerId1;
      gallery.dialog(galleryConfig);
    });
  }

  // 批量插入logo
  let pickerId2 = '.logo_picker2';
  if ($(pickerId2).length) {
    let input = 'input[name=logo]';
    let gallery = new Gallery({
      pick: {
        id: pickerId2,
        label: '本地上传',
        multiple: true
      },
      uploadConfig: uploadConfig,
      input: input
    });
    $('.logo_choose').on('click', function () {
      galleryConfig.multiple = true;
      galleryConfig.input = input;
      galleryConfig.id = pickerId2;
      gallery.dialog(galleryConfig);
    });
  }

  // 颜色选择器
  $('input[name=color]').colorPicker();
});
