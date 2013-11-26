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
$idencuesta = explode("encuesta", $idencuesta);
$idencuesta=$idencuesta[1];
//$idencuesta=2;

$encuesta=$radar->getEncuesta($idencuesta);

?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>radar|dgeti</title>
	<link rel="shortcut icon" href="img/favicon.png">
	<meta name="viewport" content="user-scalable=0,width=device-width,initial-scale=1,maximum-scale=1">
  <link rel="shortcut icon" href="../img/favicon.png">


    <link href="../css/bootstrap.css" rel="stylesheet"/>
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"/>
		<link href="../css/jquery-ui.css" rel="stylesheet"/>
    <link href="../css/font-awesome.css" rel="stylesheet"/>
		<link href="../css/validationEngine.jquery.css" rel="stylesheet"/> 
    <link href="../css/bootstrap-modal.css" rel="stylesheet"/>
    
	
	
	<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php $radar->menu($email,$rol,"inicio"); ?>



  <input type="hidden" id="uid" value="<?php echo $idalumno?>"/>
  <input type="hidden" id="idencuesta" value="<?php echo $idencuesta?>"/>
  <input type="hidden" id="idalumno" value="<?php echo $idalumno?>"/>
  <input type="hidden" id="max"/>

	<div id="listaPreguntas">
	
  </div>
  
  <section class="row hidden" id="moldePregunta">
		
    <article class="span11 offset1">
      
			<h3>Pregunta 1</h3>
      
			<div class="well">
        
        
      </div>
      
      <div class="btnNext"></div>
      
		</article>
       
	</section>
  
  <div id="finalBox" class="hidden center">
    <section>
    <article>
    <div class="hero-unit center">
    <h1 class="center">Gracias por contestar.</h1>
    <br />
    <p>Desde <b>radar|dgeti</b> queremos agradecerle el tiempo dedicado a responder nuestra encuesta de seguimiento de egresados. Gracias a sus respuestas ya estamos trabajando en mejorar aquellos aspectos menos positivos y esperamos que pronto se noten los resultados.</p>
    
    <a href="../home" class="btn btn-large btn-primary"><i class="icon-home icon-white"></i> Ir a Inicio</a>
  </div>
    </article>
    </section>
  </div>

<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/jquery.validationEngine-es.js"></script>
<script src="../js/jquery.validationEngine.js"></script>
<script src="../js/jquery.ascensor.js"></script>
<script src="../js/bootstrap-alert.js"></script>
<script src="../js/bootstrap-button.js"></script>
<script src="../js/bootstrap-dropdown.js"></script>
<script src="../js/bootstrap-modal.js"></script>
<script src="../js/bootstrap-modalmanager.js"></script>
<script src="../js/jquery.easing.min.js"></script>

