$(function() {
    $('#usuarios-table').dataTablesBMauco({
        ajax: 'usuario/getAjaxData',
        columnas: [{
            data: 'name',
            name: 'name'
        }, {
            data: 'email',
            name: 'email'
        }, {
            data: 'created_at',
            name: 'created_at'
        }, {
            data: 'updated_at',
            name: 'updated_at'
        }],
        accionesDisponibles: [{
            accion: 'editar',
            url: 'usuario/{id}/edit'
        },{
            accion: 'deshabilitar',
            url: 'usuario/{id}/deshabilitar'
        }],
        nombreEntidad: 'Usuario',
        language: {
            url: "vendor/translate/dataTables.spanish.json"
        }
    });
});
