<!DOCTYPE html>
<html lang="en">
<head>
     

  <?php include('head.php'); ?>
</head>
<body ng-class="{'bg': _m == 'dashboard' || _m == 404}" ng-app="spi" ng-controller="main">
<div id="page">
  <header class="top-head container-fluid">
    <div class="container">
      <div class="logo p-0 m-t-10 m-b-10 col-xs-6">
        <a href="/dashboard">
          <img src="<?php echo $baseUrl; ?>/images/logo.png" alt="logo">
        </a>
      </div>
      <div class="logo logo-print p-0 m-t-20 m-b-15 col-xs-5">
        <a target="_blank" href="" class="pull-right">
          <img src="<?php echo $baseUrl; ?>/images/logo2.png" alt="logo">
        </a>
      </div>
      <ul class="list-inline navbar-right top-menu top-right-menu profile-box">
        <li class="dropdown text-center">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <img class="img-circle profile-img thumb-sm" src="<?php echo $baseUrl; ?>/images/avatar.jpg" alt="">

            <div class="holder-box">
              <span class="username" ng-bind="user.login">&nbsp;</span>
              <span class="organization" ng-bind="user.relation_name">&nbsp;</span>
            </div>
            <span class="caret"></span>
          </a>
          <ul style="overflow: hidden; outline: none;" tabindex="5003"
              class="dropdown-menu extended pro-menu fadeInUp animated">
            <li><a ng-if="user" href="" ng-click="openUserProfile()"><i class="fa fa-briefcase"></i>Profil</a></li>
            <li><a ng-if="user" href="" ng-click="logout()"><i class="fa fa-sign-out"></i>Logout</a></li>
            <li><a ng-if="!user" href="" ng-click="login()"><i class="fa fa-sign-out"></i>Login</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </header>

  <?php include('menu.php'); ?>

  <?php if (isset($this->breadcrumbs) && $this->breadcrumbs): ?>
    <div class="container">
      <?php
      $this->widget('zii.widgets.CBreadcrumbs', 
              array('links'                 => $this->breadcrumbs, 
                    'homeLink'              => '<li>' . CHtml::link('Home', array('/dashboard')) . '</li>',
                    'tagName'               => 'ul', 
                    'separator'             => '', 
                    'activeLinkTemplate'    => '<li><a href="{url}">{label}</a></li>', 
                    'inactiveLinkTemplate'  => '<li ng-cloak class="active">{label}</li>',
                    'htmlOptions'           => array('class' => 'breadcrumb p-0'))); ?>
    </div>
  <?php endif; ?>

  <?php echo $content; ?>
</div>

<div class="footer">
  <div class="container">
    <div class="col-lg-7">
      <a href="" class="pull-left m-t-10">
        <img src="<?php echo $baseUrl; ?>/images/logo2-small.png" alt="logo">
      </a>
    </div>
    <div class="col-lg-5 contact-footer">
      <a target="_blank" href="" class="pull-right m-l-15">
        <img src="<?php echo $baseUrl; ?>/images/logo3.png" alt="logo">
      </a>
    </div>
  </div>
</div>
<div class="md-overlay"></div>

<?php include(Yii::app()->getBasePath().'/views/site/partials/user-editor.php'); ?>

</body>
</html>


