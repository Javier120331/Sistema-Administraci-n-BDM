$(function($){

  $.fn.extend({
    inputTable:function(opciones, args){
      if (opciones && typeof(opciones) == 'object') {
          opciones = $.extend({}, $.inputTable.defaults, opciones);
      }

      // this creates a plugin for each element in
      // the selector or runs the function once per
      // selector.  To have it do so for just the
      // first element (once), return false after
      // creating the plugin to stop the each iteration
      this.each(function() {
          new $.inputTable(this, opciones, args);
      });
      return;
    }
  });

  $.inputTable = function(elemento, opciones, args){

    var _getTablaInputs = function(columnas){

      var $nuevaTabla = $('<table class="table table-bordered table-hover"></table>')
        , $trHead = undefined
        , $nuevaColumna = undefined
      ;

      $nuevaTabla.append($('<thead><tr></tr></thead>'));

      $trHead = $nuevaTabla.find('thead > tr');

      for(var i=0; i < columnas.length; ++i){
        $nuevaColumna = $('<th>'+columnas[i]+'</th>');
        $trHead.append($nuevaColumna);
      }

      return $nuevaTabla;
    }, _getInput= function(tipoInput, atributosInput){
        return $('<input type="'
          +tipoInput+'" '+atributosInput
          +' class="form-control"  />');
    }
    , _agregarValoresColumna= function ($tablaInputs, valores
        , tipoInput, atributosInput){
        var $tBody = $('<tbody></tbody>')
          , $trNuevo = undefined
          , $tdNuevo = undefined
          , $inputNuevo = undefined
          , arregloObjetos = []
        ;
        $tablaInputs.append($tBody);

        for(var i=0; i < valores.length; ++i){
          $trNuevo = $('<tr></tr>');
          $trNuevo.append('<td>'+valores[i].texto+'</td>');
          $inputNuevo = _getInput(tipoInput, atributosInput);
          $tdNuevo = $('<td></td>');
          $tdNuevo.append($inputNuevo);
          $trNuevo.append($tdNuevo);
          $tBody.append($trNuevo);
          arregloObjetos.push({
            valor: valores[i],
            inputRelacionado: $inputNuevo
          });
        }
      $tablaInputs.append($tBody);

      return arregloObjetos;
    }
    , valores = opciones.valores
    , columnas = opciones.columnas
    , tipoInput = opciones.tipoInput
    , atributosInput = opciones.atributosInput
    , $tablaInputs = _getTablaInputs(columnas)
    , $elemento = $(elemento)
    , arregloValores = undefined
    ;

    this._$tablaInputs = $tablaInputs;


    $elemento.empty();
    $elemento.append($tablaInputs);

    arregloValores = _agregarValoresColumna($tablaInputs, valores
      , tipoInput, atributosInput );


    this._values = arregloValores;
    this.destroy = function(){
      $elemento.empty();
      $elemento.removeData();
    };

    this.val = function(incluirInputs = true){
        var valoresFinales = []
          , valorSinProcesar = this._values
          , objeto = undefined
        ;
        for(var i=0; i < valorSinProcesar.length; ++i){
          objeto = new Object();
          objeto.id=valorSinProcesar[i].valor.id;
          objeto.texto = valorSinProcesar[i].valor.texto;
          objeto.valor = valorSinProcesar[i].inputRelacionado.val();
          if(incluirInputs){
            objeto.$inputValor = valorSinProcesar[i].inputRelacionado;
          }
          valoresFinales.push(objeto);
        }
      return valoresFinales;
    };

    //Agregamos la referencia de la instancia de la función, a un
    //atributo del jQuery Element.
    $elemento.data('inputTable', this);

  };

  $.inputTable.defaults={
      valores:[],
      columnas: ['Numero de Cámaras', 'Metros Cuadrados'],
      tipoInput:'number',
      atributosInput: 'step="any" min="1"'
  }


}(jQuery));
