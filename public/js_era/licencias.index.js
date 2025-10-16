window.anularLicencia = function(idEliminar,bMaucoDataTables){
  let ajaxAnular = $.ajax({
    url: 'licencias/anular',
    type: 'POST',
    data:{id:idEliminar}
  });
  ajaxAnular.done(function(){
    window.mostrarMensajeInfo('Licencia Anulada');
    //recargamos datatables
    bMaucoDataTables.refrescar();
  });
};
window.confirmarAnularLicencia = function(idEliminar,bMaucoDataTables){
   let $modalConfirmacion = $('.resultadoAnular');
   let $btnConfirmar = $modalConfirmacion.find('.confirmarBtn');
   let $spanTexto = $('<span>');
   $spanTexto.text("El anular una licencia médica es un proceso crítico"
    +" que puede dar problemas administrativos a posterior.");
   $spanTexto.addClass('text-danger');
   $btnConfirmar.data('idEliminar', idEliminar);
   $btnConfirmar.data('bMaucoDataTables', bMaucoDataTables);

   $modalConfirmacion.find('.confirmarTexto').html($spanTexto);
   $btnConfirmar.on('click', function(){
      let $this = $(this);
      let idEliminar = $this.data('idEliminar');
      let bMaucoDataTables = $this.data('bMaucoDataTables');
      window.anularLicencia(idEliminar, bMaucoDataTables);
      $('.resultadoAnular').modal('hide');
   });
   $modalConfirmacion.modal('show');
};
$(function() {

  document
    .querySelector("#btn_fintro_licencia")
    .addEventListener("click", function(){
        let licenciasTabla = $('#licencias-table').data()
          .dataTablesBMauco.dataTablesOriginal;
        let nuevaUrl ='licencias/getAjaxData?causa='
          + document.querySelector(".causaSelect").value
          + "&estado=" + document.querySelector(".filtroEstado").value
          + "&tipo=" + document.querySelector(".tipoSelect").value;
        licenciasTabla.ajax.url(nuevaUrl).load();
        window.ocultarMensajes();
    });

      $('#licencias-table').dataTablesBMauco({
        ajax: 'licencias/getAjaxData',
        columnas: [{
          data: 'rut_empleado',
          name: 'rut_empleado'
        }, {
          data: 'nombre_empleado',
          name: 'nombre_empleado'
        }, {
          data: 'causa_licencia',
          name: 'causa_licencia'
        }, {
          data: 'tipo_licencia',
          name: 'tipo_licencia'
        },{
          data: 'fecha_inicio_proc',
          name: 'fecha_inicio_proc'
        }, {
          data: 'cant_dias',
          name: 'cant_dias'
        }, {
          data: 'fecha_termino_proc',
          name: 'fecha_termino_proc'
        }],
        accionesDisponibles: [{
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
            window.confirmarAnularLicencia(id, bMaucoDataTables);

          }
        }],
        nombreEntidad: 'Licencia',
        language: {
          url: "vendor/translate/dataTables.spanish.json"
        }
      });
});
