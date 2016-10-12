<?php 
  session_start();
?>
<nav class="navbar navbar-default header-nav m-b-0">
	<div class="container">
		<ul class="nav navbar-nav">
			<li ng-class="{'active': _m=='dashboard'}"><a href="/dashboard">Home</a></li>
      <li><a href="/about">About us</a></li>
      <li><a href="/contact">Contact</a></li>
      <?php if($_SESSION['rights']['visit']['show']): ?>
      <li><a href="/visit">Statistics</a></li>
      <?php endif; ?>
		</ul>
	</div>   
</nav>