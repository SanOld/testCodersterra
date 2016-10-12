spi.controller('UserController', function ($scope, $rootScope, network, GridService, Utils) {
  if (!$rootScope._m) {
    $rootScope._m = 'user';
  }
  $scope.filter = {is_active: 1};

  if ($scope.page) {
    $scope.filter['type'] = $scope.page; // t - performer, d - district, s - school
    $scope.filter['relation_id'] = $scope.relationId;
  }

  $scope.statuses = [
    {id: 1, name: 'Aktiv'},
    {id: 0, name: 'Nicht aktiv'}
  ];

  network.get('user_type', angular.merge({filter: 1}, $scope.filter['type'] ? {type: $scope.filter['type']} : {}), function (result, response) {
    if (result) {
      $scope.userTypes = response.result;

      var rowTA = null;
      for (var i = 0; i < $scope.userTypes.length; i++) {
        if ($scope.userTypes[i].type == 't') {
          rowTA = $scope.userTypes[i];
          break;
        }
      }
      if(rowTA) {
        $scope.userTypes.splice(i+1, 0, {id: rowTA.id+'_1', name: rowTA.name + ' (F)', 'type': rowTA.type});
        rowTA.id += '_0'
      }
    }
  });

  var grid = GridService();
  $scope.tableParams = grid('user', $scope.filter, {sorting: {name: 'asc'}});

  $scope.resetFilter = function () {
    $scope.filter = grid.resetFilter();
  };

  $scope.updateGrid = function () {
    var rowType = Utils.getRowById($scope.userTypes, $scope.filter.type_id);
    if(rowType.type == 't') {
      $scope.filter.is_finansist = rowType.id.split('_')[1];
    } else {
      delete $scope.filter.is_finansist;
    }
    grid.reload();
  };

  $scope.openEdit = function (row, modeView) {
    grid.openEditor({
      data: row,
      hint: $scope._hint,
      modeView: !!modeView,
      controller: 'UserEditController',
      template: 'editUserTemplate.html'
    });
  };

  $scope.canCreate = function () {
    return $rootScope.canEdit();
  };

  $scope.canEdit = function(row) {
    return ($rootScope.canEdit() || row.id == network.user.id) && !(network.userIsPA && row.type_id == 1);
  }

});


