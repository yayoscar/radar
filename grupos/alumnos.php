<?php
include("../class/auth.class.php");
include("../class/class.grupo.php");
require_once("../class/class.inputfilter.php");

$auth=new Auth;
$hash = $_COOKIE['auth_session'];
$uid = $auth->getUid($hash);
$ifilter = new InputFilter();
$_REQUEST = $ifilter->process($_REQUEST);
$idgrupo=$_REQUEST["idgrupo"];
$idgrupo = explode("o", $idgrupo);
$idgrupo=$idgrupo[1];
$rol=$auth->getRol($uid);

$auth = new Auth;
$grupo= new Grupo();

if(!isset($_COOKIE['auth_session']))
{
  header('Location: ../index.php');
}
$hash = $_COOKIE['auth_session'];
if(!$auth->checkSession($hash))
{
		header('Location: ../index.php');
}

$uid = $auth->getUid($hash);
$email=$auth->getEmail($uid);

$group=$grupo->getGrupo($idgrupo);
$plantel=$grupo->getPlantel($idgrupo);
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
     <link href="../css/theme.bootstrap.css" rel="stylesheet"/>
     
     <link href="../css/jquery.fileupload-ui.css" rel="stylesheet">
    
	
	

</head>

<body>

  
<!-- Navbar
  ================================================== -->
<?php $auth->menu($email,$rol,"grupos"); ?>

<div class="container">
  <div class="row">
    <div class="span12">
      <ul class="breadcrumb">
        <li>
          <a href="../grupos/index.php?idplantel=plantel<?php echo $plantel["id"]?>"><?php echo $plantel["plantel"]." ".$plantel["numero"];?></a>
          <span class="divider">/</span></li>
        <li>Grupo
          <span class="divider">/</span></li>
        <li class="active"><?php echo $group["generacion"]." ".$group["especialidad"]." ".$group["grupo"]." ".$group["turno"]?></li>
       </ul>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      
      <div class="btn-toolbar pull-right">
        <button class="btn btn-primary" id="btnModalAgregarAlumno"><i class="icon2-white icon2-plus"></i> Alumno</button>
      </div>
      
      
      
     <table class="table table-condensed table-hover" id="tblAlumnos">
        <thead>
          <tr>
            <th class="span1 filter-false"></th>
            <th class="span2" data-placeholder="Filtrar por N&uacute;mero de Control">Num Con</th>
            <th class="span5" data-placeholder="Filtrar por Nombre">Nombre</th>
            <th class="span4" data-placeholder="Filtrar por CURP">CURP</th>
            <th class="span1 filter-false"></th>
          </tr>
        </thead>
        <tbody>
          
          
          

        </tbody>
     </table>
      <table>
       <tr id='moldeAlumno' class="hidden">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td align="center">
                <i class='icon2-user' rel="tooltip" title="Ver Alumnos">&nbsp;</i>
                <i class='icon2-trash' rel="tooltip" title="Eliminar Grupo"></i>
            </td>
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

<div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Eliminar Alumno</h3>
    </div>
    <div class="modal-body">
      <p class="error-text">Se eliminar&aacute; toda la informaci&oacute;n relacionada a este Alumno</p>
       <p class="error-text">¿Est&aacute; seguro de eliminarlo?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-danger" data-dismiss="modal">Eliminar</button>
    </div>
</div>


