window.anularVacaciones = function(idEliminar,bMaucoDataTables){
  let ajaxAnular = $.ajax({
    url: 'vacaciones/anular',
    type: 'POST',
    data:{id:idEliminar}
  });
  ajaxAnular.done(function(){
    window.mostrarMensajeInfo('Vacaciones anuladas');
    //recargamos datatables
    bMaucoDataTables.refrescar();
  });
};
$(function() {

  $('#vacaciones-table').dataTablesBMauco({
    ajax: 'vacaciones/getAjaxData',
    columnas: [{
      data: 'rut_empleado',
      name: 'rut_empleado'
    }, {
      data: 'nombre_empleado',
      name: 'nombre_empleado'
    }, {
      data: 'fecha_registro',
      name: 'fecha_registro'
    },{
      data: 'fecha_inicio',
      name: 'fecha_inicio'
    }, {
      data: 'cantidad_dias',
      name: 'cantidad_dias'
    }, {
      data: 'fecha_termino',
      name: 'fecha_termino'
    }],
    accionesDisponibles: [{
      accion: 'Descargar',
      styleClass:'btn-dark',
      id: 'descargar',
      url: 'vacaciones/download/{id}',
      metodo: function(){
            var instanciaDataTables = $(this).parents("table")
                  .data().dataTablesBMauco.dataTablesOriginal,
                $tr = $(this).parents('tr'),
                id = instanciaDataTables.row($tr).data().id;
            window.location.href = "vacaciones/download/"+id;
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
        window.anularVacaciones(id, bMaucoDataTables);

      }
    }],
    nombreEntidad: 'Vacacion',
    language: {
      url: "vendor/translate/dataTables.spanish.json"
    }
  });

});
