$(function() {
      $('#usuarios-table').dataTablesBMauco({
        ajax:'../usuario/deshabilitados/getAjaxData',
        columnas:[
      { data: 'name', name: 'name' },
      { data: 'email', name: 'email' },
      { data: 'created_at', name: 'created_at' },
      { data: 'updated_at', name: 'updated_at' }],
        accionesDisponibles:[{
          accion:'habilitar',
          url: '../usuario/{id}/habilitar'
        }],
        nombreEntidad: 'Usuario',
        language:{
            url: "../translate/dataTables.spanish.json"
        }
      });
});