<div id="modalAgregarAlumno" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Alumno Nuevo</h3>
	</div>
	<div class="modal-body">
	   <form class="form-horizontal" id="formAgregarAlumno">
			<input type="hidden" id="grupo" name="grupo" value="<?php echo $idgrupo?>">
      
      
       <fieldset>
         <div class="control-group">
           <label class="control-label" for="numcontrol">N&uacute;mero de Control</label>
					<div class="controls">
						<input type="text" id="numcontrol" name="numcontrol" class="input-large validate[required] ">
					</div>
				</div>
         
				<div class="control-group">
					<label class="control-label" for="nombre">Nombre</label>
					<div class="controls">
						<input type="text" id="nombre" name="nombre" class="input-xlarge validate[required] ">
					</div>
				</div>
         
         <div class="control-group">
					<label class="control-label" for="apepat">Apellido Paterno</label>
					<div class="controls">
						<input type="text" id="apepat" name="apepat" class="input-xlarge validate[required] ">
					</div>
				</div>
         
         <div class="control-group">
					<label class="control-label" for="apemat">Apellido Materno</label>
					<div class="controls">
						<input type="text" id="apemat" name="apemat" class="input-xlarge">
					</div>
				</div>
		
				<div class="control-group">
					<label class="control-label" for="curp">CURP</label>
					<div class="controls">
						<input type="text" id="curp" name="curp" class="input-large">
					</div>
				</div>
      <input type="hidden" id="idplantel" name="idplantel" value="<?php echo $plantel["id"]?>">
      <input type="hidden" id="plantel" name="plantel" value="<?php echo $plantel["plantel"]." ".$plantel["numero"];?>">
      <input type="hidden" id="uid" name="uid" value="<?php echo $uid?>">
		</fieldset>
		</form>
		<div id="msgAgregarAlumno"></div>
	</div>
	
	<div class="modal-footer">
    <div class="btn-group pull-left">
      <button class="btn btn-mini btn-success" id="btnPlantilla"><i class="icon2-file-alt icon2-white"></i> Plantilla</button>
      <span class="btn btn-mini btn-success fileinput-button">
          <i class="icon2-upload-alt icon2-white"></i>
          <span>Carga Masiva</span>
          <input id="fileupload" type="file" name="files[]" data-url="server/php/">
      </span>
      
 

    </div>
    <p class="text-error pull-left hide" id="errorUpload"><strong>Error!</strong> Archivo Invalido </p>
    
    
		<a data-dismiss="modal" class="btn">Cancelar</a>
		<a class="btn btn-primary" id="btnAgregarAlumno" data-loading-text="Agregando... Esto tardar&aacute; algunos segundos">Agregar</a>
	</div>
</div> 


<div id="modalCargaMasiva" data-backdrop="static" class="modal hide fade" tabindex="-1">
   <div class="modal-body">
    <span id="estado"><strong>Subiendo archivo</strong></span><span class="pull-right" id="percent">30%</span>
    <div id="progress" class="progress progress-striped active"> 
      <div class="bar"></div>
    </div>
    
    <div id="errorCargaMasiva"></div>
	</div>
    
    
	
	<div class="modal-footer">
    
		<button id="btnCargaMasiva" class="btn hide">Aceptar</button>
		
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
<script src="../js/bootstrap-dropdown.js"></script>
<script src="../js/bootstrap-modal.js"></script>
<script src="../js/bootstrap-modalmanager.js"></script>
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/SetCase.js"></script>
<script src="../js/jquery.tablesorter.js"></script>
<script src="../js/jquery.tablesorter.widgets.js"></script>
<script src="../js/jquery.ui.widget.js"></script>
<script src="../js/jquery.iframe-transport.js"></script>
<script src="../js/jquery.fileupload.js"></script>


