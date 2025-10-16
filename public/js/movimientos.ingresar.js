window.anularMovimientos = function(idEliminar,bMaucoDataTables){
  let ajaxAnular = $.ajax({
    url: 'movimientos/anular',
    type: 'POST',
    data:{id:idEliminar}
  });
  ajaxAnular.done(function(){
    window.mostrarMensajeInfo('Movimiento anulado');
    //recargamos datatables
    bMaucoDataTables.refrescar();
  });
};
$(function() {

  $("body").on("change", '.tipo_movimiento_select', function(){
    var seleccionado = $(this).val();
    if(seleccionado == 'AT'){ //si es atraso se muestran los divs de horas
      $(".div_hora").removeClass("d-none");
      $(".div_cantidad").addClass("d-none");
    } else {
      $(".div_hora").addClass("d-none");
      $(".div_cantidad").removeClass("d-none");
    }
  });

  $("body").on("input", ".cantidad_dias,.fecha_inicio", function(){
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
    })
  });

  $('#movimientos-table').dataTablesBMauco({
    ajax: 'movimientos/getAjaxData',
    processing: true,
    //Hacemos que muestre el botón descargar solo los movimientos que sean
    //descargables, para el resto ocultamo
    initComplete: function(settings, json){
      let dataTablesOriginal = this.data().dataTablesBMauco.dataTablesOriginal
        , filas = dataTablesOriginal.rows()[0]
      ;
      for(let i=0; i < filas.length; ++i){
         let  fila = dataTablesOriginal.row(filas[i]);
         if(fila.data().descargable == 0){
           let btnDescarga = fila.node().querySelector('.descargar');
           //Eliminamos elemento del DOM
           btnDescarga.parentNode.removeChild(btnDescarga);
         }
      }

    },
    columnas: [{
      data: 'rut_empleado',
      name: 'rut_empleado'
    }, {
      data: 'nombre_empleado',
      name: 'nombre_empleado'
    }, {
      data: 'tipo_texto',
      name: 'tipo_texto'
    },{
      data: 'area',
      name: 'area'
    }, {
      data: 'fecha_inicio_movimiento_proc',
      name: 'fecha_inicio_movimiento_proc'
    }, {
      data: 'fecha_termino_movimiento_proc',
      name: 'fecha_termino_movimiento_proc'
    }],
    accionesDisponibles: [{
      accion: 'Descargar',
      styleClass:'btn-dark',
      id: 'descargar',
      url: 'movimientos/download/{id}',
      metodo: function(){
            var instanciaDataTables = $(this).parents("table")
                  .data().dataTablesBMauco.dataTablesOriginal,
                $tr = $(this).parents('tr'),
                id = instanciaDataTables.row($tr).data().id;
            window.location.href = "movimientos/download/"+id;
            return false;
      }
    },{
      accion:'Anular',
      styleClass: 'btn-danger',
      id: 'anular-btn',
      metodo: function(){
        let $tr = $(this).parents('tr'),
        bMaucoDataTables = $tr.parents('table')
           .data()
           .dataTablesBMauco,
        dt = bMaucoDataTables.dataTablesOriginal,
        id = dt.row($tr).data().id;
        window.anularMovimientos(id, bMaucoDataTables);

      }
    }],
    nombreEntidad: 'MovimientoAsistenciaExport',
    language: {
      url: "vendor/translate/dataTables.spanish.json"
    }
  });

});
