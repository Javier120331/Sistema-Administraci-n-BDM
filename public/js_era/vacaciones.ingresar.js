$(function() {
    window.getDiasRecomendados = function(){
      var ajax = $.ajax({
        type: 'GET',
        url:'../vacaciones/getCantidadDiasRecomendados',
        data:{
          nombre_periodo: $(".periodo").val(),
          id_empleado: $('.empleado').val()
        }
      });
      return ajax;
    };
    window.cargarPeriodos = function(){
      let def = $.Deferred();
      let ajax = $.ajax({
        type: 'GET',
        url:'../vacaciones/getAjaxDataPeriodos',
        data:{
          idEmpleado: $('.empleado').val()
        }
      });
      ajax.done(function(periodos){
        periodos = Object.keys(periodos);
        let selectPeriodos= document.querySelector('.periodo');
        selectPeriodos.innerHTML = '';
        for(let i=0; i < periodos.length; ++i){
          let option = document.createElement('option');
          option.value = periodos[i];
          option.innerText = periodos[i];
          selectPeriodos.appendChild(option);
        }
        def.resolve();
      });
      return def.promise();
    };
    window.definirDiasRecomendados = function(){

      window.getDiasRecomendados().done(function(respuesta){
        $(".advertencia-vacaciones").addClass("d-none");
        if(respuesta.permitirVacaciones){
          $(".dias_disponibles span").text(respuesta.diasRecomendados);
        } else {
          $(".advertencia-vacaciones span").text(respuesta.mensaje);
          $(".cantidad_dias").val(0);
          $(".advertencia-vacaciones").removeClass("d-none");
        }
        window.cargarFechasReales();
      });
    };
    window.cargarFechasReales = function(){

      var ajax = $.ajax({
        type: 'GET',
        url:'../feriados/getFechasReales',
        data:{
          fecha_inicio: $(".fecha_inicio").val(),
          cantidad_dias: $(".cantidad_dias").val()
        }
      });
      ajax.done(function(respuesta){
        var fechaFinal = respuesta.fechas[respuesta.fechas.length -1];
        $(".fecha_final").val(new moment(fechaFinal).format('DD/MM/YYYY'));
      });
    };
  window.recargarDatosDias = function(){
    window.cargarPeriodos().done(window.definirDiasRecomendados);
  };
  window.recargarDatosDias();
  $("body").on("click", ".ingresar-vacaciones-btn", function(){
    //Evaluamos si existe algun impedimento para brindar vacaciones
    window.getDiasRecomendados().done(function(respuesta){
      $("#modal-adv-vac .mensaje-adv-vac").empty();
      if(!respuesta.permitirVacaciones){
        $("#modal-adv-vac .mensaje-adv-vac")
          .append('<p class="alert alert-warning">'+respuesta.mensaje+ '</p>');
        $("#modal-adv-vac").modal();
      } else {
        //Verificamos si es que al trabajor se le estan definiendo más dias de
        //los permitidos
        let cantidadElegida = $(".cantidad_dias").val();
        if(respuesta.diasRecomendados < cantidadElegida){
          $("#modal-adv-vac .mensaje-adv-vac")
            .append('<p class="alert alert-warning">El trabajador dispone de'
             +'menos días de vacaciones de los que intenta registrar</p>');
          $("#modal-adv-vac .mensaje-adv-vac")
               .append("<h6>Días Recomendados:"+respuesta.diasRecomendados+" </h3>");
          $("#modal-adv-vac .mensaje-adv-vac")
                    .append("<h6>Días definidos:"+cantidadElegida+" </h3>");
          $("#modal-adv-vac").modal();
        } else {
          //Sino, efectuamos ingreso
          $(".form-vacaciones").submit();
        }
      }
    });
  });
  $("body").on("click","#modal-adv-aceptar", function(){
    $(".form-vacaciones").submit();
  });
  $("body").on("select2:select", ".empleado", window.recargarDatosDias);

  $("body").on("select2:select", ".periodo", window.definirDiasRecomendados);
  $("body").on("input", ".cantidad_dias,.fecha_inicio",window.cargarFechasReales);
});