<script>
  $(function() {
   
   
   $('#fileupload').fileupload({
        dataType: 'json',
        add: function (e, data) {
          var hash = {
            'application/vnd.ms-excel'  : 1,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 1,
          };
          $.each(data.files, function (index, file) {
               if (hash[file.type]) {
                 $("#modalAgregarAlumno").modal("hide");
                  formReset();
                  $("#modalCargaMasiva").modal("show");
                  data.submit()
               }
               else
               {
                 $("#errorUpload").show();
                 setTimeout(function() 
                 {
                   $("#errorUpload").hide();
                 }, 2500);
               }
           });
        },
        progressall: function (e, data) {
          var progress = parseInt(data.loaded / data.total * 100, 10);
          $('#progress .bar').css(
              'width',
              progress + '%'
          );
          $("#percent").html(progress + '%');
        },
        done: function (e, data) {
            $("#estado").html("<strong>Agregando Alumnos</strong>");
             $("#percent").html("0/0");
            var ciclo=setInterval(function() {
              var file_json='progresoalumnos'+$("#uid").val()+'.json';
              $.getJSON(file_json, function(data) {
                var progress = parseInt(data.item / data.max * 100, 10);
                $("#percent").html(progress+"%");
                $('#progress .bar').css('width',progress + '%');
                
               // if(data.error.lenght>0)
                  $("#errorCargaMasiva").html(data.error);
                if(data.item==data.max)
                {
                   clearInterval(ciclo);
                   $.post("elimina_progreso.php",{archivo:file_json});
                   var refresh=true;
                   //$("#infoCargaMasiva").show();
                   if(data.correcto==0){
                    $("#percent").addClass("label label-important")
                    $('#progress').removeClass("active").addClass("progress-danger");
                    refresh=false;
                   }else if(data.correcto==data.max) {
                    $('#progress').removeClass("active").addClass("progress-success");
                    $("#percent").addClass("label label-success")
                   }else {
                    $('#progress').removeClass("active").addClass("progress-warning");
                    $("#percent").addClass("label label-warning")
                   }
                   $("#percent").html("Se agregaron "+data.correcto+" Alumnos de "+data.max);
                   $("#btnCargaMasiva").show();
                   if(refresh){
                      fillTabla();
                    }
                }
               
              });
            }, 300);
            
            $.each(data.result.files, function (index, file) {
                $('#progress .bar').css('width','0%');
                $.post("carga_masiva_alumnos.php",{archivo:file.name,id_plantel:$("#idplantel").val()},function(data){
                  
                 /* 
                  if(data.code==0){
                    $("#errorCargaMasiva").html("<div class='alert alert-error'>"+data.error+"</div>");
                    $("#btnCargaMasiva").show();
                  }*/
                },"json");
                
                //$('<p/>').text(file.name).appendTo(document.body);
            });
            
            
        }
       
      });
      
  $("#btnCargaMasiva").click(function(){
    $("#modalCargaMasiva").modal("hide");
    $("#errorCargaMasiva").empty();
    $("#btnCargaMasiva").hide();
    $('#progress').removeClass().addClass("progress progress-striped active");
   });
      
  $("#btnPlantilla").click(function(){
    location.href='plantilla_alumnos.php?id_plantel='+$("#idplantel").val()+'&plantel='+$("#plantel").val();
  });
   
   $("#formAgregarAlumno").validationEngine();
   $("#nombre").Setcase({caseValue : 'upper'});
   $("#apepat").Setcase({caseValue : 'upper'});
   $("#apemat").Setcase({caseValue : 'upper'});
   $("#curp").Setcase({caseValue : 'upper'});
   
    fillTabla();
    
    $("#btnModalAgregarAlumno").click(function(){
      $("#modalAgregarAlumno").modal("show");
    });
    
    $("#btnModalAgregarMasiva").click(function(){
      $("#modalAgregarMasiva").modal("show");
    });

   function addRow(alumno)
   {
      
        $("#moldeAlumno").clone().attr("id","alumno"+alumno['id']).appendTo("#tblAlumnos > tbody:last");
        $("#alumno"+alumno['id']).removeClass("hidden");
        $("#alumno"+alumno['id']+" > td:nth-child(2)").html(alumno['num_con']);
        $("#alumno"+alumno['id']+" > td:nth-child(3)").html(alumno['nombre']);
        $("#alumno"+alumno['id']+" > td:nth-child(4)").html(alumno['curp']);
        
      
      
           
   }
   
   function fillTabla()
   {
    $.post("lista_alumnos.php",{idgrupo:$("#grupo").val()},function(aux)
    {
      $.each(aux,function(){
        addRow(this);
      });
      
      
      $("#tblAlumnos").tablesorter({
        theme : "bootstrap",// this will 
        headers: {
            0: { sorter: false },
            4: {sorter:false}
        },
        sortList: [[2,0]],
        sortAppend: [[0,1]],
        widthFixed: true,
        headerTemplate : '{content} {icon}', 
        widgets : [ "uitheme","filter" ],
        widgetoptions:{
          filter_columnFilters : true,
          filter_cssFilter : 'tablesorter-filter',
          filter_functions : {0:true},
          filter_hideFilters : true,
          filter_ignoreCase : true,
          filter_startsWith : false,
          filter_useParsedData : false
        }
      });
    },"json");
    
   }
   
   function formReset()
    {
      $("#formAgregarAlumno").each (function(){
        this.reset();
      });
      $("#msgAgregarAlumno").empty();
      $("#btnAgregarAlumno").button('reset');
      $('#formAgregarAlumno').validationEngine('hide');
    }

    $("#modalAgregarAlumno").on("hidden", function () {
      formReset();
    });

    $("#btnAgregarAlumno").click(function(){
      if($("#formAgregarAlumno").validationEngine("validate"))
      {
        $("#btnAgregarGrupo").button('loading');
        //$(this).toggleClass('loading');
        var valores=$("#formAgregarAlumno").serializeArray();
        $.post("agregar_alumno.php",valores,function(data){
          if(data['error'] == 1)
          {
            $("#msgAgregarAlumno").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
           }
          else if(data['error'] == 0)
          {
            $("#msgAgregarAlumno").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
            
            addRow(data["alumno"]);
            var resort = true;
            $("#tblAlumnos").trigger("update", [resort]);
            
            setTimeout(function() 
            {
              $('#modalAgregarAlumno').modal('hide')
              formReset();
              
              
            }, 2000);
          }
          $("#btnAgregarAlumno").button('reset');

        },"json");
      }
    });
    
    $("#tblAlumnos").on("click",".icon-pencil",function()
    {
      var id = $(this).closest("tr").attr("id");
      location.href="altaAlumnos.php?idgrupo="+id;
    });
    
  });
</script>

</body>
</html>
