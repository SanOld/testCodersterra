spi.controller('AuditController', function ($scope, $rootScope, network, GridService) {
  $rootScope._m = 'audit';
  $scope.filter = {};
  $scope.customData = [];
  $scope.types = [{'code': 'INS', 'name': 'Hinzugefügt'},
                  {'code': 'UPD', 'name': 'Bearbeitet'},
                  {'code': 'DEL', 'name': 'Gelöscht'}];
  var grid = GridService();
  $scope.tableParams = grid('audit', $scope.filter, {group: "id", sorting: {event_date: 'desc'}});

  $scope.updateGrid = function () {
    grid.reload();
  };
  
  network.get('page', {'order':'name'}, function (result, response) {
      if(result) {
        var key = -1;
        $.each(response.result, function(k,val){
          if(val.name == 'Audit') {
            key = k;
            return false;
          }
        });
        
        if(result != -1) {
          response.result.splice(key,1)
        }
        $scope.tables = response.result;
      }
  });

//  $scope.updateGrid = function () {
////    $scope.filter['limit'] = params.count();
////    $scope.filter['page'] = params.page();
//    $scope.filter['order'] = 'event_date';
//    var params = angular.copy($scope.filter);
//    try {
//      params['event_date'] =  params['date'].ymd();
//    } catch(e){}
//    delete params['date'];
//    network.get('audit', params, function (result, response) {
//      if (result) {
//        $scope.customData = response;
//      }
//    });
//    network.get('AuditTables', {}, function (result, response) {
//      if (result) {
//        $scope.tables = response.result;
//      }
//    });
//  };
  

  $scope.resetFilter = function () {
    $scope.filter = grid.resetFilter();
  };
  
//  $scope.updateGrid();
  
});
