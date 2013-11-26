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
    
	
	

</head>

<body>

  
<!-- Navbar
  ================================================== -->
<?php $radar->menu($email,$rol,"encuestas"); ?>

<div class="container">
  <input type="hidden" name="idencuesta" id="idencuesta" />
  <div class="row">
    <div class="span12">
      <ul class="breadcrumb">
        <li class="active">Encuestas</li>
       </ul>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      
      <div class="btn-toolbar">
        <button class="btn btn-primary" id="btnModalAgregarGrupo"><i class="icon-white icon-plus"></i> Grupo</button>
      </div>
      
     <table class="table table-condensed table-hover" id="tblEncuestas">
        <thead>
          <tr>
            <th class="span1"></th>
            <th class="span9">Encuesta</th>
            <th class="span2">Acciones</th>
            
          </tr>
        </thead>
        <tbody>
          <tr id="moldeEncuesta" class="hidden">
            <td class="span1"></td>
            <td class="span9">Encuesta</td>
            <td class="span2">
              <i class='icon2-edit' rel="tooltip" title="Editar"></i>
              <i class='icon2-pushpin' rel="tooltip" title="Aplicar a Generaci&oacute;n"></i>
              <i class='icon2-bar-chart' rel="tooltip" title="Reporte"></i>
              <i class='icon2-save' rel="tooltip" title="Exportar"></i>
            </td>
          </tr>

        </tbody>
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


<div id="modalAsignar" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="tituloAsignar">Asignar Encuesta</h3>
	</div>
	<div class="modal-body">
	   <form class="form-horizontal" id="formAsignar">
			 <fieldset>
          <div class="control-group">
					<label class="control-label" for="generacion">Generaci&oacute;n</label>
					<div class="controls">
						<select id="generacion" name="generacion">
              
            </select>
					</div>
				</div>
        
        
			</fieldset>
		</form>
		<div id="msgAsignar"></div>
	</div>
	
	<div class="modal-footer" id="footerAsignar">
		<a data-dismiss="modal" class="btn">Cancelar</a>
		<a class="btn btn-primary" id="btnAsignar" data-loading-text="Asignando... Esto tardar&aacute; algunos minutos">Agregar</a>
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



