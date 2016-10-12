<?php
$this->pageTitle = 'Contact | ' . Yii::app()->name;
$this->breadcrumbs = array('Contact');
?>
			<!-- Page Content Start -->
			<!-- ================== -->
			
			<div class="wraper container-fluid">
				<div class="row">
					<div class="container center-block">
						<div class="panel panel-default">
							<div class="panel-heading clearfix">
								<h1 class="panel-title col-lg-6">Contact</h1>
							</div>
							<div class="panel-body contact-block">
								<div class="row">
									<div class="col-lg-6">
										<ul class="list-unstyled">
											<li class="address">
												<address>
													<strong>Point1</strong>
													<span><strong>Description</strong></span>
													<span>Part 1</span>
													<span>Kharkov</span>
												</address>
											</li>
											<li class="contact-phone">
												<dl class="dl-horizontal">
													<dt>Phone:</dt>
													<dd> +3 (067) 333-33-33 </dd>
												</dl>
											</li>
											<li class="contact-email">
												<a href="mailto:shehovtsov_av@mail.ru">E-Mail Send</a>
											</li>
										</ul>

									</div>
									<div class="col-lg-6">
										<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d606.9895126108502!2d13.4159470038166!3d52.516098241428715!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47a84e2309f23001%3A0xfb148ef06b4adf58!2sSchicklerstra%C3%9Fe+5%2C+10179+Berlin%2C+Germany!5e0!3m2!1sen!2sua!4v1464094492782" width="550" height="210" frameborder="0" style="border:0" allowfullscreen></iframe>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- End Row -->
			</div>
		</div>

		<script src="js/lib/jquery.min.js"></script>

		<!-- js placed at the end of the document so the pages load faster -->
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/pace.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/wow.js"></script>

		<!--common script for all pages-->
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.js"></script>

		<!-- validation form -->
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/jquery.validate.min.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/form-validation-init.js"></script>
		
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/lib/sweet-alert.js"></script>

		
		<script src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.9/api/fnFilterClear.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/datatables/dataTables.bootstrap.js"></script>

		<!-- Modal-Effect -->
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/modal-effect/js/classie.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/modal-effect/js/modalEffects.js"></script>

		<!-- Datepicker -->
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/timepicker/bootstrap-datepicker.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/timepicker/bootstrap-datepicker.de.js"></script>

		<!-- Select -->
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/jquery-multi-select/jquery.quicksearch.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/select2/select2.min.js" type="text/javascript"></script>

		<!-- Wisiwig -->
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

    <!--form validation init-->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/assets/summernote/summernote.min.js"></script>
	</body>
</html>