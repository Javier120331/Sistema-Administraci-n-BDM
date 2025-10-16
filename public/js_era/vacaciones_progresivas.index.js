$(function() {

  $('#progresivas-table').dataTablesBMauco({
    ajax: 'getAjaxDataProgresivas',
    columnas: [{
      data: 'rut_empleado',
      name: 'rut_empleado'
    }, {
      data: 'nombre_empleado',
      name: 'nombre_empleado'
    }, {
      data: 'fecha_proc',
      name: 'fecha_proc'
    },{
      data: 'cantidad_anios',
      name: 'cantidad_anios'
    }],
    accionesDisponibles: [],
    nombreEntidad: 'VacacionProgresiva',
    language: {
      url: "../vendor/translate/dataTables.spanish.json"
    }
  });

});
