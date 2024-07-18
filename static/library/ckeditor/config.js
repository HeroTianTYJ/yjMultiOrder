CKEDITOR.editorConfig = function (config) {
  config.language = 'zh-cn';
  config.skin = 'kama';
  config.allowedContent = true;
  config.entities = false;
  config.toolbar = [
    {name: 'document', items: ['Source', '-', 'NewPage', 'DocProps', 'Preview']},
    {name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo']},
    {name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt']},
    {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']},
    {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']},
    {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
    {name: 'insert', items: ['Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']},
    {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
    {name: 'colors', items: ['TextColor', 'BGColor']},
    {name: 'tools', items: ['Maximize', 'ShowBlocks', '-', 'About']}
  ];
  config.removePlugins = 'easyimage,cloudservices,exportpdf';
  config.baseFloatZIndex = 198910150;
  config.resize_enabled = false;
};
CKEDITOR.on('instanceReady', function (event) {
  let element = CKEDITOR.tools.extend({}, CKEDITOR.dtd.$block, CKEDITOR.dtd.$listItem, CKEDITOR.dtd.$tableContent);
  element.br = 1;
  for (let e in element) {
    event.editor.dataProcessor.writer.setRules(e, {
      indent: 0,
      breakBeforeOpen: 0,
      breakAfterOpen: 0,
      breakBeforeClose: 0,
      breakAfterClose: 0
    });
  }
});
