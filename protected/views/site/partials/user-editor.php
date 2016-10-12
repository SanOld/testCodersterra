<script type="text/ng-template" id="editUserTemplate.html">
  <div class="panel panel-color panel-primary">
    <div class="panel-heading clearfix">
      <h3 ng-if="isInsert" class="m-0 pull-left">Benutzer hinzufügen</h3>
      <h3 ng-if="!isInsert && !modeView" class="m-0 pull-left">Benutzerprofil editieren</h3>
      <h3 ng-if="!isInsert && modeView" class="m-0 pull-left">Benutzerprofil ansicht</h3>
      <button type="button" ng-click="cancel()" class="close"><i class="ion-close-round "></i></button>
    </div>
    <div ng-show="(submited && form.$invalid) || error" class="alert alert-danger m-t-20">
      Bitte geben Sie die markierten Felder korrekt ein
    </div>
    <h3 class="subheading">Benutzerinformation</h3>
    <hr>
    <div class="panel-body">
      <form novalidate class="form-horizontal" name="form" disable-all="!canEdit() || modeView">
        <div class="form-group">
          <label class="col-lg-2 control-label">Status</label>
          <div class="group-btn clearfix m-t-20 hidden">
            <div class="pull-right">                  
              <button class="btn w-lg custom-btn" ng-if="canEdit() && !modeView" ng-click="submitForm(user)">Speichern</button>  
            </div>
          </div>         
          <div class="col-lg-4">
            <div ng-if="!isCurrentUser && !modeView" class="btn-group btn-toggle">
              <button class="btn btn-sm" ng-class="{'btn-default': user.is_active != 1}" ng-model="user.is_active"
                      uib-btn-radio="1">AKTIV
              </button>
              <button class="btn btn-sm" ng-class="{'btn-default': user.is_active != 0}" ng-model="user.is_active"
                      uib-btn-radio="0">NICHT AKTIV
              </button>
            </div>
            <span ng-if="isCurrentUser || modeView" class="no-edit-text">{{user.is_active ? 'Aktiv' : 'Nicht aktiv'}}</span>
            <span spi-hint text="_hint.is_active"></span>
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label p-r-0">Virtuelle Benutzer</label>

            <div class="col-lg-4">
              <div class="btn-group btn-toggle" ng-if="!(modeView || (!isAdmin && !userIsPA))">
                <button class="btn btn-sm" ng-class="{'btn-default': user.is_virtual != 1}" ng-model="user.is_virtual"
                        uib-btn-radio="1">JA
                </button>
                <button class="btn btn-sm" ng-class="{'btn-default': user.is_virtual != 0}" ng-model="user.is_virtual"
                        uib-btn-radio="0">NEIN
                </button>
              </div>
              <span ng-if="modeView || (!isAdmin && !userIsPA)" class="no-edit-text">{{user.is_virtual ? 'Ja' : 'Nein'}}</span>
              <span spi-hint text="_hint.is_virtual"></span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">Benutzerrollen</label>

          <div ng-if="isCurrentUser || !isInsert" class="col-lg-10">
            <span class="no-edit-text">{{type_name}}</span>
            <span spi-hint text="_hint.type_id"></span>
          </div>

          <div ng-if="!isCurrentUser && isInsert" class="col-lg-4 custom-width">
            <div spi-hint text="_hint.type_id" class="has-hint"></div>

            <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('type_id')}">
              <ui-select ng-change="reloadRelation()" ng-model="user.type_id" name="type_id" required>
                <ui-select-match placeholder="(Bitte wählen Sie)">{{$select.selected.name}}</ui-select-match>
                <ui-select-choices repeat="type.id as type in userTypes | filter: $select.search">
                  <span ng-bind-html="type.name | highlight: $select.search"></span>
                </ui-select-choices>
              </ui-select>
								<span ng-class="{hide: !fieldError('type_id')}" class="hide">
									<label ng-show="form.type_id.$error.required" class="error">Benutzerrollen erforderlich</label>
								</span>
            </div>
          </div>

        </div>
        
        <div class="form-group" ng-if="!(modeView && !user.function)">
          <label class="col-lg-2 control-label" for="title">Funktion</label>

          <div class="col-lg-4">
            <div spi-hint text="_hint.function" class="has-hint"></div>
            <div class="wrap-hint">
              <input id="title" class="form-control" ng-model="user.function" type="text" value="">
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="col-lg-2 control-label">Anrede</label>

          <div class="col-lg-10 holder-label-radio">
            <div class="radio-inline" ng-if="!modeView && canEdit()">
              <label for="radio1" class="cr-styled">
                <input type="radio" ng-model="user.sex" name="sex" value="1" id="radio1" ng-required="!user.sex">
                <i class="fa"></i>
                Herr
              </label>
            </div>
            <div class="radio-inline">
              <label for="radio2" class="cr-styled" ng-if="!modeView && canEdit()">
                <input type="radio" ng-model="user.sex" name="sex" value="2" id="radio2" ng-required="!user.sex">
                <i class="fa"></i>
                Frau
              </label>
              <span ng-if="modeView || !canEdit()" class="no-edit-text">{{user.sex == '1' ? 'Herr' : 'Frau'}}</span>
              <span spi-hint text="_hint.sex"></span>
            </div>
            <div ng-class="{hide: !fieldError('sex')}" class="hide">
              <label class="error" ng-show="form.sex.$error.required">Anrede erforderlich</label>
            </div>
          </div>
        </div>
        
        
        
        
        <div ng-if="(isInsert && isRelation) || (!isInsert && relation_name)" class="form-group">
          <label class="col-lg-2 control-label">Akteur</label>

          <div ng-if="!isInsert" class="col-lg-10">
            <span class="no-edit-text">{{relation_name}}</span>
            <span spi-hint text="_hint.relation_id"></span>
          </div>

          <div ng-if="isInsert" class="col-lg-10">
            <div spi-hint text="_hint.relation_id" class="has-hint"></div>

            <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('relation_id')}">
              <ui-select ng-disabled="!$select.items.length" ng-model="user.relation_id" name="relation_id" required>
                <ui-select-match placeholder="{{$select.disabled ? '(keine Items sind verfügbar)' :'(Bitte wählen Sie)'}}">
                  {{$select.selected.name}}
                </ui-select-match>
                <ui-select-choices repeat="item.id as item in relations | filter: $select.search">
                  <span ng-bind-html="item.name | highlight: $select.search"></span>
                </ui-select-choices>
              </ui-select>
								<span ng-class="{hide: !fieldError('relation_id')}" class="hide">
									<label ng-show="form.relation_id.$error.required" class="error">Akteur erforderlich</label>
								</span>
            </div>
          </div>
        </div>
        <div class="form-group" ng-if="isPerformer">
          <label class="col-lg-2 control-label two-line">Finanzielle Rechte</label>

          <div class="col-lg-10">
            <div ng-if="!isCurrentUser && !modeView" class="btn-group btn-toggle">
              <button class="btn btn-sm" ng-class="{'btn-default': user.is_finansist != 1}" ng-model="user.is_finansist"
                      uib-btn-radio="1">JA
              </button>
              <button class="btn btn-sm" ng-class="{'btn-default': user.is_finansist != 0}" ng-model="user.is_finansist"
                      uib-btn-radio="0">NEIN
              </button>
            </div>
            <span ng-if="isCurrentUser || modeView" class="no-edit-text">{{user.is_finansist ? 'Ja' : 'Nein'}}</span>
            <span spi-hint text="_hint.is_finansist"></span>
          </div>
        </div>
