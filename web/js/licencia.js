$( document ).ready(function() {
    $('.loading').hide();
    configureSteps();   
    
    // Cuando cambia la provincia saco el id y nombre y busco si es de santa fe
    $( "#gestionar_licencia_persona_provincia" ).change(function() {       
       var provinciaNombre = $("#gestionar_licencia_persona_provincia option:selected").text();
       var provinciaId = this.value;
       evaluarProvincia(provinciaId,provinciaNombre);
    });
    
});

function confirmarGenerarLicencia(){    
    var tipoLicencia = $("#gestionar_licencia_tipoLicencia option:selected").text();
    
    var c = confirm("¿ Generar Licencia de: "+tipoLicencia +" ?");
    if(c === true){
        return true;
    }else{
        return false;
    }
   
}

function evaluarProvincia(provinciaId,provinciaNombre){    
    var parameters= {'provinciaId':provinciaId,'provinciaNombre':provinciaNombre};
    var pathBase = $("#includeLicenciajs").attr("data-path-base");
    if(!isEmpty(provinciaNombre) && 
        !isEmpty(provinciaId) )
    {
        
        $.ajax({  
            url:        pathBase + 'provincia/findBy/'+ provinciaId + '/' + provinciaNombre ,
            type:       'POST',   
            dataType:   'json',  
            async:      true,  
            data: JSON.stringify(parameters),               
            success: function(data, status) {     
                configurarSelectLocalidad(data.santaFe);
                configurarTipoLicencia(data);
                
            },  
            error : function(xhr, textStatus, errorThrown) {                                
                console.error(xhr,textStatus,errorThrown);
                
            }  
            });  
    }else{
        console.error("No me llega el id de provincia " +provinciaId +"  y la descripcion " +provinciaNombre);
    }
}

function configurarTipoLicencia(provincia){    
    var parameters= {'provincia':JSON.stringify(provincia)};
    var pathBase = $("#includeLicenciajs").attr("data-path-base");
    if(!isEmpty(provincia))      
    {
        $.ajax({  
            url:        pathBase + 'provincia/findTiposLicenciaForProvincia',
            type:       'POST',   
            dataType:   'json',  
            async:      true,  
            data: JSON.stringify(parameters),               
            success: function(data, status) {                     
                actualizarTiposLicenciaDisponibles(data);
            },  
            error : function(xhr, textStatus, errorThrown) {                               
                console.error(xhr,textStatus,errorThrown);
            }  
        });  
    }else{
        console.error("No me llega tipoLicenciaId");
    }
}

function actualizarTiposLicenciaDisponibles(tiposLicencia){
    var newOptions = {
       
    };
    
    var select = $('#gestionar_licencia_tipoLicencia');
    if(select.prop) {
      var options = select.prop('options');
    }
    else {
      var options = select.attr('options');
    }
    // Borra todas las opciones menos la primera
    $('#gestionar_licencia_tipoLicencia').children('option:not(:first)').remove();

   
    $.each(tiposLicencia, function(val, tipoLicencia) { 
        if(tipoLicencia && !isEmpty(tipoLicencia['descripcion'])            
            && !isEmpty(tipoLicencia['id']) ){
                var id = tipoLicencia['id'];
                var descripcion = "";                
                if(tipoLicencia['arancel'] == 0){
                        descripcion = tipoLicencia['descripcion'] + " Gratuita";
                    }else{
                        descripcion = tipoLicencia['descripcion'] + " $"+tipoLicencia['arancel'];
                    }

                $('#gestionar_licencia_tipoLicencia')
                    .append($("<option></option>")
                    .attr("value",id)
                    .text(descripcion));
        }
    });
       
}
function configurarSelectLocalidad(isSantaFe){
    if(isSantaFe){    
        $( "#localidad").show();                                            
        $( "#gestionar_licencia_persona_localidad").attr('required', true);
        $( "#gestionar_licencia_persona_localidadOtraProvincia").attr('required', false);                        
        $( "#localidadOtraProvincia").hide();
        // no puede seleccionar fecha desde si es de santa fe
        $( "#gestionar_licencia_fechaDesde").attr('disabled', 'disabled');
        $( "#gestionar_licencia_fechaDesde").attr('readonly', 'readonly');
        $( "#gestionar_licencia_fechaDesde").val(getDateWithFormat('dd/mm/yyyy'));
        
    }else{     
        $( "#localidad").hide();      
        $("#gestionar_licencia_persona_localidad").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");                                             
        $( "#gestionar_licencia_persona_localidad").attr('required', false);
        $( "#gestionar_licencia_persona_localidadOtraProvincia").attr('required', true);
        $( "#localidadOtraProvincia").show();  
        
        // puede seleccionar fecha desde si es de santa fe       
        $( "#gestionar_licencia_fechaDesde").val(getDateWithFormat('dd/mm/yyyy'));
        $( "#gestionar_licencia_fechaDesde").removeAttr('disabled');
        $( "#gestionar_licencia_fechaDesde").removeAttr('readonly');
    }   
}

function hayDatosCargados(){    
    var result = false;
    var tipoDocumento = $("#gestionar_licencia_persona_tipoDocumento").val();
    var numeroDocumento = $("#gestionar_licencia_persona_numeroDocumento").val();
    var sexo = $("#gestionar_licencia_persona_sexo").val();
    var apellido = $("#gestionar_licencia_persona_nombre").val();
    var nombre = $("#gestionar_licencia_persona_apellido").val();

    if(!isEmpty(tipoDocumento) &&
        !isEmpty(numeroDocumento) &&
        !isEmpty(sexo) &&
        !isEmpty(apellido) &&
        !isEmpty(nombre) 
        ){
            result=true;
        }

    return result;
}

