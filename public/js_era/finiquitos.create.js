window.confirmarFiniquito = function(){
   let $modalConfirmacion = $('.resultadoFiniquitar');
   let $btnConfirmar = $modalConfirmacion.find('.confirmarBtn');
   let $spanTexto = $('<span>');
   $spanTexto.text("El finiquitar personal es un proceso crítico."
    +" ¿Desea realmente finiquitar al trabajador?.");
   $spanTexto.addClass('text-danger');
   $btnConfirmar.on('click', function(){
     $('#form-finiquito').submit();
   });

   $modalConfirmacion.find('.confirmarTexto').html($spanTexto);
   $modalConfirmacion.modal('show');
};
window.cargarTotalAniosServicio = function(){
  let ajax = $.ajax({
     type:'POST',
     url:'getAjaxSueldoAniosServicio',
     data: {
       fechaFiniquito: $(".fecha_finiq").val(),
       sueldoPromedio: $("#prom_rem_txt").val(),
       idCausaFiniquito: $(".causaFiniquito").val()
     }
  });
  ajax.done(function(resp){
    if(resp != null){

      $("#total_pagar_anios_servicio_txt").val(resp.totalPagarServicio);
    } else {
      $("#total_pagar_anios_servicio_txt").val("0");
    }
  });
};
window.cargarSueldoFiniquito = function(idEmpleado){
    let deferred = $.Deferred();
    let ajax = $.ajax({
       type:'POST',
       url:'getAjaxDataEmpleado',
       data: {idEmpleado:idEmpleado}
    });
    ajax.done(function(resp){
      $('.pagar_mes_aviso_div input')
        .val(window.formatearMoneda(resp.sueldoActual));
      deferred.resolve();
    });
    return deferred;
};
//recalcula el valor y cantidad de inhabiles a la hora de cambiar un parámetro
//que influye en su calculo
window.recalcularInhabiles = function(){
  let diasVacaciones = $('#dias_vacaciones_txt').val()
    , remuPromedio = window.desformatearMoneda($('#prom_rem_txt').val())
    , fechaFiniquito = $('#fecha_finiq').val()
    , ajax = $.ajax({
        type: 'GET'
      , url: 'getAjaxDiasInhabiles'
      , data:{
           fecha_finiquito: fechaFiniquito
         , cantidad_habiles: diasVacaciones
         , promedio_remu: remuPromedio
      }
    })
  ;
  ajax.done(function(respuesta){
    $('#dias_inhabiles_vacaciones_txt').val(respuesta.diasInhabiles);
    $('#total_dias_inhabiles_vacaciones_txt').val(window
        .formatearMoneda(respuesta.totalInhabiles));
  });

};
window.cargarTotalVacaciones = function(){
   let ajax = $.ajax({
       type: 'POST'
     , url: 'getVacacionesAjax'
     , data:{
        diasHabiles: $("#dias_vacaciones_txt").val()
      , remuneracionPromedio: $("#prom_rem_txt").val()
     }
   });
   ajax.done(function(respuesta){
     $("#total_dias_vacaciones_txt")
        .val(window.formatearMoneda(respuesta.valor));
   });
};

window.cargarTotalInhabilesVacaciones = function(){
   let ajax = $.ajax({
       type: 'POST'
     , url: 'getVacacionesAjax'
     , data:{
        diasHabiles: $("#dias_inhabiles_vacaciones_txt").val()
      , remuneracionPromedio: $("#prom_rem_txt").val()
     }
   });
   ajax.done(function(respuesta){
     $("#total_dias_inhabiles_vacaciones_txt")
        .val(window.formatearMoneda(respuesta.valor));
   });
};
window.calcularTotalPagar = function(){

  let remuPromedio = window.desformatearMoneda($('#prom_rem_txt').val())
    , indemAniosServicio = window
      .desformatearMoneda($("#total_pagar_anios_servicio_txt").val())
    , totalDiasHabVacaciones = window
      .desformatearMoneda($("#total_dias_vacaciones_txt").val())
    , seguroDesempleo = window
      .desformatearMoneda($("#seguro_desempleo_txt").val())
    , prestamoEmpresa = window
      .desformatearMoneda($("#prestamo_empresa_txt").val())
    , mesAviso = $('#pagar_mes_aviso_chk').is(':checked')
      ? window
        .desformatearMoneda($("#mes_aviso_valor_txt").val()) : 0
    , totalInhabilesVacaciones = window
        .desformatearMoneda($('#total_dias_inhabiles_vacaciones_txt').val())
    , descuentoSobregiro = window
        .desformatearMoneda($('#descuento_sobregiro_txt').val())
    , descuentoConvenios = window
        .desformatearMoneda($('#descuento_convenios_txt').val())
  ;
  let totalAPagar = Math.trunc(+remuPromedio + +indemAniosServicio
      + +totalDiasHabVacaciones + +totalInhabilesVacaciones
      + +mesAviso
      - +seguroDesempleo - +prestamoEmpresa
      - +descuentoSobregiro
      - +descuentoConvenios
      );

  $("#total_pagar_finiquito_txt").val(window.formatearMoneda(totalAPagar));
};
$(function(){

    //Solo si el card existe en el dom, significa que fue efectuada la petición
    //POST, por lo cual se debe lanzar la carga del total
    if(document.querySelector(".cardDetalleFiniquito") !== null){
      window.calcularTotalPagar();
    }

    $('body').on('change','#pagar_mes_aviso_chk', function(){
      if(this.checked){
        window.cargarSueldoFiniquito(this.value).done(function(){
          window.calcularTotalPagar();
          $('.pagar_mes_aviso_div').removeClass('d-none');
        });
      } else {
        $('.pagar_mes_aviso_div').addClass('d-none');
        window.calcularTotalPagar();
      }
    });

    $('body').on('input', '.input-finiquito', window.calcularTotalPagar);
    $("body").on('input', '#dias_vacaciones_txt'
      , window.cargarTotalVacaciones);
    $("body").on('input', '#dias_inhabiles_vacaciones_txt'
        , window.cargarTotalInhabilesVacaciones);
    $("body").on('click', '#finiquitar_btn', window.confirmarFiniquito);

    $('body').on('input','#dias_vacaciones_txt,#prom_rem_txt,#fecha_finiq'
      ,window.recalcularInhabiles);
    $("body").on('change', '.causaFiniquito', window.cargarTotalAniosServicio);
});