<!--        <div class="form-group">
          <label class="col-lg-2 control-label">Anrede</label>

          <div class="col-lg-10 holder-label-radio">
            <div class="radio-inline" ng-if="!modeView && canEdit()">
              <label for="radio1" class="cr-styled">
                <input type="radio" ng-model="user.sex" name="sex" value="1" id="radio1" ng-required="!user.sex">
                <i class="fa"></i>
                Herr
              </label>
            </div>
            <div class="radio-inline">
              <label for="radio2" class="cr-styled" ng-if="!modeView && canEdit()">
                <input type="radio" ng-model="user.sex" name="sex" value="2" id="radio2" ng-required="!user.sex">
                <i class="fa"></i>
                Frau
              </label>
              <span ng-if="modeView || !canEdit()" class="no-edit-text">{{user.sex == '1' ? 'Herr' : 'Frau'}}</span>
              <span spi-hint text="_hint.sex"></span>
            </div>
            <div ng-show="fieldError('sex')">
              <label class="error" ng-show="form.sex.$error.required">Anrede erforderlich</label>
            </div>
          </div>
        </div>-->
        <div class="row">
          <div class="col-lg-6">
            
            <div class="form-group" ng-if="!(modeView && !user.title)">
          <label class="col-lg-4 control-label" for="title">Titel</label>

          <div class="col-lg-8">
            <div spi-hint text="_hint.title" class="has-hint"></div>
            <div class="wrap-hint">
              <input id="title" class="form-control" ng-model="user.title" type="text" value="">
            </div>
          </div>
        </div>
        
        <div class="form-group has-feedback">
          <label class="col-lg-4 control-label" for="first_name">Vorname</label>
          <div class="col-lg-8">
            <div spi-hint text="_hint.first_name" class="has-hint"></div>
            <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('first_name')}">
              <input class="form-control" ng-model="user.first_name" name="first_name" type="text" id="first_name"
                     value="" ng-minlength="2" ng-maxlength="45" required>
                <span ng-class="{hide: !fieldError('first_name')}" class="hide">
                <label ng-show="form.first_name.$error.required" class="error">Vorname erforderlich</label>
                <label ng-show="form.first_name.$error.minlength" class="error">Vorname ist zu kurz</label>
                <label ng-show="form.first_name.$error.maxlength" class="error">Vorname ist zu lang</label>
                <span class="glyphicon glyphicon-remove form-control-feedback"></span>
              </span>
            </div>
          </div>
        </div>
        <div class="form-group has-feedback">
          <label class="col-lg-4 control-label" for="lname">Nachname</label>
          <div class="col-lg-8">
            <div spi-hint text="_hint.last_name" class="has-hint"></div>
            <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('last_name')}">
              <input class="form-control" ng-model="user.last_name" name="last_name" type="text" id="lname" value=""
                     ng-minlength="2" ng-maxlength="45" required>
              <span ng-class="{hide: !fieldError('last_name')}" class="hide">
                <label ng-show="form.last_name.$error.required" class="error">Nachname erforderlich</label>
                <label ng-show="form.last_name.$error.minlength" class="error">Nachname ist zu kurz</label>
                <label ng-show="form.last_name.$error.maxlength" class="error">Nachname ist zu lang</label>
                <span class="glyphicon glyphicon-remove form-control-feedback"></span>
              </span>
            </div>
          </div>
        </div>
            
