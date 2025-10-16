(function($) {
    $.fn.extend({
        dataTablesBMauco: function(opciones, arg) {
            if (opciones && typeof(opciones) == 'object') {
                opciones = $.extend({}, $.dataTablesBMauco.defaults, opciones);
            }

            // this creates a plugin for each element in
            // the selector or runs the function once per
            // selector.  To have it do so for just the
            // first element (once), return false after
            // creating the plugin to stop the each iteration
            this.each(function() {
                new $.dataTablesBMauco(this, opciones, arg);
            });
            return;
        }
    });
    /**
     * Plugin encargado de estandarizar datatables para Bosques del mauco,
     * incluyendo acciones de edición, deshabilitación y habilitación
     **/
    $.dataTablesBMauco = function(elemento, opciones, arg) {


        //Agregamos la referencia de la instancia de la función, a un
        //atributo del jQuery Element.
        $(elemento).data('dataTablesBMauco', this);

        //Funciones de uso comun

        /** En base a acciones disponibles, devuelve el String de un HTML Element
         * que contiene todas las acciones que deben ser agregadas a la ultima
         * columna del dataTables
         */
        var getDivAcciones = function(accionesDisponibles) {

                var $divFinal = this._$divAcciones,
                    $accionEdicion = this._$accionEdicion,
                    $accionDeshabilitar = this._$accionDeshabilitar,
                    $accionHabilitar = this._$accionHabilitar;

                for (var i = 0; i < accionesDisponibles.length; ++i) {

                    var accionActual = accionesDisponibles[i];

                    switch (accionActual.accion) {
                        case 'habilitar':
                            $divFinal.append($accionHabilitar);
                            break;
                        case 'deshabilitar':
                            $divFinal.append($accionDeshabilitar);
                            break;
                        case 'editar':
                            $divFinal.append($accionEdicion);
                            break;
                        default: $divFinal.append(this._getAccionCustom(accionActual));
                          break;
                    }

                }

                //Devolvemos el texto del HTML Element
                return $divFinal[0].outerHTML;

            },
            getAccionCustom= function(objAccion){
               var $divCustom = $('<a href="#"  class="btn  '+objAccion.id+'">'
                  +objAccion.accion+'</a>');

              //Agregamos la clase definida para el botón
              $divCustom.addClass(objAccion.styleClass);
              return $divCustom;
            } ,
            agregarListeners = function(accionesDisponibles) {
              for(var i=0; i < accionesDisponibles.length; ++i){

                var accionActual = accionesDisponibles[i];

                switch(accionActual.accion){

                  case 'habilitar': this._agregarListenerHabilitar(accionActual.url);
                      break;
                  case 'deshabilitar': this._agregarListenerDeshabilitar(accionActual.url);
                      break;
                  case 'editar': this._agregarListenerEditar(accionActual.url);
                    break;
                  default: this._agregarListenerCustom(accionActual);
                    break;
                }
              }
            },
             agregarListenerCustom= function(accion){

               //Agregamos el método custom al listener en base al id
               //proporcionado
               this._$this.on('click', 'tbody > tr a.' + accion.id, accion.metodo);
             }
             ,
            agregarListenerDeshabilitar = function(url) {
                var instanciaDataTables = this.dataTablesOriginal,
                    _nombreEntidad = this._opcionesPlugin.nombreEntidad;
                this._$this.on('click', 'tbody > tr  a.deshabilitarFila', function() {
                    var $tr = $(this).parents('tr'),
                        id = instanciaDataTables.row($tr).data().id,
                        deshabilitar = $.ajax({
                            type: 'POST',
                            url: url.replace('{id}',id),
                            dataType: 'json'
                        });
                    deshabilitar.done(function(resultado) {
                        var $resultadoDeshabilitarModal =
                            $('.resultadoDeshabilitar'),
                            $alertResultado =
                            $('.resultadoDeshabilitar .resultadoAjax');

                        $alertResultado.removeClass('alert-info');
                        $alertResultado.removeClass('alert-danger');
                        $alertResultado.empty();
                        if (resultado.ok) {
                            $alertResultado.addClass('alert-info');
                            $alertResultado.append(_nombreEntidad + ' Deshabilitado Exitosamente');
                        } else {
                            $alertResultado.addClass('alert-danger');
                            $alertResultado.append(resultado.error);
                        }
                        //Recargamos la tabla
                        instanciaDataTables.ajax.reload();
                        $resultadoDeshabilitarModal.modal('show');
                    });

                });
            },
            agregarListenerHabilitar = function(url) {
                var instanciaDataTables = this.dataTablesOriginal,
                    _nombreEntidad = this._opcionesPlugin.nombreEntidad;
                this._$this.on('click', 'tbody > tr  a.habilitarFila', function() {
                    var $tr = $(this).parents('tr'),
                        id = instanciaDataTables.row($tr).data().id,
                        habilitar = $.ajax({
                            type: 'POST',
                            url: url.replace('{id}',id),
                            dataType: 'json'
                        });
                    habilitar.done(function(resultado) {
                        var $resultadoHabilitarModal =
                            $('.resultadoHabilitar'),
                            $alertResultado =
                            $('.resultadoHabilitar .resultadoAjax');

                        $alertResultado.removeClass('alert-info');
                        $alertResultado.removeClass('alert-danger');
                        $alertResultado.empty();
                        if (resultado.ok) {
                            $alertResultado.addClass('alert-info');
                            $alertResultado.append(_nombreEntidad + ' Habilitado Exitosamente');
                        } else {
                            $alertResultado.addClass('alert-danger');
                            $alertResultado.append(resultado.error);
                        }
                        //Recargamos la tabla
                        instanciaDataTables.ajax.reload();
                        $resultadoHabilitarModal.modal('show');
                    });
                    return false;
                });
            },
            agregarListenerEditar = function(url) {
                var instanciaDataTables = this.dataTablesOriginal;
                this._$this.on('click', 'tbody > tr  a.editarFila', function() {
                    var $tr = $(this).parents('tr'),
                        id = instanciaDataTables.row($tr).data().id;
                    window.location.href = url.replace('{id}',id);
                    return false;
                });
            },
            //recarga datables una vez ue fue efectuada una modificación
            refrescar = function(){
              let instanciaDataTables = this.dataTablesOriginal;
              instanciaDataTables.ajax.reload();
        };
        this.refrescar = refrescar;
        this._agregarListenerEditar = agregarListenerEditar;
        this._agregarListenerCustom = agregarListenerCustom;
        this._agregarListenerHabilitar = agregarListenerHabilitar;
        this._agregarListenerDeshabilitar = agregarListenerDeshabilitar;

        this._getDivAcciones = getDivAcciones;
        this._getAccionCustom = getAccionCustom;
        //Definimos las opciones básicas y valores por default del plugin.
        //Definimos div que será agregado en las columnas de accion de la tabla.
        var _$divAcciones = $('<div class="btn-group" role="group"' + 'aria-label="..."></div>'),
            _$accionEdicion = $('<a href="#" class="editarFila' + ' btn btn-warning">Editar</a>'),
            _$accionDeshabilitar = $('<a href="#" data-toggle="modal"' + ' data-target=".resultadoDeshabilitar" ' + 'class="deshabilitarFila btn btn-danger">Desactivar</a>'),

            _$accionHabilitar = $('<a href="#" data-toggle="modal"' + ' data-target=".resultadoHabilitar" ' + 'class="habilitarFila btn btn-success">Habilitar</a>'),
            _opcionesPlugin = opciones,
            columnasFinales = opciones.columnas;

        //Agregamos a las columnas, la columna de acciones

        this._$this = $(elemento);
        this._$divAcciones = _$divAcciones;
        this._$accionEdicion = _$accionEdicion;
        this._$accionDeshabilitar = _$accionDeshabilitar;
        this._$accionHabilitar = _$accionHabilitar;
        this._opcionesPlugin = _opcionesPlugin;

        this._$this.dataTablesBMauco = this;
        this._opcionesPlugin.columnaAcciones.defaultContent =
            getDivAcciones.call(this, this._opcionesPlugin.accionesDisponibles);


        //Agregamos la columna de acciones al arreglo de columnas, solo
        //si es que tiene alguna acción.
        if(this._opcionesPlugin.accionesDisponibles.length > 0){
          columnasFinales.push(this._opcionesPlugin.columnaAcciones);
        }
        $.extend(this._opcionesPlugin,{columns:columnasFinales});
        this.dataTablesOriginal = this._$this.DataTable(this._opcionesPlugin);

        //Agregamos listeners de accion.
        agregarListeners.call(this, this._opcionesPlugin.accionesDisponibles);

    };

    $.dataTablesBMauco.defaults = {
        processing: true,
        serverSide: true,
        language: {
            url: "translate/dataTables.spanish.json"
        },
        columnaAcciones: {
            data: null,
            className: "center",
            defaultContent: undefined
        },
        accionesDisponibles: [{
            accion: 'habilitar',
            url: 'entidad/id/habilitar'
        }, {
            accion: 'deshabilitar',
            url: 'entidad/id/deshabilitar'
        }, {
            accion: 'editar',
            url: 'entidad/id/editar'
        }],
        nombreEntidad: 'Entidad',

        //Valores obligatorios para que funcione datatables,
        //ajax= accion a realizar
        //columnas = equivalente a atributo columns.
        ajax: undefined,
        columnas: undefined
    };
}(jQuery));
