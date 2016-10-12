spi.service('network', function ($http, configs, localStorageService, Notification, $cookies) {
  var $network = this;
  $network.servisePath = configs.getServisePath();
  $network.token = localStorageService.get('token');
  $network.authProgress = false;
  $network.loginKey = localStorageService.get('loginKey');
  $network.user = localStorageService.get('user');

  if($network.user && $network.user.type_id) {
    $network.userIsADMIN    = $network.user.type_id == 1;
    $network.userIsUSER      = $network.user.type_id == 2;
  }

  $network.onLogin = function () {
  };
  $network.onLogout = function () {      
  };
  $network.createLoginKey = function(login, password) {
    return btoa(unescape(encodeURIComponent(login + ':' + password)))
  };
  $network.updateUserField = function(field, value) {
    if($network.user[field] != undefined) {
      $network.user[field] = value;
    }
  };

  $network.reconnect = function (callback) {

    $http({
      'method': 'JSON'
      , 'dataType': 'json'
      , 'params': {'loginKey': $network.loginKey}
      , 'url': configs.getAuthPath()
    })
      .success(function (result) {
        $network.token = result.token;
        localStorageService.set('token', result.token);
        $network.user = result.user;
        localStorageService.set('user', result.user);
        localStorageService.set('rights', result.rights);
        $cookies.put('isLogined', 1);
        callback(true);
      })
      .error(function (data, status, headers, config) {
        $network.logout();
        window.location = '/';
      });
  };
  $network.logout = function () {
    $network.token = false;
    localStorageService.set('token', false);
    $network.user = {};
    localStorageService.set('user', false);
    localStorageService.set('rights', false);
    localStorageService.set('loginKey', false);
    $cookies.remove('isLogined'); 
    $network.onLogout();
    $http({
        'method': 'JSON'
        , 'dataType': 'json'
        , 'url': configs.getOutPath()
    }); 
  };
  $network.isLogined = function () {
    var token = localStorageService.get('token');
    var time = localStorageService.get('tokenTime') - $.now();
    return (token && token != 'false' && time >= 0);
  };
  $cookies.put('isLogined', $network.isLogined()?1:0);
  $network.connect = function (login, password, callback) {
    callback = callback || function () {
      };
    if (!$network.authProgress) {
      $network.authProgress = true;
      //$network.loginKey = btoa(login + ':' + password);
      $network.loginKey = $network.createLoginKey(login, password);
      localStorageService.set('loginKey', $network.loginKey);
      $http({
        'method': 'JSON'
        , 'dataType': 'json'
        , 'params': {'loginKey': $network.loginKey}
        , 'url': configs.getAuthPath()
      })
        .success(function (result) {
          $network.authProgress = false;
          $network.token = result.token;
          $network.user = result.user;
          localStorageService.set('token', result.token);
          localStorageService.set('tokenTime', $.now() + 12 * 3600 * 1000);
          localStorageService.set('user', result.user);
          localStorageService.set('rights', result.rights);
          $cookies.put('isLogined', 1);
          callback(true, result);
          $network.onLogin();
        })
        .error(function (data, status, headers, config) {
          $network.authProgress = false;
          callback(false, data);
        });
    }
  };

  $network.get = function (table, params, callback) {

    $network.servisePath = configs.getServisePath();
    var path = $network.servisePath + table;
    var method = 'GET';
    var headers = {};

    $http.defaults.headers.common.Authorization = 'token ' + $network.token;
    $http.defaults.headers.get = {};
    $http.defaults.headers.get['Authorization'] = 'token ' + $network.token;
    $http.defaults.headers.common['Authorization'] = 'token ' + $network.token;
    headers = {'Authorization': $network.token};
    params = params || {};
    params.nc = new Date().getTime();
    $http({
      'method': method
      , 'params': params
      , 'headers': headers
      , 'url': path
    })
      .success(function (result) {
        callback(true, result);
      })
      .error(function (data, status, headers, config, statusText) {
        if (status == 401) {
          $network.reconnect(function (result) {
            if (result) {
              $network.get(table, params, callback);
            } else {
              // go to login
              callback(false, data);
            }
          })
        } else {
          if (data.system_code == 'ERR_INVALID_TOKEN') {
            $network.logout();
            window.location = '/'
          } else {
            Notification.error({title: 'Сообщение', message: data.message});
            callback(false, data);
          }
        }
      });

  };

  $network.post = function (table, params, callback, showAlert) {
    callback = callback || function () {
      };
    showAlert = showAlert == undefined || showAlert;
    $network.servisePath = configs.getServisePath();
    var path = $network.servisePath + table;

    var headers = {};

    headers = {
      'Authorization': $network.token
      , 'Content-Type': 'application/x-www-form-urlencoded'
      , "Accept": "application/json; charset=utf-8"
    };
    $http({
      'method': 'POST'
      , 'headers': headers
      , 'dataType': 'json'
      , 'data': $.param(params)
      , 'url': path
    })
      .success(function (result) {
        callback(true, result);
        //if (showAlert && !result.silent)
        //  Notification.success({title: result.message});
      })
      .error(function (data, status, headers, config, statusText) {
        if (status == 401) {
          $network.reconnect(function (result) {
            if (result) {
              $network.post(table, params, callback)
            } else {
              callback(false, data)
            }
          })
        } else {
          callback(false, data);
          if (showAlert && !data.silent)
            Notification.error({title: 'Сообщение', message: data.message});
        }
      });

  };


  $network.put = function (table, params, callback, showAlert) {
    callback = callback || function () {
      };
    showAlert = showAlert == undefined || showAlert;
    $network.servisePath = configs.getServisePath();
    var path = $network.servisePath + table;

    var headers = {};

    headers = {
      'Authorization': $network.token
      , 'Content-Type': 'application/json; charset=utf-8'
      , "Accept": "application/json; charset=utf-8"
    };

    $http({
      'method': 'PUT'
//          , 'params': params
      , 'headers': headers
      , 'dataType': 'json'
      , 'data': $.param(params)
      , 'url': path
    })
      .success(function (result) {
        callback(true, result);
        if (showAlert && !result.silent)
          Notification.success({title: 'Сообщение', message: result.message});
      })
      .error(function (data, status, headers, config, statusText) {
        if (status == 401) {
          $network.reconnect(function (result) {
            if (result) {
              $network.put(table, params, callback)
            } else {
              callback(false, data)
            }
          })
        } else {
          callback(false, data);
          if (showAlert && !data.silent)
            Notification.error({title: 'Сообщение', message: data.message});
        }
      });

  };

  $network.patch = function (table, params, callback, showAlert) {
    callback = callback || function () {};
    showAlert = showAlert == undefined || showAlert;
    $network.servisePath = configs.getServisePath();
    var path = $network.servisePath + table;
    var headers = {
      'Authorization': $network.token
      , 'Content-Type': 'application/json; charset=utf-8'
      , "Accept": "application/json; charset=utf-8"
    };

    $http({
      'method': 'PATCH'
      , 'headers': headers
      , 'dataType': 'json'
      , 'data': $.param(params)
      , 'url': path
    })
      .success(function (result) {
        callback(true, result);
        if (showAlert && !result.silent)
          Notification.success({title: 'Сообщение', message: result.message});
      })
      .error(function (data, status, headers, config, statusText) {
        if (status == 401) {
          $network.reconnect(function (result) {
            if (result) {
              $network.patch(table, params, callback)
            } else {
              callback(false, data)
            }
          })
        } else {
          callback(false, data);
          if (showAlert && !data.silent)
            Notification.error({title: 'Сообщение', message: data.message});
        }
      });

  };

  $network.delete = function (table, callback, showAlert) {
    callback = callback || function () {
      };
    showAlert = showAlert == undefined || showAlert;
    $network.servisePath = configs.getServisePath();
    var path = $network.servisePath + table;

    var headers = {};

    headers = {
      'Authorization': $network.token
      , 'Content-Type': 'application/x-www-form-urlencoded'
    };
//    var params['Authorization'] = $network.token;
    $http({
      'method': 'DELETE'
      , 'headers': headers
      , 'dataType': 'json'
      , 'url': path
    })
      .success(function (result) {
        callback(true, result);
        //if (showAlert && !result.silent)
        //  Notification.success({title: result.message});
      })
      .error(function (data, status, headers, config, statusText) {
        if (status == 401) {
          $network.reconnect(function (result) {
            if (result) {
              $network.delete(table, callback)
            } else {
              callback(false, data)
            }
          })
        } else {
          callback(false, data);
          if (showAlert && !data.silent)
            Notification.error({title: 'Сообщение', message: data.message});
        }
      });

  };

});
