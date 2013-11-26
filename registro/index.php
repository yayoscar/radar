<?php
include("../class/class.radar.php");
include("../class/config.php");

$radar = new Radar;

if(!isset($_COOKIE['auth_session']))
{
  header('Location: ../index.php');
}
$hash = $_COOKIE['auth_session'];
if(!$radar->checkSession($hash))
{
	header('Location: ../index.php');
}
$uid = $radar->getUid($hash);
$email=$radar->getEmail($uid);
$rol=$radar->getRol($uid);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>radar|dgeti</title>
    <link rel="shortcut icon" href="img/favicon.png">


    <link href="../css/bootstrap.css" rel="stylesheet"/>
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"/>
		<link href="../css/jquery-ui.css" rel="stylesheet"/>
    <link href="../css/font-awesome.css" rel="stylesheet"/>
		<link href="../css/validationEngine.jquery.css" rel="stylesheet"/> 
    <link href="../css/bootstrap-modal.css" rel="stylesheet"/>
   
	
	

</head>

<body>

  
<!-- Navbar
  ================================================== -->
<?php $radar->menu($email,$rol,"inicio"); ?>

<div class="container">
  <div class="row">
    <div class="span12">
      
     
      <div id="legend">
        <legend class="">Completar Registro</legend>
      </div>
      <input type="hidden" id="idalumno" />
      <input type="hidden" id="uid" value="<?php echo $uid?>" />
      <form class="form-inline" name="formCodigo">
      <div class='alert alert-info' id="boxCodigo">
         <strong>Cuento con un c&oacute;digo que me proporcionaron en el plantel </strong>
         <div class="controls input-append">
          <input type="text" id="codigo" name="codigo" placeholder="C&oacute;digo de Activaci&oacute;n" class="input-medium">
          <a href="#" type="button" class="btn btn-primary" id="btnCodigo" data-loading-text="Buscando...">Utilizar</a>
        
          </div>
         
         
      </div>
       </form>
      
          
        
     
      
      <div id="datos" class="hide">
      <form class="form-horizontal" id="formDatos">
        
        <fieldset>
         
          
          
          <div class="control-group">
            <label class="control-label" for="telfijo">Tel&eacute;fono Fijo</label>
            <div class="controls">
              <input type="text" id="telfijo" name="telfijo" class="validate[custom[phone]] input-xlarge">
              <p class="help-block"></p>
            </div>
          </div>

          <div class="control-group">
            <!-- E-mail -->
            <label class="control-label" for="telmovil">Tel&eacute;fono M&oacute;vil</label>
            <div class="controls">
              <input type="text" id="telmovil" name="telmovil" class="validate[custom[phone]] input-xlarge">
              <p class="help-block"></p>
            </div>
          </div>
          
          <div class="control-group">
            <!-- E-mail -->
            <label class="control-label" for="direccion">Direcci&oacute;n</label>
            <div class="controls">
              <textarea name="direccion" id="direccion" class="input-xlarge span5" rows="3"></textarea>
              <p class="help-block"></p>
            </div>
          </div>
          
           <div class="control-group">
            <label class="control-label" for="fecha_nac">Fecha de Nacimiento</label>
            <div class="controls">
              <input type="text" id="fecha_nac" name="fecha_nac" class="input-large">
              <p class="help-block"></p>
            </div>
          </div> 

          <div class="control-group">
            <!-- Button -->
            <div class="controls">
              <span id="btnGuardar" class="btn btn-success" data-loading-text="Guardando">Guardar</span>
            </div>
          </div>
        </fieldset>
      </form>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="span">
      <br />
    </div>
  </div>


<footer id="footer">
    Desarrollado en el  <a target="_blank" href="http://cbtis72.edu.mx">Centro de Bachillerato Tecnol&oacutegico industrial y de servicios 72 "Andr&eacutes Quintana Roo"</a>.
</footer>

</div><!-- /container -->


<div class="modal small hide fade" id="modalCodigo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="myModalLabel">Datos del Alumno</h3>
  </div>
  <div class="modal-body">
    <dl class="dl-horizontal">
      <dt>N&uacute;mero de Control</dt>
      <dd id="modal_num_con"></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>Nombre</dt>
      <dd id="modal_nombre"></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>Plantel</dt>
      <dd id="modal_plantel"></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>Genereaci&oacute;n</dt>
      <dd id="modal_generacion"></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>Grupo</dt>
      <dd id="modal_grupo"></dd>
    </dl>
  </div>
  
 
  <div class="modal-footer">
    <button class="btn btn-success" id="btnConfirmar" data-loading-text="Cargando...">Confirmar</button>
    <button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">No soy yo :(</button>
  </div>
</div>

<div class="modal small hide fade" id="modalCodigoError" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  
  <div class="modal-body">
    
    <p>El c&oacute;digo no fue encontrado en la Base de Datos, por favor intente de nuevo</p>
    
  </div>
  <div class="modal-footer">
    
    <button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Aceptar</button>
  </div>
   
  
</div>

<!-- Javascript
================================================== -->

<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/jquery.validationEngine-es.js"></script>
<script src="../js/jquery.validationEngine.js"></script>
<script src="../js/bootstrap-alert.js"></script>
<script src="../js/bootstrap-button.js"></script>
<script src="../js/bootstrap-dropdown.js"></script>
<script src="../js/bootstrap-modal.js"></script>
<script src="../js/bootstrap-modalmanager.js"></script>



<script>
$(function() {
  $("#formDatos").validationEngine('attach');
  
  $( "#fecha_nac" ).datepicker({
      changeMonth: true,
      changeYear: true,
      yearRange: "1980:2012"
    });
    
  $("#btnCodigo").click(function()
  {
    $("#btnCodigo").button('loading');
    var aux=$("#codigo").val();
    $.post("activar_codigo.php",{codigo:aux},function(data)
    {
      if(data["code"]==1)
      {
        $("#idalumno").val(data["info"]["idalumno"])
        $("#modal_num_con").html(data["info"]["num_con"]);
        $("#modal_nombre").html(data["info"]["nombre"]);
        $("#modal_plantel").html(data["info"]["plantel"]);
        $("#modal_generacion").html(data["info"]["generacion"]);
        $("#modal_grupo").html(data["info"]["grupo"]);
        $("#modalCodigo").modal("show");
      }
      else if (data["code"]==0)
      {
        $("#modalCodigoError").modal("show");
        
      }
      $("#btnCodigo").button('reset');
    },"json");
  });
  
  $("#modalAgregarAlumno").on("hidden", function () {
    $(".infoAlumno").empty();
  });
  
  $("#btnConfirmar").click(function(){
    $("#btnConfirmar").button('loading');
    var uid=$("#uid").val();
    var idalumno=$("#idalumno").val();
    $.post("confirmar.php",{idalumno:idalumno,uid:uid},function(data){
      $("#modalCodigo").modal("hide");
      $("#datos").show();
      $("#boxCodigo").hide();
    });
  });
  
  $("#btnGuardar").click(function(){
		if($("#formDatos").validationEngine("validate"))
		{
      var idalumno=$("#idalumno").val();
      $("#btnGuardar").button("loading");
      var valores=$("#formDatos").serializeArray();
      valores.push({"name":"idalumno","value":idalumno});
			$.post("guardar_datos.php",valores,function(data){
        $("#btnGuardar").button("reset");
        location.href = "../home/";
      },"json");
    }
  });  
  
});
</script>

</body>
</html>
