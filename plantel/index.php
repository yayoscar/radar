<?php
include("../class/auth.class.php");
include("../class/config.php");

$auth = new Auth;

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
    
    
	
	

</head>

<body>

  
<!-- Navbar
  ================================================== -->
<?php $auth->menu($email,$rol,"planteles"); ?>

<div class="container">
  <div class="row">
    <div class="span12">
      <ul class="breadcrumb">
        <li><a href="#">Inicio</a> <span class="divider">/</span></li>
        <li class="active">Planteles</li>
       </ul>
    </div>
  </div>
  <div class="row">
    <div class="span12">
     
      
      <ul class="nav nav-tabs" id="tab_planteles">
        <li class="active"><a href="#cbtis" data-toggle="tab">CBTis</a></li>
        <li><a href="#cetis" data-toggle="tab">CETis</a></li>
      </ul>
      
      <div class="tabbable">
        <div class="tab-content">
          <div class="tab-pane active" id="cbtis">
            <table class="table table-condensed table-hover" id="tblCbtis">
              <thead>
                <tr>
                  <th class="span1 filter-false"></th>
                  <th class="span2" data-placeholder="Filtrar por Plantel">Plantel</th>
                  <th class="span3 filter-select" data-placeholder="Filtrar por Estado">Estado</th>
                  <th class="span4 filter-select" data-placeholder="Filtrar por Localidad">Localidad</th>
                  <th class="span2 filter-false"></th>
                </tr>
              </thead>
              <tbody>
                <tr id="moldePlantel" class="hidden">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <i class='icon2-edit' rel="tooltip" title="Editar" ></i>
                     <i class='icon2-ok-circle' rel="tooltip" title="Asignar Admin" ></i>
                    <i class='icon2-group' rel="tooltip" title="Ver Grupos" >&nbsp;&nbsp;&nbsp;</i>    
                    <i class='icon2-trash' rel="tooltip" title="Eliminar" ></i>
                  </td>
                  
                </tr>

              </tbody>
            </table>
          </div>
          <div class="tab-pane" id="cetis">
            <table class="table table-condensed table-hover" id="tblCetis">
              <thead>
                <tr>
                  <th class="span1 filter-false"></th>
                  <th class="span2" data-placeholder="Filtrar por Plantel">Plantel</th>
                  <th class="span3 filter-select" data-placeholder="Filtrar por Estado">Estado</th>
                  <th class="span4 filter-select" data-placeholder="Filtrar por Localidad">Localidad</th>
                  <th class="span2 filter-false"></th>
                </tr>
              </thead>
              <tbody>
                

              </tbody>
            </table>
          </div>
         </div>
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

<div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Eliminar Plantel</h3>
    </div>
    <div class="modal-body">
      <p class="error-text">Se eliminar&aacute; toda la informaci&oacute;n relacionada a este plantel</p>
       <p class="error-text">¿Est&aacute; seguro de eliminarlo?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-danger" data-dismiss="modal">Eliminar</button>
    </div>
</div>


<div id="modalAgregarPlantel" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
      
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Agregar Plantel</h3>
    
	</div>
	<div class="modal-body">
	  <button type="button" class="btn btn-small btn-success pull-right"><i class="icon2-truck">&nbsp;&nbsp;</i>Carga Masiva</button> 
    <form class="form-horizontal" id="formAgregarPlantel">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="plantel">Plantel</label>
					<div class="controls">
						<select name="plantel" id="plantel">
              <option value="CBTis">CBTis</option>
              <option value="CETis">CETis</option>
            </select>
					</div>
				</div>
		
				<div class="control-group">
					<label class="control-label" for="numero">N&uacute;mero</label>
					<div class="controls">
						<input type="text" id="numero" name="numero" class="input-mini validate[required,custom[integer]] ">
					</div>
				</div>
        
        <div class="control-group">
					<label class="control-label" for="numero">Nombre</label>
					<div class="controls">
						<input type="text" id="nombre" name="nombre" class="input-xlarge validate[required] ">
					</div>
				</div>
				
        <div class="control-group">
					<label class="control-label" for="estado">Estado</label>
					<div class="controls">
						<select id="estado" name="estado">
              
            </select>
					</div>
				</div>
        
				<div class="control-group">
					<label class="control-label" for="admin">Email del Administrador del Plantel</label>
					<div class="controls">
						<input type="text" id="admin2" class="input-xlarge  validate[required,custom[email]]">
					</div>
				</div>
			</fieldset>
		</form>
		<div id="errorAgregarPlantel"></div>
	</div>
	
	<div class="modal-footer">
		<a data-dismiss="modal" class="btn">Cancelar</a>
		<a class="btn btn-primary" id="btnAgregarPlantel" data-loading-text="Agregando... Esto tardar&aacute; algunos segundos">Agregar</a>
	</div>
