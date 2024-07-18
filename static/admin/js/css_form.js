$(function () {
  // 代码编辑器渲染
  let $code = $('textarea[name=code]');
  let editor = CodeMirror.fromTextArea($code.get(0), {
    lineNumbers: true,
    mode: 'text/css',
    matchBrackets: true,
    lineWrapping: true
  });
  editor.on('blur', function () {
    $code.val(editor.getValue());
  });
});
