//utilizamos el plugin original de datatables, ya que esta construido
$(document).ready(function () {
    $("#movimientos-table").DataTable({
        language: {
            url: "../vendor/translate/dataTables.spanish.json",
        },
    });

    // Activar búsqueda por nombre en el select de Trabajador usando Select2
    if ($ && $.fn && $.fn.select2) {
        var $select = $(".selectAutocomplete.empleado");
        if ($select.length && !$select.hasClass("select2-hidden-accessible")) {
            $select.select2({
                placeholder: "Escribe el nombre del trabajador",
                language: "es",
                width: "100%",
                allowClear: true,
                matcher: function (params, data) {
                    if ($.trim(params.term) === "") {
                        return data;
                    }
                    if (typeof data.text === "undefined") {
                        return null;
                    }
                    var term = params.term.toString().toLowerCase();
                    var text = data.text.toString().toLowerCase();
                    try {
                        term = term
                            .normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        text = text
                            .normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                    } catch (e) {}
                    return text.indexOf(term) > -1 ? data : null;
                },
            });
        }
    }
});
