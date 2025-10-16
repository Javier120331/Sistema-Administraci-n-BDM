window.anularFiniquito= function(idEliminar,bMaucoDataTables){
  let ajaxAnular = $.ajax({
    url: 'finiquitos/anular',
    type: 'POST',
    data:{id:idEliminar}
  });
  ajaxAnular.done(function(respuesta){
    if(respuesta.resultado){
      window.mostrarMensajeInfo('Finiquito Anulado');
    }else {
      window.mostrarMensajeError("Ha ocurrido un error al anular Finiquito");
    }
    //recargamos datatables
    bMaucoDataTables.refrescar();
  });
};
window.confirmarAnularFiniquito = function(idEliminar,bMaucoDataTables){
   let $modalConfirmacion = $('.resultadoAnular');
   let $btnConfirmar = $modalConfirmacion.find('.confirmarBtn');
   let $spanTexto = $('<span>');
   $spanTexto.text("El anular un finiquito es un proceso crítico"
    +" que puede dar problemas administrativos a posterior.");
   $spanTexto.addClass('text-danger');
   $btnConfirmar.data('idEliminar', idEliminar);
   $btnConfirmar.data('bMaucoDataTables', bMaucoDataTables);

   $modalConfirmacion.find('.confirmarTexto').html($spanTexto);
   $btnConfirmar.on('click', function(){
      let $this = $(this);
      let idEliminar = $this.data('idEliminar');
      let bMaucoDataTables = $this.data('bMaucoDataTables');
      window.anularFiniquito(idEliminar, bMaucoDataTables);
      $('.resultadoAnular').modal('hide');
   });
   $modalConfirmacion.modal('show');
};
$(function() {

      $('#finiquitos-table').dataTablesBMauco({
        ajax: 'finiquitos/getAjaxData',
        columnas: [{
          data: 'rut_empleado',
          name: 'rut_empleado'
        }, {
          data: 'nombre_empleado',
          name: 'nombre_empleado'
        }, {
          data: 'fecha_inicio_contrato_proc',
          name: 'fecha_inicio_contrato_proc'
        }, {
          data: 'fecha_finiquito_proc',
          name: 'fecha_finiquito_proc'
        },{
          data: 'causa_finiquito',
          name: 'causa_finiquito'
        }, {
          data: 'fecha_documento_proc',
          name: 'fecha_documento_proc'
        }],
        accionesDisponibles: [{
          accion: 'Descargar',
          styleClass:'btn-dark',
          id: 'descargar',
          url: 'finiquitos/download/{id}',
          metodo: function(){
              var instanciaDataTables = $(this).parents("table")
                  .data().dataTablesBMauco.dataTablesOriginal,
                $tr = $(this).parents('tr'),
                id = instanciaDataTables.row($tr).data().id;
            window.location.href = "finiquitos/download/"+id;
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
          window.confirmarAnularFiniquito(id, bMaucoDataTables);

        }
      }],
        nombreEntidad: 'Finiquito',
        language: {
          url: "vendor/translate/dataTables.spanish.json"
        }
      });
});
