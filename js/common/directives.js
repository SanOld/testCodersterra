spi.directive("spiHintMain", function () {
  return {
    restrict: 'A',
    scope: {
      header: '=',
      text: '='
    },
    template: '<div ng-if="header && text" class="hint-details alert alert-info m-0 clearfix"  ng-init="isCollapsed = 1"> <div class="heading-alert"> <strong ng-bind="header"></strong> <span ng-click="isCollapsed = !isCollapsed" class="show-link pull-right"> <span ng-show="isCollapsed">Zeigen</span> <span ng-hide="isCollapsed">Ausblenden</span> <span class="caret" ng-class="{\'open\': !isCollapsed}"></span> </span> </div> <div uib-collapse="isCollapsed"> <p ng-bind-html="text | nl2br"></p> </div> </div>'
  };
});

spi.directive("spiHint", function () {
  return {
    restrict: 'A',
    scope: {
      text: '=',
      class: '@'
    },
    template: '<button ng-if="text" uib-popover="{{text}}" class="btn btn-question {{class}}" type="button"> <i class="fa fa-question"></i> </button>'
  };
});

spi.directive("qqFileUpload", function (Notification) {
  return {
    restrict: 'A',
    scope: {
      setting: '='
    },
    template: '<div class="btn w-sw custom-color pull-right" id="file-uploader"></div>',
    link: function (scope, element, attrs, ctrl) {
      var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        action: '/api/upload-file/' + scope.setting.model + '/' + (scope.setting.params ? '?' + $.param(scope.setting.params) : ''),
        uploadButtonText: scope.setting.buttonText || '',
        customHeaders: scope.setting.customHeaders || {},
        sizeLimit: scope.setting.sizeLimit || 10520000,
        allowedExtensions: scope.setting.allowedExtensions || ['doc', 'docx', 'pdf'],
        messages: {
          typeError: "Unfortunately the file(s) you selected weren't the type we were expecting. Only {extensions} files are allowed",
          sizeError: "{file} is too large, maximum file size is {sizeLimit}",
          minSizeError: "{file} is too small, minimum file size is {minSizeLimit}",
          emptyError: "{file} is empty, please select files again without it",
          onLeave: "The files are being uploaded, if you leave now the upload will be cancelled"
        },
        showMessage: function (message) {
          Notification.error({title: 'File upload error', message: message});
        },
        onComplete: (scope.setting.onCompile) || function (id, fileName, responseJSON) {
          Notification.success(responseJSON.message);
        },
        onUpload: scope.setting.onUpload || function (id, fileName, xhr) {
        },
        onProgress: scope.setting.onProgress || function (id, fileName, loaded, total) {
        },
        onError: scope.setting.onError || function (id, fileName, xhr) {
          Notification.error(responseJSON.message);
        }
      });
    }
  };
});

spi.directive('disableAll', function ($timeout) {
  return {
    restrict: 'A',
    link: function (scope, element, attrs) {
      var _disableElements = ['input', 'button[uib-btn-radio]', 'textarea', 'select', '.select2'];
      var _skipClasses = ['cancel-btn', 'document-link', 'btn-question', 'fa-question', 'fa-info-circle'];

      scope.$watch(attrs.disableAll, function (isDisabled) {
        if(isDisabled) {
          $timeout(function() {
            disable(element[0], isDisabled);
          });
        }
      });

      var disable = function(element) {
        angular.element(element).addClass('disable-all');
        // element.addEventListener('click', preventDefault, true);
        for(var i = 0; i < _disableElements.length; i++) {
          disableElements(angular.element(element).find(_disableElements[i]), _disableElements[i] == 'textarea');
        }
      };

      var preventDefault = function(event) {
        for (var i = 0; i < event.target.classList.length; i++) {
          if(_skipClasses.indexOf(event.target.classList[i]) !== -1){
            return true;
          }
        }
        event.stopPropagation();
        event.preventDefault();
        return false;
      };

      var disableElements = function(elements, readonly) {
        var len = elements.length;
        for (var i = 0; i < len; i++) {
          var shouldDisable = true;
          for (var j = 0; j < elements[i].classList.length; j++) {
            if(_skipClasses.indexOf(elements[i].classList[i]) !== -1){
              shouldDisable = false;
              break;
            }
          }
          if (shouldDisable && elements[i].disabled === false) {
            if(readonly) {
              elements[i].readOnly = true;
            } else {
              elements[i].disabled = true;
            }
            elements[i].disabledIf = true;
          }
        }
      };

    }
  }
});

spi.directive("spiOnFocusLarge", function () {
  return {
    restrict: 'A',
    scope: {
      spiSave: '=',
      spiCancel: '=',
      callback: '&spiCallback',
      ngModel: '='
    },
    link: function (scope, element, attr) {
      var defaultText = element.val();
      element.bind('focus',function () {
        defaultText = element.val();
        element.addClass('animate');
      });
      scope.$watch('spiSave', function(val) {
        if(element.hasClass('animate')) {
          element.removeClass('animate');
          defaultText = element.val();
          scope.callback();
        }
      });
      scope.$watch('spiCancel', function(val) {
        if(element.hasClass('animate')) {
          scope.ngModel = defaultText;
          element.removeClass('animate');
        }
      });
    }
  };
});

spi.filter('nl2br', ['$sce', function ($sce) {
  return function (text) {
    return text ? $sce.trustAsHtml(text.replace(/\n/g, '<br/>')) : '';
  };
}]);