<!--            <div class="form-group" ng-if="!(modeView && !user.title)">
              <label class="col-lg-4 control-label" for="title">Titel</label>

              <div class="col-lg-8">
                <div spi-hint text="_hint.title" class="has-hint"></div>
                <div class="wrap-hint">
                  <input id="title" class="form-control" ng-model="user.title" type="text" value="">
                </div>
              </div>
            </div>-->
<!--            <div class="form-group" ng-if="!(modeView && !user.function)">
              <label class="col-lg-4 control-label" for="title">Funktion</label>

              <div class="col-lg-8">
                <div spi-hint text="_hint.function" class="has-hint"></div>
                <div class="wrap-hint">
                  <input id="title" class="form-control" ng-model="user.function" type="text" value="">
                </div>
              </div>
            </div>-->
<!--            <div class="form-group has-feedback">
              <label class="col-lg-4 control-label" for="first_name">Vorname</label>
              <div class="col-lg-8">
                <div spi-hint text="_hint.first_name" class="has-hint"></div>
                <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('first_name')}">
                  <input class="form-control" ng-model="user.first_name" name="first_name" type="text" id="first_name"
                         value="" ng-minlength="2" ng-maxlength="45" required>
                    <span ng-show="fieldError('first_name')">
                    <label ng-show="form.first_name.$error.required" class="error">Vorname erforderlich</label>
                    <label ng-show="form.first_name.$error.minlength" class="error">Vorname ist zu kurz</label>
                    <label ng-show="form.first_name.$error.maxlength" class="error">Vorname ist zu lang</label>
                    <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group has-feedback">
              <label class="col-lg-4 control-label" for="lname">Nachname</label>
              <div class="col-lg-8">
                <div spi-hint text="_hint.last_name" class="has-hint"></div>
                <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('last_name')}">
                  <input class="form-control" ng-model="user.last_name" name="last_name" type="text" id="lname" value=""
                         ng-minlength="2" ng-maxlength="45" required>
                  <span ng-show="fieldError('last_name')">
                    <label ng-show="form.last_name.$error.required" class="error">Nachname erforderlich</label>
                    <label ng-show="form.last_name.$error.minlength" class="error">Nachname ist zu kurz</label>
                    <label ng-show="form.last_name.$error.maxlength" class="error">Nachname ist zu lang</label>
                    <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                  </span>
                </div>
              </div>
            </div>-->

          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label class="col-lg-3 control-label" for="login">Benutzername</label>

              <div class="col-lg-9">
                <div spi-hint text="_hint.login" class="has-hint"></div>
                <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('login')}">
                  <input class="form-control" type="text" name="login" ng-model="user.login" id="login" value=""
                         ng-disabled="isCurrentUser && !isAdmin" ng-minlength="3" ng-maxlength="45" required>
									<span ng-class="{hide: !fieldError('login')}" class="hide">
										<label ng-show="form.login.$error.required" class="error">Benutzername erforderlich</label>
										<label ng-show="form.login.$error.minlength" class="error">Benutzername ist zu kurz</label>
										<label ng-show="form.login.$error.maxlength" class="error">Benutzername ist zu lang</label>
                    <label ng-show="error.login.dublicate" class="error">Dieser Benutzername bereits registriert</label>
										<span class="glyphicon glyphicon-remove form-control-feedback"></span>
									</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-3 control-label" for="email">E-Mail</label>

              <div class="col-lg-9">
                <div spi-hint text="_hint.email" class="has-hint"></div>
                <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('email')}">
                  <input class="form-control" type="email" name="email" ng-model="user.email" id="email" value=""
                         ng-maxlength="45" required ng-pattern="emailFormat">
									<span ng-class="{hide: !fieldError('email')}" class="hide">
										<label ng-show="form.email.$error.required" class="error">E-Mail erforderlich</label>
										<label ng-show="form.email.$error.email || form.email.$error.pattern" class="error">Geben Sie eine gültige E-Mail ein</label>
										<label ng-show="form.email.$error.maxlength" class="error">Username ist zu lang</label>
										<label ng-show="error.email.dublicate" class="error">This E-Mail already registered</label>
										<span class="glyphicon glyphicon-remove form-control-feedback"></span>
									</span>
                </div>
              </div>

            </div>
            <div class="form-group" ng-if="!(modeView && !user.phone)">
              <label class="col-lg-3 control-label" for="phone">Telefon</label>

              <div class="col-lg-9">
                <div spi-hint text="_hint.phone" class="has-hint"></div>
                <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('phone')}">
                  <input class="form-control" type="text" name="phone" ng-model="user.phone"  value="" ng-pattern="/^[^A-Za-z]*$/">
                  <span ng-class="{hide: !fieldError('phone')}" class="hide">
										<label ng-show="form.phone.$error.pattern" class="error">Telefon must not contain letters</label>
										<span class="glyphicon glyphicon-remove form-control-feedback"></span>
									</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row" ng-if="!modeView && (isCurrentUser || canEdit()) && user.is_virtual != 1">
          <div class="form-custom-box clearfix">
            <div class="col-lg-12">
              <h4>Passwort ändern</h4>
            </div>
            <div ng-if="isCurrentUser" class="col-lg-4">
              <label>Altes Passwort</label>

              <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('old_password'), 'wrap-line error': fieldError('password_repeat')}">
                <input class="form-control" name="old_password" ng-model="user.old_password" type="password" value="">
								  <span ng-class="{hide: !fieldError('old_password')}" class="hide">
                    <label ng-show="error.old_password.error" class="error">Altes Passwort ist falsch</label>
                    <span class="glyphicon glyphicon-remove form-control-feedback"></span>
								  </span>
              </div>
            </div>
            <div ng-class="isCurrentUser ? 'col-lg-4' : 'col-lg-6'">
              <label>Neues Passwort</label>

              <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('password'), 'wrap-line error': fieldError('password_repeat')}">
                <input class="form-control" name="password" ng-model="user.password" type="password" value=""
                       ng-minlength="3" ng-required="isInsert && user.is_virtual != 1 || (loaded_is_virtual == 1 && user.is_virtual == 0 )">
								  <span ng-class="{hide: !fieldError('password')}" class="hide" >
                    <label ng-show="form.password.$error.required" class="error">Passwort erforderlich</label>
                    <label ng-show="form.password.$error.minlength" class="error">Passwort ist zu kurz</label>
                    <span class="glyphicon glyphicon-remove form-control-feedback"></span>
								  </span>
              </div>
            </div>
            <div ng-class="isCurrentUser ? 'col-lg-4' : 'col-lg-6'">
              <label>Neues Passwort bestätigen</label>

              <div class="wrap-hint" ng-class="{'wrap-line error': fieldError('password_repeat')}">
                <input class="form-control" name="password_repeat" ng-model="password_repeat" type="password" value=""
                       ng-pattern="user.password.length ? user.password : '^$'" ng-required="isInsert || user.password.length">
								<span ng-class="{hide: !fieldError('password_repeat')}" class="hide">
									<label ng-show="form.password_repeat.$error.required" class="error">Passwort bestätigen erforderlich</label>
									<label ng-show="form.password_repeat.$error.pattern" class="error">Passwörter sind nicht gleich</label>
									<span class="glyphicon glyphicon-remove form-control-feedback"></span>
								</span>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group group-btn">
          <div class="col-lg-2" ng-if="!isInsert && !isCurrentUser && canDelete() && !modeView">
            <a class="btn btn-icon btn-danger btn-lg sweet-4" ng-click="remove(userId)">
              <i class="fa fa-trash-o"></i>
            </a>
          </div>
          <div class="col-lg-6 text-right pull-right">
            <button class="btn w-lg cancel-btn" ng-click="cancel()">Abbrechen</button>
            <button class="btn w-lg custom-btn" ng-if="canEdit() && !modeView" ng-click="submitForm(user)">Speichern</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</script>