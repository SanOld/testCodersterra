spi.controller('UserRolesController', function ($scope, $rootScope, network, GridService) {
  $rootScope._m = 'user_type';
  var grid = GridService();
  $scope.tableParams = grid('user_type', $scope.filter, {sorting: {name: 'asc'}});

  $scope.updateGrid = function () {
    grid.reload();
  };

  $scope.openEdit = function (row, modeView) {
    grid.openEditor({
      data: row,
      hint: $scope._hint,
      modeView: !!modeView,
      controller: 'EditUserRoleController'
    }, function () {
      getTypes();
    });
  };

  function getTypes() {
    network.get('user_type', {}, function (result, response) {
      if (result) {
        $scope.tableParams = grid(response.result, {}, {sorting: {name: 'asc'}, count: response.result.length});
      }
    });
  }

});


spi.controller('EditUserRoleController', function ($scope, $uibModalInstance, modeView, data, hint, network, GridService, Utils) {
  $scope.isInsert = !data.id;
  $scope._hint = hint;
  $scope.modeView = modeView;
  $scope.userIsSuperUser = network.userIsSuperUser;
  $scope.network = network;

  if (!$scope.isInsert) {
    $scope.userTypeId = data.id;
    $scope.default = +data.default;
    $scope.relation_name = data.relation_name;
    $scope.user_type = {name: data.name};
  } else {
    network.get('user_type', {'default': 1}, function (result, response) {
      if (result) {
        $scope.relations = response.result;
        $scope.user_type = {type: 'a'};
      }
    });
  }


  var grid = GridService();
  var params = {right: 1, type_id: data.id, all: 1, order: 'name'};
    
  if(network.userIsSuperUser) {
    params.system = 1
  }
  console.log(network);
  network.get('page', params, function (result, response) {
    if (result) {
      $scope.tableParams = grid(response.result, {}, {sorting: {page_name: 'asc'}, count: response.result.length});
      $scope.user_right = [];
      for (var k in response.result) {
        $scope.user_right.push({
          id: response.result[k].right_id,
          type_id: data.id,
          page_id: response.result[k].id,
          code: response.result[k].code,
          can_view: response.result[k].can_view,
          can_edit: response.result[k].can_edit,
          can_show: response.result[k].can_show,
          is_real_page: response.result[k].is_real_page,
          is_without_login: response.result[k].is_without_login
        });
      }
    }
  });

  $scope.fieldError = function (field) {
    return $scope.form[field] && ($scope.submited || $scope.form[field].$touched) && $scope.form[field].$invalid || ($scope.error && $scope.error[field] != undefined && $scope.form[field].$pristine);
  };

  $scope.submitForm = function () {
    $scope.errors = [];
    $scope.submited = true;
    $scope.form.$setPristine();
    if ($scope.form.$valid) {
      var callback = function (result, response) {
        if (result) {
          $uibModalInstance.close();
        } else {
          setError(response.system_code);
        }
        $scope.submited = false;
      };
      if (!$scope.default || network.userIsSuperUser) {
        $scope.user_type.rights = $scope.user_right;
      }
      if ($scope.isInsert) {
        network.post('user_type', $scope.user_type, callback);

      } else {
        network.put('user_type/' + data.id, $scope.user_type, callback);
      }
    }
  };

  $scope.remove = function (id) {
    Utils.doConfirm(function() {
      network.delete('user_type/' + id, function (result) {
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

  function setError(code) {
    var result = false;
    switch (code) {
      case 'ERR_DUPLICATED_NAME':
        result = {name: {dublicate: true}};
        break;
    }
    $scope.error = result;
  }
});