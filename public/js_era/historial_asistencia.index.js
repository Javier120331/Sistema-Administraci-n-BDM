//utilizamos el plugin original de datatables, ya que esta construido
$(document).ready(function () {
    $('#movimientos-table').DataTable({
        language: {
            url: "../vendor/translate/dataTables.spanish.json"
        }
    });
});