<script>
  $(function() {
   function fillTabla()
   {
    $.post("lista_encuestas.php",function(encuesta)
    {
      $.each(encuesta,function(){
        $("#moldeEncuesta").clone().attr("id","enc"+this.id).appendTo("#tblEncuestas > tbody:last");
        $("#enc"+this.id).removeClass("hidden");
        $("#enc"+this.id+" > td:nth-child(2)").html(this.nombre);
      });
      $("[rel=tooltip]").tooltip();
    },"json");
    
   }
   
   $("#tblEncuestas").on("click",".icon2-pushpin",function()
  {
    var id = $(this).closest("tr").attr("id");
    $("#idencuesta").val(id);
    $("#tituloAsignar").html("Asignar Encuesta");
    $("#btnAsignar").removeClass("reporte");
    $("#btnAsignar").addClass("asignar");
    $("#modalAsignar").modal("show");
  });
  
   $("#tblEncuestas").on("click",".icon2-save",function()
  {
    var id = $(this).closest("tr").attr("id");
    $("#idencuesta").val(id);
    $("#tituloAsignar").html("Reporte");
    $("#btnAsignar").removeClass("asignar");
    $("#btnAsignar").addClass("reporte");
    $("#modalAsignar").modal("show");
  });
  
  $("#tblEncuestas").on("click",".icon2-bar-chart",function()
  {
    var id = $(this).closest("tr").attr("id");
    $("#idencuesta").val(id);
    location.href="../encuestas/reporte.php?idencuesta="+id;
  });
  
  function fillCombo(select)
    {
      aux="#"+select;
      eval("$combo"+select+"=$(aux)");
      $.post("cambiar_combo.php",{combo:select},function(data)
      {
        eval("$combo"+select+".empty()");
        options='';
        $.each(data, function (){
          var $option = $("<option/>").attr("value", this.id).html(this.texto);
          //if( this.selected == true ) $option.attr("selected", "selected");
         eval("$option.appendTo($combo"+select+")");
        });	
      },"json");
    }
    
    $("body").on("click",".asignar",function(){
    //$("#btnAsignar").click(function(){
      $("#msgAsignar").html("");
      $("#btnAsignar").button('loading');
      var valores=$("#formAsignar").serializeArray();
      var idencuesta=$("#idencuesta").val();
      valores.push({ name: "idencuesta", value: idencuesta });
      $.post("asignar_encuestas.php",valores,function(data){
        if(data['code']==2)
        {
          $("#msgAsignar").html("<div class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>&times;</button><p>Las encuestas se asignaron con &eacute;xito</p></div>");
        }
        else{
          $("#msgAsignar").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button><p>La encuesta ya ha sido asignada previamente a esta generaci&oacute;n</p></div>");
        }
        $("#btnAsignar").button('reset');
        
        $("#msgAsignar").empty;
        //$("#modalAsignar").modal("hide");
        
      },"json");
      
    });
    
    $("body").on("click",".reporte",function(){
    //$("#btnAsignar").click(function(){
      $("#msgAsignar").html("");
      var idgeneracion=$("#generacion").val();
      var idencuesta=$("#idencuesta").val();
      location.href="reporte_excel.php?idencuesta="+idencuesta+"&idgeneracion="+idgeneracion;
      
    });
    
   fillTabla();
   fillCombo("generacion");
   
   
   /* 
   function addRow(grupo)
   {
      var aux;
      $.each(grupo, function ()
      {
        aux="<tr id='grupo"+this.id+"'>";
        aux=aux+"<td></td>";
        aux=aux+"<td>"+this.generacion+"</td>";
        aux=aux+"<td>"+this.especialidad+"</td>";
        aux=aux+"<td>"+this.grupo+"</td>";
        aux=aux+"<td>"+this.turno+"</td>";
        aux=aux+"<td><a href='#'><i class='icon-user'></i></a>";
        aux=aux+"<a href='#myModal' role='button' data-toggle='modal'><i class='icon-remove'></i></a></td>";
        aux=aux+"</tr>"
        $('#tblGrupos > tbody:last').append(aux);
     });
   }
   
   function fillTabla()
   {
    $.post("lista_grupos.php",{plantel:$("#plantel").val()},function(grupo)
    {
      addRow(grupo);
    },"json");
    
   }
    
    $("#formAgregarGrupo").validationEngine();

    fillTabla();
    fillCombo("especialidad");
    fillCombo("generacion");

    $("#btnModalAgregarGrupo").click(function(){
      $("#modalAgregarGrupo").modal("show");
    });

    function fillCombo(select)
    {
      aux="#"+select;
      eval("$combo"+select+"=$(aux)");
      $.post("cambiar_combo.php",{combo:select},function(data)
      {
        eval("$combo"+select+".empty()");
        options='';
        $.each(data, function (){
          var $option = $("<option/>").attr("value", this.id).html(this.texto);
          //if( this.selected == true ) $option.attr("selected", "selected");
         eval("$option.appendTo($combo"+select+")");
        });	
      },"json");
    }

    function formReset()
    {
      $("#formAgregarGrupo").each (function(){
        this.reset();
      });
      $("#errorAgregarGrupo").empty();
      $("#btnAgregarGrupo").button('reset');
      $('#formAgregarGrupo').validationEngine('hide');
    }

    $("#modalAgregarGrupo").on("hidden", function () {
      formReset();
    });

    
    
    //Click Alumnos
    $("#tblGrupos").on("click",".icon-user",function()
    {
      var id = $(this).closest("tr").attr("id");
      location.href="alumnos.php?idgrupo="+id;
    });
    */
  });
</script>

</body>
</html>
