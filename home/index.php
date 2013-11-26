<?php
include("../class/class.radar.php");
include("../class/config.php");

$radar = new Radar;

if(!isset($_COOKIE['auth_session']))
{
  header('Location: index.php');
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

if(!$radar->isActivo($uid))
{
  header('Location: ../registro/');
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>radar|dgeti</title>
    <link rel="shortcut icon" href="../img/favicon.png">


    <link href="../css/bootstrap.css" rel="stylesheet"/>
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"/>
		<link href="../css/jquery-ui.css" rel="stylesheet"/>
    <!-- <link href="css/font-awesome.css" rel="stylesheet"/>
		 <link href="css/validationEngine.jquery.css" rel="stylesheet"/>  
    -->
	
	

</head>

<body>

  
<!-- Navbar
  ================================================== -->
<?php $radar->menu($email,$rol,"inicio"); ?>


<input type="hidden" id="idalumno" value="<?php echo $idalumno?>"/>
<div class="container">
  <div class="row">
    <div class="span12">
      <div id="legend">
        <legend class="">Solicitudes</legend>
      </div>
      <table class="table table-condensed table-hover" id="tblSolicitudes">
        <thead>
          <tr>
            <th class="span2"></th>
            <th class="span6"></th>
            <th class="span2"></th>
            <th class="span2"></th>
          </tr>
        </thead>
        <tbody>
         
         
        </tbody>
      </table>
      <table> 
        <tr id="moldeSolicitud" class="hidden">
            <td><span class="label label-important">Encuesta</span> </td>
            <td>Nombre de la Encuesta</td>
            <td>11:23 PM</td>
            <td><button class="btn btn-mini btn-primary span1">Responder</button></td>
        </tr>
      </table>
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

<form action="../encuesta" method="POST" id="sendEncuesta">
  <input type="hidden" name="idencuesta"/>
</form>



<!-- Javascript
================================================== -->

<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/jquery-ui.js"></script>

<script src="../js/jquery.crypt.js"></script>
<script src="../js/jquery.cookie.js"></script>
<script src="../js/jquery.validationEngine-es.js"></script>
<script src="../js/jquery.validationEngine.js"></script>
<script src="../js/bootstrap-alert.js"></script>
<script src="../js/bootstrap-button.js"></script>
<script src="../js/bootstrap-dropdown.js"></script>



<script>
   $(function() {
    
   function fillTabla()
   {
    $.post("lista_solicitudes.php",{idalumno:$("#idalumno").val()},function(aux)
    {
      $.each(aux,function(){
        addRow(this);
      });
    },"json");
   }
   
   function addRow(solicitud)
   {
      $("#moldeSolicitud").clone().attr("id","encuesta"+solicitud['idencuesta']).appendTo("#tblSolicitudes > tbody:last");
      $("#encuesta"+solicitud['idencuesta']).removeClass("hidden");
      $("#encuesta"+solicitud['idencuesta']+" > td:nth-child(2)").html(solicitud['encuesta']);
      $("#encuesta"+solicitud['idencuesta']+" > td:nth-child(3)").html(solicitud['ago']);
     
    }
    
    $("#tblSolicitudes").on("click",".btn",function()
    {
      //var idalumno=$("#idalumno").val();
      var idencuesta = $(this).closest("tr").attr("id");
      //$("#sendEncuesta input").val(idencuesta);
      location.href="../encuesta/index.php?idencuesta="+idencuesta;
      //$("#sendEncuesta").submit();
    });
    
    fillTabla();
    
    
  });
</script>

</body>
</html>
