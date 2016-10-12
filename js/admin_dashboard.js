spi.controller('AdminDashboardController', function ($scope, $rootScope, network, GridService) {
  $rootScope._m = 'visit';
  $scope.filter = {};

  var grid = GridService();
  $scope.tableParams = grid('visit', $scope.filter, {sorting: {name: 'asc'}});

  $scope.updateGrid = function () {
    grid.reload();
  };



  $scope.resetFilter = function () {
    $scope.filter = grid.resetFilter();
  };




  $scope.getDate = function (date) {
    var result = '';
    if(date){
      result = new Date(date);
    }
    return result;
  }
});

