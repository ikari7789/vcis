<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">

	<!-- Use the .htaccess and remove these lines to avoid edge case issues.
		 More info: h5bp.com/i/378 -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="description" content="">

	<!-- Mobile viewport optimized: h5bp.com/viewport -->
	<meta name="viewport" content="width=device-width">

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/reset.css">
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/default.css">

	<!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

	<!-- All JavaScript at the bottom, except this Modernizr build.
		 Modernizr enables HTML5 elements & feature detects for optimal performance.
		 Create your own custom Modernizr build: www.modernizr.com/download/ -->
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
		 chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	<div id="page" class="clearfix">
		<header>
			<div id="logo">
				<a href="http://www.uww.edu"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/w_logo.png" alt="UW-W Home" /></a>
				<a href="http://www.uww.edu/registrar/index.php"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/registrar_logo.png" alt="Registrar's Office" /></a>
			</div>
			<div id="header-content">
				<?php /* <?php echo CHtml::beginForm('#', 'get', array('id'=>'search')); ?>
					<?php echo CHtml::textField('terms'); ?>
					<?php echo CHtml::button('Search'); ?>
			</form> */ ?>
				<?php $this->widget('zii.widgets.CMenu', array(
					'items'=>array(
						array('label'=>'Administrative Tools', 'url'=>array('admin/index'), 'visible'=>!Yii::app()->user->isGuest), //, 'visible'=>!Yii::app()->user->isGuest),
						array('label'=>'View List', 'url'=>array('list/index')),
						array('label'=>'Help', 'url'=>array('site/page', 'view'=>'help')),
						array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('site/logout'), 'visible'=>!Yii::app()->user->isGuest),
					),
					'firstItemCssClass'=>'first',
					'id'=>'main-nav',
				)); ?>
			</div>
		</header>
		<?php //if(isset($this->breadcrumbs)):?>
			<?php $this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
				'homeLink'=>CHtml::link('VCIS (home)', array('site/index')),
				'encodeLabel'=>false,
			)); ?><!-- breadcrumbs -->
		<?php //endif ?>
		<?php if (!Yii::app()->db): ?>
			<div class="contentwrapper">
				Sorry, but there wasn't a database found.
			</div>
		<?php else: ?>
			<?php echo $content; ?>
		<?php endif; ?>
		<footer>

		</footer>
	</div>


	<!-- JavaScript at the bottom for fast page loading -->

	<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
	<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js"></script> -->
	<!-- <script>window.jQuery || document.write('<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-1.7.2.js"><\/script>')</script> -->

	<!-- scripts concatenated and minified via build script -->
	<!-- <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/plugins.js"></script> -->
	<!-- <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/script.js"></script> -->
	<!-- end scripts -->
</body>
</html>