</div> 

<div id="modalAgregarAdmin" class="modal hide fade" tabindex="-1">
    
  <div class="modal-header">
      
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Agregar Administrador</h3>
    
	</div>
	<div class="modal-body">
	  <form class="form-horizontal" id="formAgregarAdmin">
			<fieldset>
				<input type="hidden" id="idplantel" name="idplantel"/>
        <div class="control-group">
					<label class="control-label" for="admin">Email del Administrador del Plantel</label>
					<div class="controls">
						<input type="text" id="admin" name="admin" class="input-xlarge  validate[required,custom[email]]">
					</div>
				</div>
			</fieldset>
		</form>
		<div id="errorAgregarAdmin"></div>
	</div>
	
	<div class="modal-footer">
		<a data-dismiss="modal" class="btn">Cancelar</a>
		<a class="btn btn-primary" id="btnAgregarAdmin" data-loading-text="Agregando... Esto tardar&aacute; algunos segundos">Agregar</a>
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
<script src="../js/bootstrap-tab.js"></script>
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/SetCase.js"></script>
<script src="../js/jquery.tablesorter.js"></script>
<script src="../js/jquery.tablesorter.widgets.js"></script>



<script>
   $(function() {
   
   $.ajaxSetup({ cache: false });
    actualizarPlanteles();
    fillCombo();
    $("#nombre").Setcase({caseValue : 'upper'});
    $("#admin").Setcase({caseValue : 'lower'});

    
    $('#tab_planteles a:first').tab('show');
    
    $("#btnModalAgregarPlantel").click(function(){
      $("#modalAgregarPlantel").modal("show");
    });
    
    function formReset()
    {
      $("#formAgregarAdmin").each (function(){
        this.reset();
      });
      $("#errorAgregarAdmin").empty();
      $("#btnAgregarAdmin").button('reset');
      $('#formAgregarAdmin').validationEngine('hide');
    }
    
    $("#modalAgregarPlantel").on("hidden", function () {
      formReset();
    });
    
    $("#formAgregarPlantel").validationEngine();
    
    $("#admin").autocomplete({
			delay:0,
			minLength: 2,
			source: "buscar_email.php",
			select: function( event, ui ) {
				$("#admin").validationEngine('hide');
			}
		});
    
        
    $("#btnAgregarPlantel").click(function(){
      if($("#formAgregarPlantel").validationEngine("validate"))
      {
        $("#btnAgregarPlantel").button('loading');
        var valores=$("#formAgregarPlantel").serializeArray();
        $.post("agregar_plantel.php",valores,function(data){
          if(data['error'] == 1)
          {
            $("#errorAgregarPlantel").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
           }
          else if(data['error'] == 0)
          {
            $("#errorAgregarPlantel").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
            //actualizarPlanteles();
            
            var aux;
            if(data['plantel'].plantel=="CBTis"){
              aux="#tblCbtis";
            } else if (data['plantel'].plantel=="CETis"){
              aux="#tblCetis";
            }
 
              
             
              $("#moldePlantel").clone().attr("id","plantel"+data['plantel'].id).appendTo(aux+ " > tbody:last");
              $("#plantel"+data['plantel'].id).removeClass("hidden");
              $("#plantel"+data['plantel'].id+" > td:nth-child(2)").html("<span class='label label-info'>"+data['plantel'].plantel+"</span> <span class='label label-important'>"+data['plantel'].numero+"</span> <span class='label label-success'>"+data['plantel'].nombre+"</span>");
              $("#plantel"+data['plantel'].id+" > td:nth-child(3)").html("<strong>"+data['plantel'].nom_estado+"</strong>");
            
            var resort = true;
            $(aux).trigger("update", [resort]);
            
            setTimeout(function() 
            {
              $('#modalAgregarPlantel').modal('hide')
              formReset();
            }, 2000);
          }
          $("#btnAgregarPlantel").button('reset');
        },"json");
      }
    });
    
   
    
   
    
    function actualizarPlanteles()
    {
      var idPlantel,plantel,numero,estado;
      $.post("lista_planteles.php",function(data)
      {
        $.each(data["CBTis"],function(){
          /*this.nombre=pascal(this.nombre);
          this.nom_estado=pascal(this.nom_estado);*/
          $("#moldePlantel").clone().attr("id","plantel"+this.id).appendTo("#tblCbtis > tbody:last");
          $("#plantel"+this.id).removeClass("hidden");
          $("#plantel"+this.id+" > td:nth-child(2)").html("<span class='label label-info'>"+this.plantel+"</span> <span class='label label-important'>"+this.numero+"</span> <span class='label label-success'>"+this.nombre+"</span>");
          $("#plantel"+this.id+" > td:nth-child(3)").html(this.nom_estado);
          $("#plantel"+this.id+" > td:nth-child(4)").html(this.localidad);
        });
        
        $.each(data["CETis"],function(){
          /*this.nombre=pascal(this.nombre);
          this.nom_estado=pascal(this.nom_estado);*/
          $("#moldePlantel").clone().attr("id","plantel"+this.id).appendTo("#tblCetis > tbody:last");
          $("#plantel"+this.id).removeClass("hidden");
          $("#plantel"+this.id+" > td:nth-child(2)").html("<span class='label label-info'>"+this.plantel+"</span> <span class='label label-important'>"+this.numero+"</span> <span class='label label-success'>"+this.nombre+"</span>");
          $("#plantel"+this.id+" > td:nth-child(3)").html(this.nom_estado);
          $("#plantel"+this.id+" > td:nth-child(4)").html(this.localidad);
        });
        
        $("[rel=tooltip]").tooltip();
        
        $(".table").tablesorter({
          theme : "bootstrap",// this will 
          headers: {
              0: { sorter: false },
              3: {sorter:false}
          },
          sortList: [[2,0],[1,0]],
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
    
    $("#tblCetis").on("click",".icon2-group",function()
    {
      var id = $(this).closest("tr").attr("id");
      location.href="../grupos/index.php?idplantel="+id;
    });
    
    $("#tblCbtis").on("click",".icon2-group",function()
    {
      var id = $(this).closest("tr").attr("id");
      location.href="../grupos/index.php?idplantel="+id;
    });
    
    $("#tblCetis").on("click",".icon2-ok-circle",function()
    {
      var id = $(this).closest("tr").attr("id");
      location.href="../grupos/index.php?idplantel="+id;
    });
    
    $("#tblCbtis").on("click",".icon2-ok-circle",function()
    {
      var id = $(this).closest("tr").attr("id");
      $("#idplantel").val(id);
      $("#modalAgregarAdmin").modal("show");
    });
    
    function fillCombo()
    {
      $.post("cambiar_combo.php",function(data)
      {
        $("#estado").empty();
        options='';
        $.each(data, function (){
          var $option = $("<option/>").attr("value", this.id).html(this.nombre);
          //if( this.selected == true ) $option.attr("selected", "selected");
         $option.appendTo($("#estado"));
        });	
      },"json");
    }
    
    $("#btnAgregarAdmin").click(function(){
      if($("#formAgregarAdmin").validationEngine("validate"))
      {
        $("#btnAgregarAdmin").button('loading');
        var valores=$("#formAgregarAdmin").serializeArray();
        $.post("agregar_admin.php",valores,function(data){
          if(data['error'] == 1)
          {
            $("#errorAgregarAdmin").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
           }
          else if(data['error'] == 0)
          {
            $("#errorAgregarAdmin").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data['message'])+"</div>";
            //actualizarPlanteles();
            
               
            setTimeout(function() 
            {
              $('#modalAgregarAdmin').modal('hide')
              formReset();
            }, 3000);
          }
          $("#btnAgregarAdmin").button('reset');
        },"json");
      }
    });
    
    
  });
</script>





</body>
</html>
