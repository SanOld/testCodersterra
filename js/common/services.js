spi.service('configs', function () {
  var $configs = this;

  $configs.getServisePath = function () {
    return ((location + '').match(/http\:\/\/([^\/]+)/)[0]) + '/api/';
  };
  $configs.getAuthPath = function () {
    return ((location + '').match(/http\:\/\/([^\/]+)/)[0]) + '/api/login';
  };
  $configs.getOutPath = function () {
    return ((location + '').match(/http\:\/\/([^\/]+)/)[0]) + '/api/logout';
  };
  $configs.getSitePath = function () {
    return ((location + '').match(/http\:\/\/([^\/]+)/)[0]) + '';
  };
  $configs.getDomain = function () {
    var domain = ((location + '').match(/http\:\/\/([^\/]+)/)[1]) + '';
    var path = domain.split('.');
    var start = path.length - 2;
    start = start < 0 ? 0 : start;
    account = path.splice(start, path.length).join('.');
    return account;
  };
  $configs.getAccount = function () {
    var domain = ((location + '').match(/http\:\/\/([^\/]+)/)[1]) + '';
    var path = domain.split('.');
    var account = '';
    if (path.length > 2) {
      account = path.splice(0, path.length - 2).join('.');
    }
    return account;
  }
});

spi.service("GridService", function (network, NgTableParams, $uibModal, Notification,$timeout) {
  return function () {
    var tableParams;
    var defaultFilter = {};
    var filter = {};
    var model = '';

    function getData(path, params, filter, callback) {
      model = path;
      filter['limit'] = params.count();
      filter['page'] = params.page();
      var sort = params.sorting();
      if (Object.keys(sort).length) {
        filter['order'] = Object.keys(sort)[0];
        filter['direction'] = sort[filter['order']];
      }
      network.get(model, angular.copy(filter), function (result, response) {
        if (result) {
          callback(response);
        }
      });
    }

    function filterEquals() {
      var trueFilter = {};
      var except = ['page', 'limit', 'order', 'direction'];
      for (var k in filter) {
        if (except.indexOf(k) === -1) {
          trueFilter[k] = filter[k];
        }
      }
      return angular.equals({}, trueFilter);
    }

    function grid(data, defFilter, params) {
      filter = defFilter || {};
      params = params || {};
      defaultFilter = angular.copy(filter);
      var dataset = typeof(data) === 'object' ? {dataset: data} : {
        getData: function ($defer, params) {
          getData(data, params, filter, function (response) {
            params.total(response.count);
            $defer.resolve(response.result);
          });
        }
      };
      dataset.groupOptions = {isExpanded: false, defaultSort: 'desc'}//event_date
      tableParams = new NgTableParams(params, dataset);
      return tableParams;
    }

    grid.reload = function () {
      tableParams.page(1);
      tableParams.reload();
    };
    grid.resetFilter = function () {
      if (!filterEquals()) {
        filter = {};
        grid.reload();
      }
      return filter;
    };
    grid.openEditor = function (params, callback) {
      console.log(params);
      model = params.model || model;
      if(model && params.data && params.data.id) {
        network.get(model, {id: params.data.id}, function(result, response) {
          if(result && response.result.length) {
            params.data = response.result[0];
            openModal(params, callback);
          } else {
            Notification.error({title: 'Zeile nicht gefunden', message: 'Bitte die Seite aktualisieren'});
          }
        });
      } else {
        openModal(params, callback);
      }

      function openModal(params, callback) {
        var modalInstance = $uibModal.open({
          animation: true,
          templateUrl: params.template || 'editTemplate.html',
          controller: params.controller || 'ModalEditController',
          size: params.size || 'lg',
          resolve: {
            data: function () {
              return params.data || {};
            },
            hint: function () {
              return params.hint;
            },
            modeView: function () {
              return params.modeView;
            }
          }          
        });
        
        

        modalInstance.result.then(function () {
          callback ? callback(true) : tableParams.reload();
        }, function() {
          if(callback) {
            callback(false)
          }
        });

      }



    };
    return grid;
  }
});

spi.service("HintService", function (network) {
  return function (code, callback) {
    network.get('hint', {filter: 1, page_code: code}, function (result, response) {
      if (result) {
        var hints = {};
        for (var i = 0; i < response.result.length; i++) {
          hints[response.result[i].position_code] = response.result[i].position_code == 'header' ?
          {title: response.result[i].title, text: response.result[i].description} : response.result[i].description;
        }
        callback(hints);
      }
    });
  };
});

spi.service("RequestService", function () {
  this.getProjectData = function() {};
  this.setRequestCode = function() {};
  this.financePlanData = function() {};
  this.getSchoolConceptData = function() {};
  this.getSchoolGoalData = function() {};
  this.initProjectData = function(data) {};
  this.initFinancePlan = function(data) {};
  this.initSchoolConcept = function(data) {};
  this.initSchoolGoal = function(data) {};
  
  this.updateFinansistPD = function(id) {};
  this.updateFinansistFP = function(id) {};
  this.initAll = function(data) {
    this.initProjectData(data);
    this.initFinancePlan(data);
    this.initSchoolConcept(data);
    this.initSchoolGoal(data);
  };
  
});