<script>
$(function() {
  function cargarEncuesta(){
    var idencuesta=$("#idencuesta").val();
    var idalumno=$("#idalumno").val();
    $.post("cargar_encuesta.php",{idencuesta:idencuesta,idalumno:idalumno},function(data){
      
      $("#max").val(data["preguntas"].length);
      $.each(data["preguntas"],function(){
        addPregunta(this);
      });
      
      var estado;
      if(data["estado"]){
        estado=data["estado"];
        if(estado==0){
          location.href="../home";
        }
      } else {
        
          estado=1;
       
      }
      
      $('#listaPreguntas').append($("#finalBox").html());
      
      $('#listaPreguntas').ascensor({
        AscensorName: 'ascensor',
        ChildType: 'section',
        //AscensorFloorName: floors,
        Time: 1000,
        WindowsOn: estado,
        /*Direction: "chocolate",
        AscensorMap: '2|1 & 2|2 & 3|2 & 3|3 & 3|4 & 2|4 & 1|4',*/
        Easing: 'easeInOutQuad',
        KeyNavigation: false,
        Queued: false,
        QueuedDirection: "y",
        Overflow:"hidden"
      });
      
      
   },"json");
   
   
  }
  
  var destinos=new Array();
  
  
  function addPregunta(pregunta)
   {
        
        var aux="<form>";
        
        $("#moldePregunta").clone().attr("id","pregunta"+pregunta['orden']).appendTo("#listaPreguntas");
        $("#pregunta"+pregunta['orden']).removeClass("hidden");
        $("#pregunta"+pregunta['orden']+" > article > h3").html(pregunta['pregunta']);
        //Tipo Comentario
        if(pregunta["tipo"]==2){
          aux=aux+"<textarea id='texto"+pregunta['orden']+"' name='respuesta' class='span9' placeholder='Introducir texto' rows='5'></textarea>";
          aux=aux+"<input type='hidden' name='idpregunta' value='"+pregunta['id']+"'>"
          aux=aux+"</form>";
          $("#pregunta"+pregunta['orden']+" > article > .well").html(aux);
      }
        //opcion multiple 
        if(pregunta["tipo"]==3){
          $.each(pregunta["opcion"],function(){
            aux=aux+"<label class='radio'>";
            aux=aux+"<input type='radio' id='opcion"+pregunta['orden']+"' name='respuesta' value='"+this.id+"' class='radio"+pregunta['orden']+"'>";
            aux=aux+this.opcion;
            aux=aux+"</label>";
         });
         aux=aux+"<input type='hidden' name='idpregunta' value='"+pregunta['id']+"'>"
         aux=aux+"</form>";
         $("#pregunta"+pregunta['orden']+" > article > .well").html(aux);
        }
         
        //MultiOpcion
        if(pregunta["tipo"]==4) {
          $.each(pregunta["opcion"],function(){
            aux=aux+"<label class='checkbox'>";
            aux=aux+"<input type='checkbox' name='respuesta[]' id='check"+pregunta['orden']+"' value='"+this.id+"'>"+this.opcion;
            aux=aux+"</label>";
          });
          aux=aux+"<input type='hidden' name='idpregunta' value='"+pregunta['id']+"'>"
          aux=aux+"</form>";
          $("#pregunta"+pregunta['orden']+" > article > .well").html(aux);
       }
       destinos.push(pregunta["destino"]);
   }
  
  $("body").on("focus","textarea",function(){
    
      //pregunta actual
      var opcion=this.id
      var aux=opcion.split('texto');
      var current=aux[1];
            
      var aux=parseInt(current)-1;
      var sigue=parseInt(current)+1;
      
      $("#ascensorFloor"+current+" > article > .btnNext").html("<button class='ascensorLink ascensorLink"+sigue+" btn btn-success'>Continuar</button>");
   
 });
 
 $("body").on("change","input[type=checkbox]",function(){
    
      //pregunta actual
      var opcion=this.id
      var aux=opcion.split('check');
      var current=aux[1];
            
      var aux=parseInt(current)-1;
      var sigue=parseInt(current)+1;
      
      
      $("#ascensorFloor"+current+" > article > .btnNext").html("<button class='ascensorLink ascensorLink"+sigue+" btn btn-success'>Continuar</button>");
   
 });
  
  $("body").on("change","input[type=radio]",function(){
    
      //pregunta actual
      var $padre=$(this).parent()
      var opcion=this.id
      var aux=opcion.split('opcion');
      var current=aux[1];
      
      //opcion seleccionada
      var opcion=$padre.index();
   
      opcion++;
     
      var aux=parseInt(current)-1;
      var sigue=parseInt(current)+1;
      
      $("#ascensorFloor"+current+" > article > .btnNext").html("<button class='ascensorLink ascensorLink"+sigue+" btn btn-success'>Continuar</button>");
      
      
      var obj = $.parseJSON(destinos[aux]);
      
      //si tiene condicionales 
      if(obj){
        $.each(obj["ds"],function(){
          var ban
          var con=0;
          var preg=0;
          $.each(this.ps,function(){
            var opcion=$padre.index();
            opcion++;
            ban=$.inArray(opcion,this.o);
            if(ban!=-1){
              con++;
            }
            preg++;
          });
          if(con==preg){
            var d=this.d
            $("#ascensorFloor"+current+" > article > .btnNext").html("<button class='ascensorLink ascensorLink"+d+" btn btn-success'>Continuar</button>");
          }

        });
      }
      
    });
   
   $("body").on("click",".ascensorLink",function(){
      
      var next=($(this).prop("class"));
      next=next.split(" ");
      next=next[1];
      next=next.split("ascensorLink");
      next=next[1];
      
      var max=$("#max").val();
     
     $form = $(this).parent().parent().find('form');
      var valores = $form.serializeArray();
      
     
      
      valores.push({name:'next',value:next});
      valores.push({name:'max',value:max});
      valores.push({name:'idalumno',value:$("#idalumno").val()});
      valores.push({name:'idencuesta',value:$("#idencuesta").val()});
      $.post("enviar_encuesta.php",valores,function(data){
        
      });
      
    });
	
    cargarEncuesta();
	
});

</script>




</body>
</html>
