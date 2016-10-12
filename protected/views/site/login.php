<?php
/* @var $this SiteController */

$this->pageTitle = 'Login | ' . Yii::app()->name;
?>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>

<div ng-controller="LoginController" class="wrapper-page animated fadeInDown">
    <div class="panel panel-color panel-primary">
        <div class="panel-heading">
            <h3 class="text-center m-t-10">Login</h3>
        </div>

        <form novalidate method="post" name="form" id="loginForm" class="cmxform form-horizontal m-t-40">
            <div ng-style="error && {'display': 'block'}" class="alert alert-danger text-center" ng-bind="error">

            </div>
            <div class="form-group has-feedback">
                <label class="col-xs-12" for="username">Login</label>
                <div class="col-xs-12 wrap-line"  ng-class="{'error': fieldError('login'), 'success': fieldSuccess('login')}">
                    <input class="form-control" ng-model="user.login" type="text" id="username" name="login" required>
                    <label ng-show="fieldError('login') && form.$submitted" class="error" for="username">Enter login</label>
                    <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                    <span class="glyphicon glyphicon-ok form-control-feedback"></span>
                </div>
            </div>
            <div class="form-group has-feedback">
                <label class="col-xs-12" for="password">Password</label>
                <div class="col-xs-12 wrap-line"  ng-class="{'error': fieldError('password'), 'success': fieldSuccess('password')}">
                    <input class="form-control" ng-model="user.password" type="password" id="password" name="password" required>
                    <label ng-show="fieldError('password') && form.$submitted" class="error" for="password">Please enter a password</label>
                    <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                    <span class="glyphicon glyphicon-ok form-control-feedback"></span>
                </div>
            </div>

            <div class="form-group ">
                <div class="col-xs-12">
                    <label class="cr-styled">
                        <input ng-model="user.remember" ng-true-value="1" ng-false-value="0" type="checkbox">
                        <i class="fa"></i>
                        Remember me
                    </label>
                </div>
            </div>

            <div class="form-group text-right">
                <div class="col-xs-12">
                    <button ng-click="submitForm(user)" class="btn btn-block btn-lg btn-purple w-md custom-btn" type="submit">Sign in</button>
                </div>
            </div>
            <div class="form-group m-t-20">
                <div class="col-sm-12 text-center">
                    <a href="/forgot-password">Forgot your password?</a>
                </div>
            </div>
        </form>
    </div>
    <div class="m-t-30">
        <address class="ng-scope">
            <strong>Programm Name</strong><br/>
            Programm summary description<br/>
            Street AV 5-7<br/>
            100256 Kharkov
            <p class="m-t-10">Tel.: +222 22 22<br />
                Fax.: +222 22 22</p>
            <p class="m-t-10"><a target="_blank" href="mailto:shehovtsov_av@mail.ru">E-mail send</a><br/>
                <a target="_blank" href="">programm.com</a></p>
        </address>
    </div>
</div>