spi.factory('Utils', function (SweetAlert) {
  return {
    getRowById: function (items, id, field) {
      for (var i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          if (field && items[i][field] != undefined) {
            return items[i][field];
          }
          return items[i];
        }
      }
      return false;
    },
    getRowBy: function (items, field, value) {
      for (var i = 0; i < items.length; i++) {
        if (items[i][field] != undefined && items[i][field] == value) {
          return items[i];
        }
      }
      return false;
    },
    getFinanceTypes: function () {
      return [ {id: 'l', name: 'LM'}, {id: 'b', name: 'BP'}, {id: 'b', name: 'BP'}];
    },
    getSqlDate: function(d) {
      return d.getFullYear()+'-'+((d.getMonth()+1) < 10 ? '0'+(d.getMonth()+1) : d.getMonth()+1)+'-'+(d.getDate() < 10 ? '0'+d.getDate() : d.getDate());
    },
    doConfirm: function(callback) {
      SweetAlert.swal({
        title: "Sind Sie sicher?",
        text: "Diese Datei wird nicht wiederhergestellt!",
        type: "warning",
        confirmButtonText: "JA, LÖSCHEN!",
        showCancelButton: true,
        cancelButtonText: "ABBRECHEN",
        closeOnConfirm: false
      }, function(isConfirm){
        if(isConfirm) {
          callback();
        }
      });
    },    
    doCloseConfirm: function(callback) {
      SweetAlert.swal({
        title: "Sind Sie sicher?",
        text: "Sie haben neue Daten auf diese Seite eingegeben. Falls Sie die Seite ohne Ihre Daten zu speichern verlassen, so werden die Veränderungen verloren sein.",
        type: "warning",
        confirmButtonText: "OK",
        showCancelButton: true,
        cancelButtonText: "ABBRECHEN",
        closeOnConfirm: true
      }, function(isConfirm){          
         if(isConfirm) {
          callback();
         }
      });
    },   
    closeForm: function (formToClose){
      var form = formToClose;      
      result = false;   
      for(var item in form){
        if(form[item] && typeof form[item] == "object" && (form[item]['$dirty'] || form['$dirty'])){                 
          result = true;
          break;                     
        }
      }    
      return result;     
    },
    modalClosing: function (form, $uibModalInstance, event, reason, $redirect){
      if(arguments.length > 2 && !$redirect){
        if(reason == undefined){
          return true;
        };
        if (reason == "backdrop click" || reason == "cancel" || reason == "escape key press"){   
          event.preventDefault();      
        };
      };
      var self = this;
      var result = self.closeForm(form);    
      if(result){
        setTimeout(function(){  
          if($redirect) {
            self.doCloseConfirm(function() {
              location.href = $redirect; 
            });
          }else{
            self.doCloseConfirm(function() {
              $uibModalInstance.close();
            }); 
          }
        },10);      
      }else{
        if($redirect) {          
          location.href = $redirect;         
        }else{          
          $uibModalInstance.close();  
        }
      };  
    },
    deleteSuccess: function() {
      SweetAlert.swal("Gelöscht!", "Ihre Datei ist erfolgreich gelöscht!", "success");
    },
    getIdByPath: function() {
      var id = +location.pathname.split('/').pop();
      return !isNaN(id) ? id : 0;
    }
  };
});

spi.factory('SweetAlert', ['$rootScope', function ($rootScope) {

  var swal = window.swal;

  //public methods
  var self = {

    swal: function (arg1, arg2, arg3) {
      $rootScope.$evalAsync(function () {
        if (typeof(arg2) === 'function') {
          swal(arg1, function (isConfirm) {
            $rootScope.$evalAsync(function () {
              arg2(isConfirm);
            });
          }, arg3);
        } else {
          swal(arg1, arg2, arg3);
        }
      });
    },
    success: function (title, message) {
      $rootScope.$evalAsync(function () {
        swal(title, message, 'success');
      });
    },
    error: function (title, message) {
      $rootScope.$evalAsync(function () {
        swal(title, message, 'error');
      });
    },
    warning: function (title, message) {
      $rootScope.$evalAsync(function () {
        swal(title, message, 'warning');
      });
    },
    info: function (title, message) {
      $rootScope.$evalAsync(function () {
        swal(title, message, 'info');
      });
    },
    showInputError: function (message) {
      $rootScope.$evalAsync(function () {
        swal.showInputError(message);
      });
    },
    close: function () {
      $rootScope.$evalAsync(function () {
        swal.close();
      });
    }
  };

  return self;
}]);