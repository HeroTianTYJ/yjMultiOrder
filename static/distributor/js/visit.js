$(function () {
  let moduleName = '访问统计';

  // 列表
  list(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
});

function listItem (item) {
  return '<tr class="item' + item['id'] + '"><td>' + item['ip'] + '</td><td><a href="' + item['url'] + '" target="_blank" title="' + item['url'] + '">' + item['truncate_url'] + '</a></td><td>' + item['count'] + '</td><td>' + item['date1'] + '</td><td>' + item['date2'] + '</td></tr>';
}
