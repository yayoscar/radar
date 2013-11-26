<?php
include("../class/class.radar.php");
//include("../class/config.php");

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

$idencuesta=$_REQUEST["idencuesta"];
$idencuesta = explode("enc", $idencuesta);
$idencuesta=$idencuesta[1];
//$idencuesta=3;
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
    <link href="../css/bootstrap-modal.css" rel="stylesheet"/>
    <link href="../css/font-awesome.css" rel="stylesheet"/>
    <link href="../css/validationEngine.jquery.css" rel="stylesheet"/>
    <link rel="stylesheet" href="styles.css">
	
	

</head>

<body>

  
<!-- Navbar
  ================================================== -->
<?php $radar->menu($email,$rol,"encuestas"); ?>

<div class="container">
  
  <div class="row">
    <div class="span12">
      <ul class="breadcrumb">
        <li>Encuestas<span class="divider">/</span></li> 
        <li class="active"><?php echo $radar->nombreEncuesta($idencuesta) ?></li>
       </ul>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <form class="form-inline" id="formCharts">
        <input type="hidden" name="idencuesta" id="idencuesta" value="<?php echo $idencuesta?>"/>
        <label for "idgeneracion">Generaci&oacute;n</label>
        <select id="idgeneracion" name="idgeneracion" class="input-medium"><select>
        <!-- <label for "idestado">Estado</label>
        <select id="idestado" name="idestado" class="input-medium"><select>
        <label for "idplantel">Plantel</label>
        <select id="idplantel" name="idplantel" class="input-medium"><select>
        <label for "turno">Turno</label>
        <select id="turno" name="turno" class="input-small"><select>
        <label for "idespecialidad">Especialidad</label>
        <select id="idespecialidad" name="idespecialidad" class="input-medium"><select>
          <label for "igrupo">Grupo</label>
        <select id="idespecialidad" name="idgrupo" class="input-medium"><select> -->
      </form>   
      <div class="hidden" id="divVacio">
    <h1 class="center">Gracias por contestar.</h1>
    <br />
    <p>Desde <b>radar|dgeti</b> queremos agradecerle el tiempo dedicado a responder nuestra encuesta de seguimiento de egresados. Gracias a sus respuestas ya estamos trabajando en mejorar aquellos aspectos menos positivos y esperamos que pronto se noten los resultados.</p>
    
    <a href="../home" class="btn btn-large btn-primary"><i class="icon-home icon-white"></i> Ir a Inicio</a>
  </div>
    </div>
  </div>
  
  <div id="Charts">
  <div class="row hidden" id="moldeChart">
    <h4>texto</h4>
    <div id="chart" class="span12" style="height: 400px;"></div>
  </div>
</div>
  <hr>

<footer id="footer">
    Desarrollado en el  <a target="_blank" href="http://cbtis72.edu.mx">Centro de Bachillerato Tecnol&oacutegico industrial y de servicios 72 "Andr&eacutes Quintana Roo"</a>.
</footer>

</div><!-- /container -->

<div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Eliminar Grupo</h3>
    </div>
    <div class="modal-body">
      <p class="error-text">Se eliminar&aacute; toda la informaci&oacute;n relacionada a este grupo</p>
       <p class="error-text">¿Est&aacute; seguro de eliminarlo?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-danger" data-dismiss="modal">Eliminar</button>
    </div>
</div>





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
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/bootstrap-dropdown.js"></script>
<script src="../js/bootstrap-modal.js"></script>
<script src="../js/bootstrap-modalmanager.js"></script>
<script src="../js/bootstrap-tab.js"></script>
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/amcharts.js"></script>



<script>
  $(function() {
     
  function fillCombo(select)
    {
      aux="#"+select;
      
      $.post("cambiar_combo.php",{combo:'generacion'},function(data)
      {
        aux.empty;
        options='';
        $.each(data, function (){
          var $option = $("<option/>").attr("value", this.id).html(this.texto);
          //if( this.selected == true ) $option.attr("selected", "selected");
         $option.appendTo(aux);
        });	
        cargarCharts();
      },"json");
    }
    
   
   fillCombo("idgeneracion");
   
    
   
  $("#idgeneracion").change(function(){
    $(".dynamyc").remove;
    cargarCharts();
  });
    
    function cargarCharts(){
      var valores=$("#formCharts").serializeArray();
      $.post("cargar_charts.php",valores,function(chart){
        if(chart['code']==1){
           $("#divVacio").show();
        }
        else {
          $.each(chart,function(){
            var chartData;
            var objchart = new AmCharts.AmPieChart();
            objchart.valueField = "value";
            objchart.titleField = "title";
            $("#moldeChart").clone().attr("id","chartRow"+this.id).appendTo("#Charts");
            $("#chartRow"+this.id).removeClass("hidden");
            $("#chartRow"+this.id).addClass("dynamic");
            $("#chartRow"+this.id+" > h4").html(this.pregunta);
            $("#chartRow"+this.id+" > div").attr("id","chart"+this.id);
            chartData=this.chart;
            objchart.dataProvider = chartData;
            objchart.write("chart"+this.id);
         });
        }
        
      },"json");
    }

    function drawCharts(){
      var chartData = [{title:"Pie I have eaten",value:70},{title:"Pie I haven\'t eaten",value:30}];			
      var chart = new AmCharts.AmPieChart();
      chart.valueField = "value";
      chart.titleField = "title";
      chart.dataProvider = chartData;
      chart.write("chart1");
    }
  });
</script>

</body>
</html>
