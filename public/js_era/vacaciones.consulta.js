
$(function() {

    window.cargarTabla = function(idEmpleado){
      let tabla = document.querySelector('#consulta-table');
      let thead = tabla.querySelector('thead').cloneNode(true);
      let cardBody = tabla.closest('.card-body');
      cardBody.innerHTML = '';
      let row = cardBody.closest('.row');
      row.classList.add('d-none');
      tabla = document.createElement('table');
      tabla.classList.add('table','table-striped','table-bordered'
        , 'dataTable', 'no-footer');
      tabla.id = 'consulta-table';
      tabla.appendChild(thead);
      cardBody.appendChild(tabla);

      $(tabla).dataTablesBMauco({
        ajax: 'getAjaxDataConsulta?idEmpleado='+idEmpleado,
        order: [[0,'desc']],//ordenamos descendente por periodo
        columnas: [{
          data: 'nombre',
          name: 'nombre'
        }, {
          data: 'base',
          name: 'base'
        }, {
          data: 'progresivas',
          name: 'progresivas'
        },{
          data: 'dias_autorizados',
          name: 'dias_autorizados'
        }, {
          data: 'dias_disponibles',
          name: 'dias_disponibles'
        }],
        accionesDisponibles: [],
        nombreEntidad: 'Periodo',
        language: {
          url: "../vendor/translate/dataTables.spanish.json"
        }
      });
      row.classList.remove('d-none');
    };
    window.cargarDatosEmpleado = function(idEmpleado){
       let $ajax = $.ajax({
         type:'GET',
         data:{idEmpleado:idEmpleado},
         url: 'getAjaxDataEmpleado'
       });
       $ajax.done(function(resp){
          document.querySelector('.rut_empleado_span').innerText
            = resp.empleado.rut;
          document.querySelector('.nombre_empleado_span').innerText
              = resp.empleado.nombre;
          document.querySelector('.fecha_contrato_span').innerText
            = new moment(resp.empleado.fecha_inicio_contrato.date)
              .format('DD/MM/YYYY');
          document.querySelector('.cargo_span').innerText
              = resp.cargo.nombre;
          document.querySelector('.progresivas_span').innerText
                  = resp.progresivas?resp.progresivas.cantidad_anios:0;
       });
    };
    document.querySelector('.consultar-btn').addEventListener('click'
      , function(){
        let empleadoSeleccionado = document.querySelector('.empleado').value;
        window.cargarDatosEmpleado(empleadoSeleccionado);
        window.cargarTabla(empleadoSeleccionado);
    });
});
