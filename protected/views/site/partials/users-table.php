<table id="datatable" ng-cloak ng-table="tableParams" class="table dataTable table-hover table-bordered table-edit">
  <tr ng-repeat="row in $data" ng-class="{'disable': row.is_active == '0'}">
    <td data-title="'Name'" sortable="'name'" ng-bind="::row.name"></td>
    <td ng-if="!page" data-title="'Benutzerrollen'" sortable="'type_name'" ng-bind="::row.type_name"></td>
    <td ng-if="!page" data-title="'Akteur'" sortable="'relation_name'" ng-bind="::row.relation_name"></td>
    <td data-title="'Benutzername'" sortable="'login'"><a href="#" ng-bind="::row.login"></a></td>
    <td data-title="'E-Mail'" sortable="'email'"><a href="mailto:{{row.email}}" ng-bind="::row.email"></a></td>
    <td data-title="'Telefon'" sortable="'phone'" ng-bind="::row.phone"></td>
    <td ng-if="canByType(['a','p','t'])" data-title="'Status'" sortable="'status_name'" ng-bind="::row.status_name"></td>
    <td data-title="'Ansicht / Bearbeiten'" header-class="'dt-edit'" class="dt-edit">
      <a class="btn center-block edit-btn" ng-click="openEdit(row, !canEdit(row))">
        <i class="ion-eye"  ng-if="!canEdit(row)"></i>
        <i class="ion-edit" ng-if="canEdit(row)"></i>
      </a>
    </td>
  </tr>
</table>
<div class="notice">
  <span class="color-notice"></span>
  Nicht aktiv Benutzer
</div>