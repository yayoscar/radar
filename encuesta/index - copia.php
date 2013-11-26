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

$idalumno=$radar->getIdAlumno($uid);
$idencuesta=$_REQUEST["idencuesta"];

$encuesta=$radar->getEncuesta($idencuesta);

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
        <legend class=""><?php echo $encuesta?></legend>
      </div>
      <form id="formEncuesta">
      <input type="hidden" id="uid" name="idalumno" value="<?php echo $idalumno?>"/>
      <input type="hidden" id="idencuesta" name="idencuesta" value="<?php echo $idencuesta?>"/>
      
      <div id="listaPreguntas">
      
       
      </div>
      </form>
  <div class="row">
    <div class="offset6 span2">
      <button class="btn btn-primary pull-right" id="btnEnviar">Enviar Respuestas</button>
    </div>
  </div>
      
    </div>
  </div>
  <hr>


<footer id="footer">
    Desarrollado en el  <a target="_blank" href="http://cbtis72.edu.mx">Centro de Bachillerato Tecnol&oacutegico industrial y de servicios 72 "Andr&eacutes Quintana Roo"</a>.
</footer>

</div><!-- /container -->


<div class="modal small hide fade" id="modalEncuesta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="msgEnviar">
      
    </div>
    <div class="modal-footer" id="msgBoton">
        
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
  function cargarEncuesta(){
    var idencuesta=<?php echo $idencuesta?>;
    $.post("cargar_encuesta.php",{idencuesta:idencuesta},function(data){
      $.each(data,function(){
        var aux="";
        aux=aux+"<div class='row'><div class='span8'><div class='row'><div class='span8'>"
        idpregunta=this.id;
        aux=aux+"<h4><strong>"+this.pregunta+"</strong></h4>";
        aux=aux+"</div></div><div class='row'><div class='offset2 span6'>";
        $.each(this.opcion,function(){
          aux=aux+"<label class='radio'>";
          aux=aux+"<input type='radio' name='opcion"+idpregunta+"' id='opcion"+idpregunta+"' value='"+idpregunta+"|"+this.id+"'>";
          aux=aux+this.opcion;
          aux=aux+"</label>";
         });
         aux=aux+"</div></div></div></div><hr>";
         $("#listaPreguntas").append(aux);
      });
   },"json");
  }
  
  $("#btnEnviar").click(function(){
    //$("#btnEnviar").button('loading');
    var valores=$("#formEncuesta").serializeArray();
    $.post("enviar_encuesta.php",valores,function(data){
      if(data["code"]==1){
        $("#msgEnviar").html("<h4>La Encuesta se env&iacute;o correctamente</h4>");
        $("#msgBoton").html("<button class='btn btn-success' id='btnOk' data-dismiss='modal' aria-hidden='true'>Aceptar</button>");
        $("#modalEncuesta").modal("show");
      }else if(data["code"]==0){
        $("#msgEnviar").html("<h4>Hubo un error al enviar la información</h4>");
        $("#msgBoton").html("<button class='btn btn-danger' data-dismiss='modal' aria-hidden='true'>Aceptar</button>");
        $("#modalEncuesta").modal("show");
      }
    },"json");
    
  });
  
  $("#msgBoton").on("click","#btnOk",function(){
    location.href="../home/index.php";
  });
  
  cargarEncuesta();
});
</script>

</body>
</html>
