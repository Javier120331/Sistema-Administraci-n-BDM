$(
    (function () {
        // API de feriados de Chile
        window.feriadosUrl = "https://www.feriadosapp.com/api/holidays.json";

        window.solicitarEliminacionFeriado = function (feriado) {
            window.feriadoAEliminar = feriado;
            $("#feriado_eliminado_fecha").text(feriado.fecha);
            $("#feriado_eliminado_descripcion").text(feriado.descripcion);
            $("#eliminarFeriadoModal").modal("show");
        };

        /**
         * Configura el datepicker para agregar un nuevo feriado, evitando
         * que permita agregar las fechas que ya poseen un feriado definido
         * @param  {Array} eventos Arreglo de eventos con la estructura
         * {
         *    start: fecha,
         *    title: titulo del feriado,
         *    allDay: true
         * }
         * @return void
         */
        window.configurarDatePickerAgregar = function (eventos) {
            let d = $(".agregar_feriados_date").data().datetimepicker;
            let fechasNoDisponibles = [];
            for (let i = 0; i < eventos.length; ++i) {
                let fecha = new moment(eventos[i].start);
                fechasNoDisponibles.push(fecha);
            }
            d.disabledDates(false);
            d.disabledDates(fechasNoDisponibles);
        };

        window.recargarEventosCalendario = function (eventos) {
            window.calendario.data().fullCalendar.removeEvents();
            window.calendario.data().fullCalendar.addEventSource(eventos);
            window.configurarDatePickerAgregar(eventos);
        };
        /**
         * Genera eventos para fullcalendar en base a feriados
         * @param  {Array} listaFeriados lista de feriados con la estructura
         * descrita en base al modelo Feriado
         * @return {Array} Arreglo con la estructura:
         * {
         *   title: nombre del feriado,
         *   start: fecha del feriado,
         *   allDay: true
         * }
         */
        window.getFeriadosCalendarEvents = function (listaFeriados) {
            let arregloEventos = [];
            for (let i = 0; i < listaFeriados.length; ++i) {
                let evento = {
                    title: listaFeriados[i].descripcion,
                    start: listaFeriados[i].fecha,
                    allDay: true,
                };
                arregloEventos.push(evento);
            }
            return arregloEventos;
        };
        window.cargarFeriadosExistentes = function () {
            var ajax = $.ajax({
                type: "GET",
                url: "feriados/get",
            });
            ajax.done(function (respuesta) {
                let eventos = window.getFeriadosCalendarEvents(respuesta);
                window.recargarEventosCalendario(eventos);
            });
        };
        window.enviarFeriados = function (feriados, anio) {
            var ajax = $.ajax({
                type: "POST",
                url: "feriados/importar",
                data: {
                    feriados: feriados,
                    anio: anio,
                },
            });
            ajax.done(function (respuesta) {
                $(".mensaje_feriados").empty();
                $("#cargandoModal").modal("hide");
                if (respuesta.resultado == true) {
                    $(".mensaje_feriados").append(
                        "<span class='alert alert-primary'>Importación Realizada</span>"
                    );
                    window.cargarFeriadosExistentes();
                } else {
                    $(".mensaje_feriados").append(
                        "<span class='alert alert-danger'>Ha Ocurrido un error</span>"
                    );
                }
            });
        };
        window.obtenerFeriados = function (data, anio) {
            var arreglo = [];
            for (var i = 0; i < data.length; i++) {
                var feriadoActual = data[i],
                    anioFeriado = feriadoActual.date.split("-")[0];
                if (anioFeriado == anio) {
                    arreglo.push(feriadoActual);
                }
            }
            return arreglo;
        };

        window.agregarFeriado = function (fecha, descripcion) {
            let ajax = $.ajax({
                type: "POST",
                url: "feriados/store",
                data: {
                    fecha: fecha,
                    descripcion: descripcion,
                },
            });
            ajax.done(function (respuesta) {
                $(".mensaje_feriados").empty();
                if (respuesta.resultado == true) {
                    $(".mensaje_feriados").append(
                        "<span class='alert alert-primary'>Dia Festivo Agregado</span>"
                    );
                    window.cargarFeriadosExistentes();
                } else {
                    $(".mensaje_feriados").append(
                        "<span class='alert alert-warning'>" +
                            respuesta.mensaje +
                            "</span>"
                    );
                }
            });
            ajax.fail(function (respuesta) {
                $(".mensaje_feriados").empty();
                $(".mensaje_feriados").append(
                    "<span class='alert alert-danger'>Error al procesar solicitud</span>"
                );
            });
        };

        window.calendario = $("#feriados_calendar").fullCalendar({
            defaultView: "month",
            lang: "es",
            eventClick: function (calEvent, jsEvent, view) {
                let feriado = {
                    fecha: calEvent.start.format("DD/MM/YYYY"),
                    descripcion: calEvent.title,
                };
                window.solicitarEliminacionFeriado(feriado);
            },
        });

        $("body").on("click", "#agregar_feriado_btn", function () {
            let fecha = window.fechaAgregar,
                descripcion = $("#feriado_agregar_descripcion").val().trim(),
                $mensajes = $(".mensajes_agregar_feriado");
            $mensajes.empty();
            if (descripcion == "") {
                $mensajes.append(
                    "<span class='alert alert-warning'>Debe ingresar una descripción</span>"
                );
            } else {
                window.agregarFeriado(fecha, descripcion);
                $("#feriado_agregar_descripcion").val("");
                $("#agregarFeriadoModal").modal("hide");
            }
        });

        $("body").on("click", "#mostrar_agregar_feriados_btn", function () {
            window.fechaAgregar = $(".nuevo_feriado").val();
            $("#feriado_agregar_fecha").text(window.fechaAgregar);
            $("#agregarFeriadoModal").modal("show");
        });

        $("body").on("click", "#eliminar_feriado_btn", function () {
            var ajax = $.ajax({
                type: "POST",
                data: {
                    fecha: window.feriadoAEliminar.fecha,
                },
                url: "feriados/delete",
            });
            ajax.done(function (respuesta) {
                window.cargarFeriadosExistentes();
            });
        });

        $("body").on("change", ".anio_select", function () {
            var fechaSeleccionada = $.fullCalendar.moment(
                $(this).val() + "-01-01"
            );
            $("#feriados_calendar")
                .data()
                .fullCalendar.view.setDate(fechaSeleccionada);
        });

        $("body").on("click", "#importar_feriados_btn", function () {
            $("#cargandoModal")
                .modal("show")
                .on("shown.bs.modal", function () {
                    var ajax = $.ajax({
                        type: "GET",
                        url: window.feriadosUrl,
                    });
                    ajax.done(function (respuesta) {
                        var feriados = window.obtenerFeriados(
                            respuesta.data,
                            $(".anio_select").val()
                        );
                        window.enviarFeriados(
                            feriados,
                            $(".anio_select").val()
                        );
                    });
                    ajax.fail(function (respuesta) {
                        $(".mensaje_feriados").empty();
                        $("#cargandoModal").modal("hide");
                        $(".mensaje_feriados").append(
                            "<span class='alert alert-danger'>Error al obtener feriados. Verifique su conexión a internet.</span>"
                        );
                    });
                });
        });
        window.cargarFeriadosExistentes();
    })(jQuery)
);
