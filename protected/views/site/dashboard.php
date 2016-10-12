<?php
/* @var $this SiteController */

$this->pageTitle = 'Startpage | ' . Yii::app()->name;

?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/dashboard.js"></script>

<div class="container home-dashboard" ng-controller="DashboardController">
    <div class="col-lg-12 animated fadeInDown text-center">
        <div class="row text-left">
            <div class="col-lg-4">
                <a class="box-home box-1" href="/about">
                    <h2>About us</h2>
                </a>
            </div>
            <div class="col-lg-4">
                <a ng-if="user.type_id == 1" class="box-home box-2" href="/login">
                    <h2>Sgn in</h2>
                </a>
                <a ng-if="user.type_id != 1" class="box-home box-2" href="/visit">
                    <h2>Statistics</h2>
                </a>
            </div>
            <div class="col-lg-4">
                <a class="box-home box-3" href="/contact">
                    <h2>Contact</h2>
                </a>
            </div>
        </div>
    </div>
</div>