<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);

Yii::app()->clientScript->registerCss('error','
	body {font-family:"Verdana";font-weight:normal;color:black;background-color:white;}
	h1 { font-family:"Verdana";font-weight:normal;font-size:18pt;color:red }
	h2 { font-family:"Verdana";font-weight:normal;font-size:14pt;color:maroon }
	h3 {font-family:"Verdana";font-weight:bold;font-size:11pt}
	p {font-family:"Verdana";font-weight:normal;color:black;font-size:9pt;margin-top: -5px}
	.version {color: gray;font-size:8pt;border-top:1px solid #aaaaaa;}
');
?>

<!-- <h2>Error <?php echo $code; ?></h2>

<div class="error">
<?php echo $message; ?>
</div> -->

<h1>Error <?php echo $code; ?></h1>
<h2><?php echo nl2br(CHtml::encode($message)); ?></h2>
<p>
The above error occurred when the Web server was processing your request.
</p>
<p>
If you think this is a server error, please contact <?php echo Yii::app()->params['adminEmail']; ?>.
</p>
<p>
Thank you.
</p>
<div class="version">
<?php echo date('Y-m-d H:i:s',time()); ?>
</div>