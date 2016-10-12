var spi = angular.module('spi', [
    'ngSanitize',
    'ui.select',
    'LocalStorageModule',
    'ngTable',
    'ui.bootstrap',
    //'ui.mask',
    'ngAnimate',
    'ui-notification',
    'mm.iban',
    'summernote',
    'ui.bootstrap.accordion',
    'ngCookies'
]);

spi.run(function(ngTableDefaults, $templateCache) {
    ngTableDefaults.params.count = 10;
    ngTableDefaults.settings.counts = [10, 25, 50, 100];
    $templateCache.put('ng-table/header.html', '<tr><th title="{{column.headerTitle(this)}}" ng-repeat="column in $columns" class="{{column.class(this)}}" ng-class="{\'sorting\': column.sortable(this), \'sorting_asc\': column.sortable(this) && tableParams.sorting()[column.sortable(this)]==\'asc\', \'sorting_desc\': column.sortable(this) && tableParams.sorting()[column.sortable(this)]==\'desc\'}" ng-click="column.sortable(this) && tableParams.sorting(column.sortable(this), params.sorting()[column.sortable(this)]==\'asc\' ? \'desc\' : \'asc\')" ng-if="column.show(this)" ng-init="template=column.headerTemplateURL(this)">{{column.title(this)}}<div ng-if="template" ng-include="template"></div></th></tr>');
    $templateCache.put('ng-table/pager.html', '<div ng-init="countModel = params.count()" class="ng-cloak wrap-paging clearfix"> <div class="dataTables_info" id="datatable_info">Seite {{params.page()}} von {{pages.length ? pages.length - 2 : 1}} aus {{params.total()}} Einträgen</div> <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate"> <ul ng-if="pages.length" class="pagination"> <li class="paginate_button" ng-class="{\'disabled\': !page.active && !page.current, \'active\': page.current}" ng-repeat="page in pages" ng-switch="page.type"> <a ng-switch-when="prev" ng-click="params.page(page.number)" href="">Zurück</a> <a ng-switch-when="first" ng-click="params.page(page.number)" href=""> <span ng-bind="page.number"></span> </a> <a ng-switch-when="page" ng-click="params.page(page.number)" href=""> <span ng-bind="page.number"></span> </a> <a ng-switch-when="more" ng-click="params.page(page.number)" href="">&#8230;</a> <a ng-switch-when="last" ng-click="params.page(page.number)" href=""> <span ng-bind="page.number"></span> </a> <a ng-switch-when="next" ng-click="params.page(page.number)" href="">Weiter</a> </li> </ul> </div> <div ng-if="params.settings().counts.length && params.total() > 10" class="dataTables_length" id="datatable_length"> <label> <select name="datatable_length" ng-model="countModel" ng-change="params.count(countModel)" class="form-control input-sm" ng-options="count for count in params.settings().counts"> </select>  Objekte pro Seite </label></div></div>');
    // $templateCache.put("uib/template/popover/popover.html",'<div class="popover {{placement}}" ng-class="{ in: isOpen(), fade: animation() }"> <div class="arrow" style="left: 50%;"></div><i ng-click="$parent.isOpen = false" class="ion-close-round"></i><div class="popover-content" ng-bind="content"></div>');
    $templateCache.put("angular-ui-notification.html",'<div class=\"ui-notification\"><div class="image"><i class="fa" ng-class="{\'fa-check\': t == \'s\', \'fa-exclamation\': t == \'e\', \'fa-question\': t == \'i\', \'fa-warning\': t == \'w\', \'ion-ios7-information \': t == \'p\'}"></i></div><div class="text-wrapper"><div class="title" ng-show=\"title\" ng-bind-html=\"title\"></div><div class="text" ng-bind-html=\"message\"></div></div></div>');
    $templateCache.put("uib/template/tabs/tabset.html",
      "<div>\n" +
      "  <div class=\"row\"><ul class=\"row nav nav-{{tabset.type || 'tabs'}}\" ng-class=\"{'nav-stacked': vertical, 'nav-justified': justified}\" ng-transclude></ul></div>\n" +
      "  <div class=\"tab-content\">\n" +
      "    <div class=\"tab-pane\"\n" +
      "         ng-repeat=\"tab in tabset.tabs\"\n" +
      "         ng-class=\"{active: tabset.active === tab.index}\"\n" +
      "         uib-tab-content-transclude=\"tab\">\n" +
      "    </div>\n" +
      "  </div>\n" +
      "</div>\n" +
      "");
  $templateCache.put("uib/template/popover/popover.html",
    "<div ng-show=\"$parent.isOpen\" class=\"popover {{placement}}\"\n" +
    "  tooltip-animation-class=\"fade\"\n" +
    "  uib-tooltip-classes\n" +
    "  ng-class=\"{ in: isOpen() }\">\n" +
    "  <div class=\"arrow\"></div><i ng-click=\"$parent.isOpen = false\" class=\"ion-close-round {{$parent.isOpen}}\"></i>\n" +
    "\n" +
    "  <div class=\"popover-inner\">\n" +
    "      <h3 class=\"popover-title\" ng-bind=\"uibTitle\" ng-if=\"uibTitle\"></h3>\n" +
    "      <div class=\"popover-content\" ng-bind=\"content\"></div>\n" +
    "  </div>\n" +
    "</div>\n" +
    "");
});

spi.config(function($locationProvider, $uibTooltipProvider, NotificationProvider, uiSelectConfig) {
  $uibTooltipProvider.options({trigger: 'outsideClick', placement: 'auto top', appendToBody: 'true'});
  NotificationProvider.setOptions({
      delay: 5000,
      startTop: 10,
      startRight: 10,
      verticalSpacing: 3,
      horizontalSpacing: 3,
      positionX: 'right',
      positionY: 'top'
  });
  uiSelectConfig.theme = 'select2';
  uiSelectConfig.appendToBody = true;
  $locationProvider.html5Mode(true);
  $('a').each(function(){
    if (!$(this).is('[target]') && !$(this).is('[ng-href]')) {
      $(this).attr('target', '_self');
    }
  });
});


Date.prototype.iso = function() {
  var yyyy = this.getFullYear().toString();
  var mm = (this.getMonth()+1).toString();
  var dd  = this.getDate().toString();
  return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0])+'T'+this.toLocaleTimeString();
 };
Date.prototype.ymd = function() {
  var yyyy = this.getFullYear().toString();
  var mm = (this.getMonth()+1).toString();
  var dd  = this.getDate().toString();
  return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
 };
Date.prototype.dmy = function() {
  var yy = (this.getFullYear().toString()).split('20').join('');
  var mm = (this.getMonth()+1).toString();
  var dd  = this.getDate().toString();
  return (dd[1]?dd:"0"+dd[0]) + '.' + (mm[1]?mm:"0"+mm[0])+'.'+yy ;
 };