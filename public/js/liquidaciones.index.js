$(function() {

  window.seleccionarCosechadoresDelGrupo = function(idGrupo){
    var peticion = $.ajax({
       type: "GET"
     , url: "liquidacion/grupo/cosechadores/"+idGrupo
   });
   peticion.done(function(respuesta){
     window.reinicializarSelectCosechadores(respuesta);
     if($('.todosCheckBoxGrupo').is(':checked')){
       window.seleccionarTodosCosechadores();
     }
   });
  };

  /**
   * Reinicializa el select de cosechadores con los datos provistos
   * @param  {Array[Object]} datos con la estructura de cada object
   * {id, text}
   * @return void
   * @todo Generar mi propio Select2 con juegos de azar y agregar
   * este método?
   */
  window.reinicializarSelectCosechadores = function(datos){
    var $select = $('.cosechador');

    // save current config. options
    var options = $select.data('select2').options.options;



    // delete all items of the native select element
    $select.html('');

    // build new items
    for (var i = 0; i < datos.length; i++) {

    // logik to create new items
      $select.append("<option value=\"" + datos[i].id + "\">" + datos[i].text + "</option>");
    }

    // add new items
    options.data = datos;
    $select.select2(options);
  };
  window.seleccionarTodosCosechadores = function(){

    var $cosechadores = $('.cosechador')
      , selectedItems = $cosechadores
          .find('option')
          .map(function() { return this.value })
    ;
    $cosechadores.val(selectedItems).trigger('change');
  };
  window.quitarTodosCosechadores = function(){
    var $cosechadores = $('.cosechador');
    $cosechadores.val(null).trigger('change');
  };
  $('.todosCheckBox').on('click', function(){
    var $grupo = $('.grupo')
      , $cosechadores = $('.cosechador')
    if($(this).is(':checked')){
      $grupo.attr('disabled', true);
      $cosechadores.attr("disabled", true);
    } else {
      $grupo.removeAttr('disabled');
      if(!$('.todosCheckBoxGrupo').is(':checked')){
        $cosechadores.removeAttr('disabled');
        window.quitarTodosCosechadores();
      }
    }
  });

  $('.todosCheckBoxGrupo').on('click', function(){
    var $cosechadores = $('.cosechador')
    if(!$('.todosCheckBox').is(':checked')){
      if($(this).is(':checked')){
        $cosechadores.attr("disabled", true);
      } else {
        $cosechadores.removeAttr('disabled');
        window.quitarTodosCosechadores();
      }
    }
  });

  $('.grupo').on('change', function(){
    var $idGrupo = $(this).val();
    window.seleccionarCosechadoresDelGrupo($idGrupo);
  });

});
