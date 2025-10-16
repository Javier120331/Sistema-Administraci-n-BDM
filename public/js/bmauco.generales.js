$.extend(true, $.fn.datetimepicker.defaults, {
  icons: {
    time: 'far fa-clock',
    date: 'far fa-calendar',
    up: 'fas fa-arrow-up',
    down: 'fas fa-arrow-down',
    previous: 'fas fa-chevron-left',
    next: 'fas fa-chevron-right',
    today: 'fas fa-calendar-check',
    clear: 'far fa-trash-alt',
    close: 'far fa-times-circle'
  }
});

$(function(){

  /**
  * Valida que lo ingresado en un input sea solo números
  * @param  Event  evt Evento lanzado por e input
  * @return Boolean true si lo ingresado son numeros, false caso contrario
  */
  window.onlyNumeros=function isNumericKey(evt)
  {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46 || charCode > 31 && (charCode < 48 || charCode > 57)){
      evt.preventDefault();
      return false;
    }
    return true;
  };
  window.formatearMoneda = function (amount) {
    amount+="";
    num = amount.replace(/\./g,'');
    if(!isNaN(num)){
      num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
      num = num.split('').reverse().join('').replace(/^[\.]/,'');
    } else {
      num = amount;
    }
    return num;
  };

  window.desformatearMoneda = function(amount){
      return amount.replace(/\./g,'');
  };
  var $infoGeneral = $('.infoGeneral')
  , $errorGeneral = $('.errorGeneral')
  , definirEstadoProgreso = function(valor, $elementoProgreso, titulo){

    var $progressBar = $elementoProgreso.find('.progress-bar')
    , $tituloProgreso = $elementoProgreso.find('h5')
    ;
    $tituloProgreso.text(titulo);
    $progressBar.attr('aria-valuenow', valor);
    $progressBar.css('width', valor + "%");
    $progressBar.text(valor + '%');
  }
  , mostrarMensajeError = function(texto){
    $errorGeneral.text(texto);
    $errorGeneral.removeClass('d-none');

  }
  , mostrarMensajeInfo= function(texto){
    $infoGeneral.text(texto);
    $infoGeneral.removeClass('d-none');
  }
  , ocultarMensajes = function(){
    $errorGeneral.addClass('d-none');
    $infoGeneral.addClass('d-none');
  };



  window.definirEstadoProgreso = definirEstadoProgreso;
  window.mostrarMensajeError = mostrarMensajeError;
  window.mostrarMensajeInfo = mostrarMensajeInfo;
  window.ocultarMensajes = ocultarMensajes;
  $('.selectorFecha').datetimepicker({
    locale: 'es'
  });
  $('.selectMultiple,.selectSimple').selectpicker();
  $('.selectorSoloFecha, .selectorFechaOnly').datetimepicker({
    locale: 'es',
    format: 'DD/MM/YYYY'
  });
  $('.selectorSoloFechaMoment').datetimepicker({
    locale: 'es',
    format: 'DD/MM/YYYY'
  });
  $('.selectorSoloMes').datetimepicker({
    locale: 'es',
    format: 'MM/YYYY'
  });

  $('.selectorSoloFechaVinculado1').datetimepicker({
    locale: 'es',
    format: 'DD/MM/YYYY'
  });

  $('.selectorSoloFechaVinculado2').datetimepicker({
    useCurrent: false,
    locale: 'es',
    format: 'DD/MM/YYYY'
  });

  //selector de solo hora
  $('.selectorSoloHora').datetimepicker({
    locale: 'es',
    format: 'LT'
  });


  $(".selectorSoloFechaVinculado1").on("change.datetimepicker", function (e) {
    $('.selectorSoloFechaVinculado2').datetimepicker('minDate', e.date);
  });
  $(".selectorSoloFechaVinculado2").on("change.datetimepicker", function (e) {
    $('.selectorSoloFechaVinculado1').datetimepicker('maxDate', e.date);
  });


  $('.selectAutocomplete').select2({
    placeholder: {
      id: '0', // the value of the option
      text: 'Seleccione una Opción'
    },
     language: "es"
  });
  $('.selectAutocompleteNoDefault').select2({
    placeholder: {
      id: '-1', // the value of the option
      text: 'Seleccione una Opción'
    },
     language: "es"
  });
  $('.selectAutocompleteConDefault').select2({
    placeholder: {
      id: '0', // the value of the option
      text: 'Seleccione una Opción'
    },
    allowClear:true,
    language: "es"
  });

  //solo números!
  let inputs = document.querySelectorAll('.only-numeros');
  [].forEach.call(inputs, function(input){

    input.addEventListener('keypress', function(evt){
      let res = window.onlyNumeros(evt);
      return res;
    },false);
  });
  let inputsMoneda = document.querySelectorAll('.input-moneda');
  [].forEach.call(inputsMoneda, function(input){
    input.addEventListener('input', function(evt){
      this.value =  window.formatearMoneda(this.value);
    },false);
  });

}(jQuery));
