<?php
include("class/auth.class.php");
include("class/config.php");
include("fbmain.php");

$auth = new Auth;

if(isset($_COOKIE['auth_session']))
{
	$hash = $_COOKIE['auth_session'];

	if($auth->checkSession($hash))
	{
		header('Location: home');
	}
}

?>

<!DOCTYPE html>
<html lang="es" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
  <script src="js/jquery-1.8.3.min.js"></script>
  <script src="js/jquery.cookie.js"></script>
  <?php
    if ($user){ 
      $me = $facebook->api('/me'); //user
      $fbid = $facebook->getUser();
      $email = $me['email'];
      
 
     if($login=$auth->fblogin($fbid,$email)){
      ?>
        <script>
          $.cookie("auth_session", "<?php echo $login['session_hash']?>", { expires: 30 });
          location.href = "home/index.php";
        </script>
      <?php
    //header('Location: home.php');
      }
      else{
        $auth->addFacebook($fbid,$email); ?>
        <script>
          location.href = "index.php";
        </script>
      <?php
    //header('Location: home.php');
      }
    }
  ?>
    <meta charset="utf-8">
    <title>radar|dgeti</title>
    <link rel="shortcut icon" href="img/favicon.png">


    <link href="css/bootstrap.css" rel="stylesheet"/>
    <link href="css/bootstrap-responsive.css" rel="stylesheet"/>
		<link href="css/bootstrap-modal.css" rel="stylesheet"/>
    <link href="css/font-awesome.css" rel="stylesheet"/>
		<link href="css/validationEngine.jquery.css" rel="stylesheet"/>

	
	

</head>

<body>
<input type="hidden" value="<?=$loginUrl?>" id="loginUrl"/>
  
<!-- Navbar
  ================================================== -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">

            <a class="logo-radar" href="#"><img src="img/logo.png"/></a>

        </div>
    </div>
</div>

<div class="container">





<!-- Typography
================================================== -->
<section id="login">
    <div class="page-header">
        <h1>Bienvenido</h1>
    </div>

   <div class="row">

        <div class="span4">
			
		</div>

        <div class="span4">
             <form id="form-login" method="post">
					<input type="text" id="email-login" class="span4 validate[required,custom[email]]" name="email-login" placeholder="Email" />
                    <input type="password" id="password-login" class="span4 validate[required]" name="password-login" placeholder="Password" />
					<input type="hidden" name="password-sha1-login" id="password-sha1-login" />
					<div id='message-login'></div>
                    <a class="btn btn-primary" id="btn-login" data-loading-text="Iniciando...">Iniciar Sesi&oacute;n</a>
					<div>
					<br/><a href="<?php echo $auth_conf['base_url']?>auth/reset.php"><span class="label label-warning">Olvid&eacute; mi pasword</span></a>
					<a href="<?php echo $auth_conf['base_url']?>auth/activar.php"><span class="label label-warning">Quiero activar mi cuenta con un c&oacute;digo</span></a>
					</div>
					
                </form>

        </div>

        <div class="span4">
            <div>


              <a href="#" id="btnFacebook" class="btn btn-large btn-block btn-inverse" data-loading-text="Iniciando Sesi&oacute;n..."><i class="icon-facebook icon-large"></i> Entrar con Facebook</a>
				<p></p>
			</div>
			
			<div>
                <button id="btnRegistro" class="btn btn-success" >Registrarse</button>
                <p></p>
			</div>
  			
        </div>

    </div>

</section>



<!-- Footer
 ================================================== -->
<hr>

<footer id="footer">
    Desarrollado en el  <a target="_blank" href="http://cbtis72.edu.mx">Centro de Bachillerato Tecnol&oacutegico industrial y de servicios 72 "Andr&eacutes Quintana Roo"</a>.
</footer>

</div><!-- /container -->


<!-- /modal -->

<div id="modalRegistro" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Registrarse</h3>
	</div>
	<div class="modal-body">
	   <form class="form-horizontal" id="formRegistro">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="email">E-mail</label>
					<div class="controls">
						<input type="text" id="email" name="email" placeholder="" class="input-xlarge validate[required,custom[email]]">
					</div>
				</div>
		
				<div class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls">
						<input type="password" id="password" name="password" placeholder="" class="input-xlarge validate[required,minSize[6]] ">
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="password_confirm">Password (Confirmaci&oacuten)</label>
					<div class="controls">
						<input type="password" id="password_confirm" name="password_confirm" placeholder="" class="input-xlarge  validate[required,equals[password]]">
					</div>
				</div>
			</fieldset>
		</form>
		<div id="errorRegistro"></div>
	</div>
	
	<div class="modal-footer">
		<a data-dismiss="modal" class="btn">Cancelar</a>
		<a class="btn btn-primary" id="submitRegistro" data-loading-text="Registrando">Registrarse</a>
	</div>
</div> 



<!-- /modal -->
<div id="ajax-modal" class="modal hide fade" tabindex="-1"></div>

<!-- Javascript
================================================== -->


<script src="js/jquery.crypt.js"></script>
<script src="js/jquery.validationEngine-es.js"></script>
<script src="js/jquery.validationEngine.js"></script>
<script src="js/bootstrap-alert.js"></script>
<script src="js/bootstrap-button.js"></script>
<script src="js/bootstrap-modalmanager.js"></script>
<script src="js/bootstrap-modal.js"></script>



<script>
$(document).ready(function() {
	$("#formRegistro").validationEngine('attach');
	$("#form-login").validationEngine('attach');
	
	$("#btnRegistro").click(function(){
		$("#modalRegistro").modal("show");
	});
	
	$("#submitRegistro").click(function(){
		if($("#formRegistro").validationEngine("validate"))
		{
	
			var email=$("#email").val();
			var password = $().crypt({method:"sha1",source:$().crypt({method:"sha1",source:$("#password").val()})});
	
			$("#submitRegistro").button('loading');
			$.post("auth/registro.php",{email:email,password:password},function(data){
				if(data['error'] == 1)
				{
					$("#errorRegistro").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				else if(data['error'] == 0)
				{
					$("#errorRegistro").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				$("#submitRegistro").button('reset');
			},"json");
		}
	});
	
	$("#modalRegistro").on("hidden", function () {
		$("#formRegistro").each (function(){
			this.reset();
		});
		$("#errorRegistro").empty();
		$("#submitRegistro").button('reset');
		$('#formRegistro').validationEngine('hide');
  });

	
	$("#btn-login").click(function(event){
		if($("#form-login").validationEngine("validate"))
		{
			$("#btn-login").button("loading");
			$("#password-sha1-login").val($().crypt({method:"sha1",source:$().crypt({method:"sha1",source:$("#password-login").val()})}));
			var valores=$("#form-login").serializeArray();
			$.post("auth/login.php",valores,function(data){
				if(data['error'] == 1)
				{
					$("#message-login").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
				}
				else if(data['error'] == 0)
				{
					$.cookie("auth_session", data['session_hash'], { expires: 30 });
					$("#message-login").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
          location.href = "home/index.php";
				}
				$("#btn-login").button("reset");
			},"json");
		}
	});	
  
  $("#btnFacebook").click(function(){
    $("#btnFacebook").button("loading");
    var loginUrl=$("#loginUrl").val();
    document.location.href=loginUrl;
  })
	
	
	
});
</script>

</body>
</html>