function configureSteps(){  
    if(!hayDatosCargados())  {
        step1();
    }else{
        step3();
    }
}
function step1()
{
    $('#divBusquedaPersona').addClass("disabledPanel");
    $('#divFormularioLicencia').addClass("disabledPanel");
}

function step2()
{
    $('#divBusquedaPersona').show();    
    $('#divBusquedaPersona').removeClass("disabledPanel");         
}

function step3()
{
    $('div.setup-panel #step-3').trigger('click');

    $('#divFormularioLicencia').removeClass("disabledPanel");

    var provinciaId =  $('#gestionar_licencia_persona_provincia').val();

    if(!isEmpty(provinciaId)) {
        var provinciaNombre = $('#gestionar_licencia_persona_provincia option[value=' + provinciaId + ']').text();
        evaluarProvincia(provinciaId, provinciaNombre);
    }
}

function imprimirBoletaPago(urlBoletaPago){
    if(!isEmpty(urlBoletaPago)){
        window.open(urlBoletaPago,'_blank'); 
    }
}

function buscarPersona(){

    var tipoDocumento = $("#gestionar_licencia_persona_tipoDocumento").val();
    var numeroDocumento = $("#gestionar_licencia_persona_numeroDocumento").val();
    var sexo = $("#gestionar_licencia_persona_sexo").val();
    var parameters= {
        "tipoDocumento":tipoDocumento,
        "numeroDocumento":numeroDocumento,
        "sexo":sexo
    }
    console.log(JSON.stringify(parameters));
    debugger;

    var pathBase = $("#includeLicenciajs").attr("data-path-base");
    var debug = '?XDEBUG_SESSION_START=13128';
    if(!isEmpty(tipoDocumento) &&
        !isEmpty(numeroDocumento) &&
        !isEmpty(sexo)
        )
        {

            $.ajax({  
                url:        pathBase + 'persona/findBy/'+ tipoDocumento + '/' + numeroDocumento +'/'+ sexo + debug ,
                type:       'POST',   
                dataType:   'json',  
                async:      true,  
                data: JSON.stringify(parameters),               
                success: function(data, status) {
                    debugger;
                        busquedaPersonaEjecutada = 1;
                        step3();
                        if(data){
                            var persona=data;
                            bindValuesToPersona(persona);
                        }else{                            
                            clearValuesPersona();
                        }              
                },  
                error : function(xhr, textStatus, errorThrown) {                
                    alert('No se pudo recuperar los datos.');  
                    console.error(xhr,textStatus,errorThrown);
                }  
                });  
        }else{
            alert("Debe completar todos los campos para buscar");
        }
}
function clearValuesPersona(persona){
    $("#gestionar_licencia_persona_nombre").val("");
    $("#gestionar_licencia_persona_apellido").val("");
    $("#gestionar_licencia_persona_fechaNacimiento").val("");
    $("#gestionar_licencia_persona_domicilioCalle").val("");
    $("#gestionar_licencia_persona_domicilioNumero").val("");
    $("#gestionar_licencia_persona_telefono").val("");
    $("#gestionar_licencia_persona_email").val("");
    $("#gestionar_licencia_persona_id").val("");
    $('#gestionar_licencia_persona_localidad').prop('selectedIndex',0);
    $('#gestionar_licencia_persona_provincia').prop('selectedIndex',0);
    $("#gestionar_licencia_persona_localidadOtraProvincia").val("");
}

function bindValuesToPersona(persona){
    $("#gestionar_licencia_persona_nombre").val(persona['nombre']);
    $("#gestionar_licencia_persona_apellido").val(persona['apellido']);
    $("#gestionar_licencia_persona_fechaNacimiento").val(persona['fechaNacimiento']);
    $("#gestionar_licencia_persona_domicilioCalle").val(persona['domicilioCalle']);
    $("#gestionar_licencia_persona_domicilioNumero").val(persona['domicilioNumero']);    
    $("#gestionar_licencia_persona_telefono").val(persona['telefono']);
    $("#gestionar_licencia_persona_email").val(persona['email']);
    $("#gestionar_licencia_persona_id").val(persona['id']);

    var jubilado = 1;
    if(persona['jubilado']==true){
        jubilado =0;
    } 
    var idJubilado="gestionar_licencia_persona_jubilado_"+ jubilado;        
    $( "#"+idJubilado ).prop( "checked", true );

    if(persona && persona['localidad']){
        var localidadId = persona['localidad']['id'];
        $("#gestionar_licencia_persona_localidad").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");                                             
        $('#gestionar_licencia_persona_localidad option[value='+localidadId +']').attr('selected','selected');
        $('#gestionar_licencia_persona_localidad').trigger('change', true);
    }
    
    if(persona && persona['provincia']){
        var provinciaId = persona['provincia']['id'];
        var provinciaNombre = persona['provincia']['nombre'];
        $('#gestionar_licencia_persona_provincia option[value='+provinciaId +']').attr('selected','selected');

        configurarSelectLocalidad(persona['provincia']['santaFe']);
        evaluarProvincia(provinciaId,provinciaNombre);
    }

    
    $("#gestionar_licencia_persona_localidadOtraProvincia").val(persona['localidadOtraProvincia']);
}
