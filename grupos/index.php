<?php
include("../class/auth.class.php");
include("../class/class.grupo.php");

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
$rol=$auth->getRol($uid);

if($rol==1){
  $idplantel=$_REQUEST["idplantel"];
  $idplantel = explode("el", $idplantel);
  $idplantel=$idplantel[1];
  $plantel=$grupo->getInfoPlantel($idplantel);
}
if($rol==2){
  $plantel=$grupo->getPlantel($uid);
}

$nom_plantel=$plantel["plantel"]." ".$plantel["numero"];


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
        <li><a href="#"><?php echo $plantel["plantel"]." ".$plantel["numero"]?></a> <span class="divider">/</span></li>
        <li class="active">Grupos</li>
       </ul>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      
      <div class="btn-toolbar pull-right">
        <button class="btn btn-primary" id="btnModalAgregarGrupo"><i class="icon2-white icon2-plus"></i> Grupo</button>
      </div>
      <table class="table table-condensed table-hover" id="tblGrupos">
        <thead>
          <tr>
            <th class="span1 filter-false"></th>
            <th class="span2 filter-select" data-placeholder="Filtrar por Generaci&oacute;n">Generaci&oacute;n</th>
            <th class="span4 filter-select" data-placeholder="Filtrar por Especialidad">Especialidad</th>
            <th class="span2 filter-false">Grupo</th>
            <th class="span2 filter-select" data-placeholder="Filtrar por Turno">Turno</th>
            <th class="span1 filter-false"></th>
          </tr>
        </thead>
        <tbody>
        
       </tbody>
     </table>
     <table>
         <tr id='moldeGrupo' class="hidden">
            <td></td>
            <td>Generacion</td>
            <td>Especialidad</td>
            <td>Grupo</td>
            <td>Turno</td>
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


<div id="modalAgregarGrupo" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Grupo Nuevo</h3>
	</div>
	<div class="modal-body">
	    
    <form class="form-horizontal" id="formAgregarGrupo">
			<input type="hidden" id="plantel" name="plantel" value="<?php echo $plantel["id"]?>">
      <input type="hidden" id="uid" name="uid" value="<?php echo $uid?>">
      <input type="hidden" id="nom_plantel" name="nom_plantel" value="<?php echo $nom_plantel?>">
       <fieldset>
				<div class="control-group">
					<label class="control-label" for="especialidad">Especialidad</label>
					<div class="controls">
						<select name="especialidad" id="especialidad" class="input-xlarge">
              
            </select>
					</div>
				</div>
		
				<div class="control-group">
					<label class="control-label" for="grupo">Grupo</label>
					<div class="controls">
						<input type="text" id="grupo" name="grupo" class="input-mini validate[required] ">
					</div>
				</div>
				
        <div class="control-group">
					<label class="control-label" for="turno">Turno</label>
					<div class="controls">
						<select id="turno" name="turno">
              <option value="1">Matutino</option>
              <option value="2">Vespertino</option>
            </select>
					</div>
				</div>
        
				<div class="control-group">
					<label class="control-label" for="generacion">Generaci&oacute;n</label>
					<div class="controls">
						<select id="generacion" name="generacion">
              
            </select>
					</div>
				</div>
        
        
			</fieldset>
		</form>
    
		<div id="errorAgregarGrupo"></div>
  
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
		<a class="btn btn-primary" id="btnAgregarGrupo" data-loading-text="Agregando... Esto tardar&aacute; algunos segundos">Agregar</a>
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
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/bootstrap-dropdown.js"></script>
<script src="../js/bootstrap-modal.js"></script>
<script src="../js/bootstrap-modalmanager.js"></script>
<script src="../js/bootstrap-tab.js"></script>
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/SetCase.js"></script>
<script src="../js/jquery.tablesorter.js"></script>
<script src="../js/jquery.tablesorter.widgets.js"></script>

<script src="../js/jquery.ui.widget.js"></script>
<script src="../js/jquery.iframe-transport.js"></script>
<script src="../js/jquery.fileupload.js"></script>








