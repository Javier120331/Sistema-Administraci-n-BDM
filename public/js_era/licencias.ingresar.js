$(function() {

    $("body").on("input", ".cantidad_dias", function(){
      var ajax = $.ajax({
        type: 'GET',
        url:'../feriados/getFechasRealesIncWeekend',
        data:{
          fecha_inicio: $(".fecha_inicio").val(),
          cantidad_dias: $(".cantidad_dias").val()
        }
      });
      ajax.done(function(respuesta){
        var fechaFinal = respuesta.fechas[respuesta.fechas.length -1];
        $(".fecha_final").val(new moment(fechaFinal).format('DD/MM/YYYY'));
      })
    });

});
