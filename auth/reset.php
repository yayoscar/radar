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


    <!--[ IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link href="../css/bootstrap.css" rel="stylesheet"/>
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"/>
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
        <h1>Solicitud para Restablecer Password</h1>
    </div>

   <div class="row">

        <div class="span6">
			<form class="form-inline" id="formReset1">
				<div class="controls input-append">
					<input type="text" name="email" placeholder="Email" class="input-xlarge validate[required,custom[email]]" />
					<button type="button" class="btn btn-primary" id="submitReset1" data-loading-text="Solicitando...">Solicitar</button>
				</div>
			</form>
			<div id="message"></div>
			<div>
			<a href="<?php echo $auth_conf['base_url']?>"><span class="label label-warning">Iniciar Sesi&oacute;n</span></a>
			<a href="<?php echo $auth_conf['base_url']?>auth/reset2.php"><span class="label label-warning">He recibido mi c&oacute;digo de reinicio de password</span></a>
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

<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/jquery.validationEngine-es.js"></script>
<script src="../js/jquery.validationEngine.js"></script>
<script src="../js/bootstrap-alert.js"></script>
<script src="../js/bootstrap-button.js"></script>


<script>
$(document).ready(function() {
	$("#formReset1").validationEngine('attach');
	
	$("#submitReset1").click(function(){
		if($("#formReset1").validationEngine("validate"))
		{
			$("#submitReset1").button('loading');
			var datos=$("#formReset1").serializeArray();
			$.post("reset1Script.php",datos,function(data){
				if(data['error'] == 1)
				{	
					$("#message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				else if(data['error'] == 0)
				{
					$("#message").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				$("#submitReset1").button('reset');
			},"json");
		}
	});
	
	
});
</script>

</body>
</html>
