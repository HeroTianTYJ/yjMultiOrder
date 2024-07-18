$(function () {
  let moduleName = '访问统计（微信小程序）';

  // 列表
  list(moduleName);

  // 导出并清空
  $('.tool .output').on('click', function () {
    confirmLayer(
      CONFIG['OUTPUT'],
      {},
      '<h3><span>？</span>确定要将访问统计导出到文件并清空当前访问数据吗？</h3><p>导出成功后，可到<a href="' + CONFIG['FILE_CONTROLLER'] + '">文件管理</a>模块中进行下载。</p>',
      function (json, layerIndex) {
        if (json['state'] === 1) {
          layer.close(layerIndex);
          setTimeout(function () {
            window.location.reload(true);
          }, 3000);
        }
      }
    );
  });

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 分销商
    layui.form.on('select(manager_id)', function (data) {
      window.location.href = searchUrl('manager_id=' + data.value);
    });
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
  return '<tr class="item' + item['id'] + '"><td>' + item['ip'] + '</td><td>' + item['manager'] + '</td><td>' + item['wxxcx'] + '</td><td>' + item['type'] + '</td><td>' + item['page'] + '</td><td>' + item['scene'] + '</td><td>' + item['count'] + '</td><td>' + item['date1'] + '</td><td>' + item['date2'] + '</td></tr>';
}
