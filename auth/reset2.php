<?php
require_once("../class/class.inputfilter.php");
require_once("../class/config.php");
$ifilter = new InputFilter();
$_GET = $ifilter->process($_GET);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>radar|dgeti</title>

    <link href="../css/bootstrap.css" rel="stylesheet"/>
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"/>
	<link href="../css/bootstrap-modal.css" rel="stylesheet"/>
	<link href="../css/font-awesome.css" rel="stylesheet"/>
	<link href="../css/validationEngine.jquery.css" rel="stylesheet"/>
	

</head>

<body>
<!-- Navbar
  ================================================== -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">

            <a class="logo-radar" href="#"><img src="../img/logo.png"/></a>

        </div>
    </div>
</div>

<div class="container">





<!-- Typography
================================================== -->
<section id="login">
    <div class="page-header">
        <h1>Restablecer Password</h1>
    </div>

   <div class="row">

        <div class="span6">
			<form class="form-inline" id="formReset2">
				<div class="controls input-append">
					<input type="text" id="llave" name="llave" placeholder="C&oacute;digo de Restablecimiento" class="input-xlarge validate[required,minSize[20],maxSize[20]]"
					 <?php if(isset($_GET['llave'])) { echo "value=\"" . $_GET['llave'] . "\""; } ?>>
					<button type="button" class="btn btn-primary" id="submitReset2">Restablecer</button>
				</div>
			</form>
			<div id="message"></div>
			<div>
			<a href="<?php echo $auth_conf['base_url']?>"><span class="label label-warning">ya record&eacute; mi password</span></a>
			<div>
        </div>

        

    </div>

</section>



<!-- Footer
 ================================================== -->
<hr>

<footer id="footer">
    Desarrollado en el  <a target="_blank" href="http://cbtis72.edu.mx">Centro de Bachillerato Tecnol&oacute;gico industrial y de servicios 72 "Andr&eacute;s Quintana Roo"</a>.
</footer>

</div><!-- /container -->

<!-- modal -->
<div id="modalReset3" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
		<h3>Restablecer Password</h3>
	</div>
	<div class="modal-body">
	   <form class="form-horizontal" id="formReset3">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls">
						<input type="password" id="password" name="password" placeholder="" class="input-xlarge  validate[required,minSize[6]] ">
						<input type="hidden" name="password_sha1" id="password_sha1" />
						<input type="hidden" name="llave2" id="llave2" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="password_confirm">Password (Confirmaci&oacute;n)</label>
					<div class="controls">
						<input type="password" id="password_confirm" name="password_confirm" placeholder="" class="input-xlarge  validate[required,equals[password]]">
					</div>
				</div>
			</fieldset>
		</form>
		<div id="message2"></div>
	</div>
	
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" id="submitReset3" data-loading-text="Restableciendo...">Restablecer</button>
	</div>
</div> <!-- /modal -->




<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/jquery.crypt.js"></script>
<script src="../js/jquery.validationEngine-es.js"></script>
<script src="../js/jquery.validationEngine.js"></script>
<script src="../js/bootstrap-alert.js"></script>
<script src="../js/bootstrap-button.js"></script>
<script src="../js/bootstrap-modalmanager.js"></script>
<script src="../js/bootstrap-modal.js"></script>


<script>
$(document).ready(function() {
	$("#formReset2").validationEngine('attach');
	$("#formReset3").validationEngine('attach');
	
	$("#submitReset2").click(function(){
		if($("#formReset2").validationEngine("validate"))
		{
			var datos=$("#formReset2").serializeArray();
			$.post("reset2Script.php",datos,function(data){
				if(data['error'] == 1)
				{	
					$("#message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				else if(data['error'] == 0)
				{
					$("#message").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
					setTimeout(function() {
						$(".alert").alert('close')
						$("#modalReset3").modal("show");
					},1200);
				}
			},"json");
		}
	});
	
	$("#submitReset3").click(function(){
		if($("#formReset3").validationEngine("validate"))
		{
			$("#submitReset3").button("loading");
			$("#password_sha1").val($().crypt({method:"sha1",source:$().crypt({method:"sha1",source:$("#password").val()})}));
			$("#llave2").val($("#llave").val());
			var valores=$("#formReset3").serializeArray();
			$.post("reset3Script.php",valores,function(data){
				if(data['error'] == 1)
				{
					$("#message2").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				else if(data['error'] == 0)
				{
					$("#message2").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				setTimeout(function() {
					location.href = "<?php echo $auth_conf['base_url'] ?>"; 
				}, 2000);
			},"json");
		}
	});
	
	
});
</script>

</body>
</html>
