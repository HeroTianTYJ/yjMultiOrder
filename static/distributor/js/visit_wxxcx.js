$(function () {
  let moduleName = '访问统计（微信小程序）';

  // 列表
  list(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 微信小程序
    layui.form.on('select(wxxcx_id)', function (data) {
      window.location.href = searchUrl('wxxcx_id=' + data.value);
    });
    // 页面类型
    layui.form.on('select(type)', function (data) {
      window.location.href = searchUrl('type=' + data.value);
    });
    // 列表页
    layui.form.on('select(lists_id)', function (data) {
      window.location.href = searchUrl('lists_id=' + data.value);
    });
    // 商品页
    layui.form.on('select(item_id)', function (data) {
      window.location.href = searchUrl('item_id=' + data.value);
    });
    // 品牌分类
    layui.form.on('select(category_id)', function (data) {
      window.location.href = searchUrl('category_id=' + data.value);
    });
    // 品牌
    layui.form.on('select(brand_id)', function (data) {
      window.location.href = searchUrl('brand_id=' + data.value);
    });
    // 访问场景
    layui.form.on('select(wxxcx_scene_id)', function (data) {
      window.location.href = searchUrl('wxxcx_scene_id=' + data.value);
    });
  });
});

function listItem (item) {
  return '<tr class="item' + item['id'] + '"><td>' + item['ip'] + '</td><td>' + item['wxxcx'] + '</td><td>' + item['type'] + '</td><td>' + item['page'] + '</td><td>' + item['scene'] + '</td><td>' + item['count'] + '</td><td>' + item['date1'] + '</td><td>' + item['date2'] + '</td></tr>';
}
