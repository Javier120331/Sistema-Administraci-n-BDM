
window.efectuarImportacion = function(ruta){
  var $divMensajes = $(this).parent().find('.mensajesResultado')
  , peticion = undefined
  , $modal = $('#modalImportando')
  ;

  $modal.modal('show');
  $modal.on("shown.bs.modal", function(e){
    $divMensajes.addClass('hidden');
    peticion = $.ajax({
      url: ruta
      , type: 'POST'
    });
    peticion.done(function(respuesta){
      var $mensajeCorrecto = $divMensajes.find('.infoGeneral')
      , $mensajeError = $divMensajes.find('.errorGeneral')
      ;
      $divMensajes.removeClass('hidden');
      $mensajeCorrecto.empty();
      $mensajeError.empty();
      if(respuesta.correcto){

        $mensajeCorrecto.text(respuesta.mensaje);
      } else {

        $mensajeError.text(respuesta.mensaje);
      }
      $modal.modal('hide');
    });
  });
  return false;
};
$(function() {
  window.konamiActivado = false;
   var easterEgg = new Konami(function(){
     if(!window.konamiActivado){
         $('body').addClass("konami");
         $(".rollback_importacion").removeClass("d-none");
         window.konamiActivado = true;
       }
   });
  $('.btnSubirLiquidacion').on('click', function(){
    window.efectuarImportacion.call(this,'importacion/upload');
  });
  $('.btnSubirRemuneraciones').on('click', function(){
    window.efectuarImportacion.call(this,'importacion/upload_remuneraciones');
  });
});
