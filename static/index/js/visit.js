visit();

function visit () {
  let managerId = 0;
  if (typeof (CONFIG) !== 'undefined' && CONFIG['MANAGER_ID']) managerId = CONFIG['MANAGER_ID'];
  if (getPar('uid')) managerId = getPar('uid');
  AjaxVisit({
    method: 'post',
    url: '//localhost/2/yjorder5/index.php/common/visi1t.html',
    data: {
      url: location,
      manager_id: managerId
    },
    success: function (data) {

    },
    async: true
  });
}

function getPar (par) {
  let localUrl = document.location.href.toLowerCase();
  let get = localUrl.indexOf(par + '=');
  if (get === -1) return false;
  let getPar = localUrl.slice(par.length + get + 1);
  let nextPar = getPar.indexOf('&');
  if (nextPar !== -1) getPar = getPar.slice(0, nextPar);
  return getPar;
}

function AjaxVisit (obj) {
  let xhr = (function () {
    if (typeof XMLHttpRequest !== 'undefined') {
      return new XMLHttpRequest();
    } else if (typeof window.ActiveXObject !== 'undefined') {
      let version = [
        'MSXML2.XMLHttp6.0',
        'MSXML2.XMLHttp3.0',
        'MSXML2.XMLHttp'
      ];
      for (let i = 0; i < version.length; i++) {
        try {
          return new ActiveXObject(version[i]);
        } catch (e) {
        }
      }
    } else {
      throw new Error('您的系统或浏览器不支持XHR对象！');
    }
  })();
  obj.url += (obj.url.indexOf('?') === -1 ? '?' : '&') + 'rand=' + Math.random();
  obj.data = (function (data) {
    let arr = [];
    for (let i in data) {
      arr.push(encodeURIComponent(i) + '=' + encodeURIComponent(data[i]));
    }
    return arr.join('&');
  })(obj.data);
  if (obj.method === 'get') obj.url += obj.url.indexOf('?') === -1 ? '?' + obj.data : '&' + obj.data;
  if (obj.async === true) {
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) callback();
    };
  }
  xhr.open(obj.method, obj.url, obj.async);
  if (obj.method === 'post') {
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(obj.data);
  } else {
    xhr.send(null);
  }
  if (obj.async === false) callback();

  function callback () {
    if (xhr.status === 200) {
      obj.success(xhr.responseText);
    } else {
      alert('获取数据错误，错误代号：' + xhr.status + '，错误信息：' + xhr.statusText);
    }
  }
}