<script>

  
$(function () {
  
  $.ajaxSetup({ cache: false });
   
   $('#fileupload').fileupload({
        dataType: 'json',
        add: function (e, data) {
          var hash = {
            'application/vnd.ms-excel'  : 1,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 1,
          };
          $.each(data.files, function (index, file) {
               if (hash[file.type]) {
                 $("#modalAgregarGrupo").modal("hide");
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
            $("#estado").html("<strong>Guardando Grupos</strong>");
             $("#percent").html("0/0");
            var ciclo=setInterval(function() {
              var file_json='progreso'+$("#uid").val()+'.json';
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
                   $("#infoCargaMasiva").show();
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
                   $("#percent").html("Se agregaron "+data.correcto+" grupos de "+data.max);
                   $("#btnCargaMasiva").show();
                   if(refresh){
                      fillTabla();
                    }
                }
               
              });
            }, 300);
            
            $.each(data.result.files, function (index, file) {
                $('#progress .bar').css('width','0%');
                $.post("carga_masiva_grupos.php",{archivo:file.name,id_plantel:$("#plantel").val()},function(data){
                  
                 
                },"json");
                
                
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
   /* $.post("plantilla_grupos.php",{},function(data){
      
    });*/
    location.href='plantilla_grupos.php?id_plantel='+$("#plantel").val()+'&plantel='+$("#nom_plantel").val();
  });
   
   function addRow(grupo)
   {
     $.each(grupo,function(){
        
        $("#moldeGrupo").clone().attr("id","grupo"+this.id).appendTo("#tblGrupos > tbody:last");
        $("#grupo"+this.id).removeClass("hidden");
        $("#grupo"+this.id+" > td:nth-child(2)").html(this.generacion);
        $("#grupo"+this.id+" > td:nth-child(3)").html(this.especialidad);
        $("#grupo"+this.id+" > td:nth-child(4)").html(this.grupo);
        $("#grupo"+this.id+" > td:nth-child(5)").html(this.turno);
      });
   }
   
   function fillTabla()
   {
    $.post("lista_grupos.php",{plantel:$("#plantel").val()},function(grupo)
    {
      addRow(grupo);
      $("[rel=tooltip]").tooltip();
      
      $("#tblGrupos").tablesorter({
        theme : "bootstrap",// this will 
        headers: {
            0: { sorter: false },
            5: {sorter:false}
        },
        sortList: [[1,0],[2,0],[3,0]],
        sortAppend: [[1,0]],
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

    $("#btnAgregarGrupo").click(function(){
      if($("#formAgregarGrupo").validationEngine("validate"))
      {
        $("#btnAgregarGrupo").button('loading');
        //$(this).toggleClass('loading');
        var valores=$("#formAgregarGrupo").serializeArray();
        $.post("agregar_grupo.php",valores,function(data){
          if(data['error'] == 1)
          {
            $("#errorAgregarGrupo").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
           }
          else if(data['error'] == 0)
          {
            $("#errorAgregarGrupo").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
            
            $("#moldeGrupo").clone().attr("id","grupo"+data['idgrupo']).appendTo("#tblGrupos > tbody:last");
            $("#grupo"+data['idgrupo']).removeClass("hidden");
            $("#grupo"+data['idgrupo']+" > td:nth-child(2)").html(data['grupo'].generacion);
            $("#grupo"+data['idgrupo']+" > td:nth-child(3)").html(data['grupo'].especialidad);
            $("#grupo"+data['idgrupo']+" > td:nth-child(4)").html(data['grupo'].grupo);
            $("#grupo"+data['idgrupo']+" > td:nth-child(5)").html(data['grupo'].turno);
            
            var resort = true;
            $("#tblGrupos").trigger("update", [resort]);
            
            setTimeout(function() 
            {
              $('#modalAgregarGrupo').modal('hide')
              formReset();
            }, 2000);
          }
          $("#btnAgregarGrupo").button('reset');

        },"json");
      }
    });
    
    //Click Alumnos
    $("#tblGrupos").on("click",".icon2-user",function()
    {
      var id = $(this).closest("tr").attr("id");
      location.href="alumnos.php?idgrupo="+id;
    });
    
  
  
  
  
  });
</script>

</body>
</html>
