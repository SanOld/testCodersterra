spi.controller('main', function ($scope, $rootScope, $location, network, GridService, localStorageService, $timeout, HintService, RequestService) {
  $scope._r = localStorageService.get('rights');
  $scope.request_code = '';
  $rootScope.emailFormat = /^[a-zA-Z]+[a-z0-9._\-]+@[a-z0-9\-]+\.[a-z.]{2,5}$/;
  
  RequestService.setRequestCode = function(code){
    $scope.request_code = code;
  };
  
  $scope.canByType = function (types) {
    return types.indexOf($scope.user.type) != -1;
  };

  $scope.canShow = function (model) {
    model = model || $rootScope._m;
    return model && $scope._r[model] && $scope._r[model].show;
  };

  $rootScope.canView = function (model) {
    model = model || $rootScope._m;
    return model && $scope._r[model] && $scope._r[model].view;
  };

  $rootScope.canEdit = function (model) {
    model = model || $rootScope._m;
    return model && $scope._r[model] && $scope._r[model].edit;
  };

  $timeout(function() {
    if($location.path() !== '/404') {
      if($rootScope._m && $rootScope._m != 'dashboard' && $scope._r[$rootScope._m] && !$scope._r[$rootScope._m].show) {
        window.location = '/dashboard';
      }
    }
  });

  $scope.isLogin = network.isLogined();
  if ($scope.isLogin) {
    $scope.user = network.user;
  } else {
    //window.location = '/'
  }

  $scope.logout = function () {
    network.logout();    
  };
  network.onLogout = function () {
    window.location = '/'
  };
  
  $scope.login = function () {
    window.location = '/login'    
  };

  $scope.openUserProfile = function () {
    network.get('user', {auth_token: network.token}, function(result, response) {
      if(result) {
        GridService().openEditor({
          data: response.result[0],
          controller: 'UserEditController',
          template: 'editUserTemplate.html'
        }, function () {
          $scope.user = network.user;
        });
      }
    });

  };

});

spi.controller('UserEditController', function ($scope, $rootScope, modeView, $uibModalInstance, data, network, localStorageService, hint, HintService, Utils) {
  $scope.model = 'user';
  $scope.isInsert = true;
  $scope.isAdmin = network.userIsADMIN;
  $scope.userIsPA = network.userIsPA;
  $scope.modeView = modeView;
  
  
  $scope.user = {
    is_active: 1,
    is_finansist: 0,
    is_virtual: 0
  };
  $scope.loaded_is_virtual = 1;
  
  
  if (!hint) {
    HintService($scope.model, function (result) {
      $scope._hint = result;
    });
  } else {
    $scope._hint = hint;
  }

  if (data.id) {
    $scope.isInsert = false;
    $scope.userId = data.id;
    $scope.type_name = data.type_name;
    $scope.relation_name = data.relation_name;
    $scope.user = {
      is_active: +data.is_active,
      is_finansist: +data.is_finansist,
      sex: +data.sex,
      title: data.title,
      function: data.function,
      first_name: data.first_name,
      last_name: data.last_name,
      login: data.login,
      email: data.email,
      phone: data.phone,
      type_id: data.type_id,
      is_virtual: data.is_virtual*1
    };
    $scope.loaded_is_virtual = data.is_virtual*1;
    $scope.isCurrentUser = network.user.id == data.id;
    $scope.isPerformer = data.type == 't';
  } else {
    network.get('user_type', {filter: 1, user_create: 1}, function (result, response) {
      if (result) {
        $scope.userTypes = response.result;
      }
    });
  }

  $scope.reloadRelation = function () {
    $scope.relations = [];
    var type = Utils.getRowById($scope.userTypes, $scope.user.type_id);
    
    var relation_code = false
    switch(type.type) {
      case 't': relation_code = 'performer';
        break;
      case 's':  relation_code = 'school';
        break;
      case 'd':  relation_code = 'district';
        break;
    }
    $scope.isRelation = relation_code;
    $scope.isPerformer = type && type.type == 't';
    if ($scope.isRelation) {
      $scope.isRelation = true;
      network.get(relation_code, {filter: 1}, function (result, response) {
        if (result) {
          $scope.relations = response.result;
        }
      });
    }
  };

  $scope.fieldError = function (field) {
    return $scope.form[field] && $scope.form[field] && (($scope.submited || $scope.form[field].$touched) && $scope.form[field].$invalid) || ($scope.error && $scope.error[field] != undefined && $scope.form[field].$pristine);
  };

  $scope.submitForm = function (formData) {
    $scope.error = false;
    $scope.submited = true;
    $scope.form.$setPristine();
    if ($scope.form.$valid) {
      var callback = function (result, response) {
        if (result) {
          if ($scope.isCurrentUser) {
            network.updateUserField('login', formData.login);
          }
          $uibModalInstance.close();
        } else {
          $scope.error = getError(response.system_code);
        }
        $scope.submited = false;
      };
      if ($scope.isInsert) {
        network.post($scope.model, formData, callback);
      } else {
        network.put($scope.model + '/' + data.id, formData, callback);
      }
    }
  };

  $scope.remove = function (id) {
    Utils.doConfirm(function() {
      network.delete('user/' + id, function (result) {
        if (result) {
          Utils.deleteSuccess();
          $uibModalInstance.close();
        }
      });
    });
  };

  $scope.$on('modal.closing', function(event, reason, closed) {
    Utils.modalClosing($scope.form, $uibModalInstance, event, reason);
  });

  $scope.cancel = function () {
    Utils.modalClosing($scope.form, $uibModalInstance);
  };

  function getError(code) {
    var result = false;
    switch (code) {
      case 'ERR_DUPLICATED':
        result = {login: {dublicate: true}};
        break;
      case 'ERR_DUPLICATED_EMAIL':
        result = {email: {dublicate: true}};
        break;
      case 'ERR_CURRENT_PASSWORD':
        result = {old_password: {error: true}};
        break;
    }
    return result;
  }

  $scope.canDelete = function() {
    return $rootScope.canEdit() && !network.userIsPA;
  };

  $scope.canEdit = function() {
    return $scope.isCurrentUser || ($rootScope.canEdit() && !(network.userIsPA && data.type_id == 1));
  };


});