@extends('master', [
  'menuActual' => 'buscador'])

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> Buscador de Trabajadores
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" id="termino_busqueda" 
                                       placeholder="Buscar por RUT, nombre, apellido o email..." 
                                       autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btn_buscar">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Ingrese al menos 2 caracteres para buscar
                            </small>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="tipo_busqueda">
                                <option value="todos">Buscar en Todos</option>
                                <option value="empleados">Solo Empleados</option>
                                <option value="trabajadores">Solo Trabajadores (Veritas)</option>
                            </select>
                        </div>
                    </div>

                    <div id="mensaje_busqueda" class="alert" style="display:none;"></div>
                    
                    <div id="loading_busqueda" style="display:none;" class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p>Buscando...</p>
                    </div>

                    <div id="resultados_container" style="display:none;">
                        <h5>Resultados de la búsqueda (<span id="cantidad_resultados">0</span>)</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="tabla_resultados">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>RUT</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Área</th>
                                        <th>Comuna</th>
                                        <th>Dirección</th>
                                        <th>Fecha Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_resultados">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="sin_resultados" style="display:none;" class="text-center text-muted py-5">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <p>Utilice el buscador para encontrar empleados o trabajadores</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('javascript')
<script>
$(document).ready(function() {
    // Event delegation para las filas clikeables
    $(document).on('click', '.clickable-row', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var url = $(this).data('url');
        console.log('Click detected! URL:', url); // Debug
        
        if (url) {
            console.log('Redirecting to:', url);
            setTimeout(function() {
                window.location.href = url;
            }, 100);
        } else {
            console.error('No URL found in data-url attribute');
        }
    });

    // Efecto hover para las filas
    $(document).on('mouseenter', '.clickable-row', function() {
        $(this).addClass('table-active');
    }).on('mouseleave', '.clickable-row', function() {
        $(this).removeClass('table-active');
    });

    // Buscar al presionar Enter
    $('#termino_busqueda').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarTrabajadores();
        }
    });

    // Buscar al hacer clic en el botón
    $('#btn_buscar').on('click', function() {
        buscarTrabajadores();
    });

    function buscarTrabajadores() {
        var termino = $('#termino_busqueda').val().trim();
        var tipo = $('#tipo_busqueda').val();

        // Validar término de búsqueda
        if (termino.length < 2) {
            mostrarMensaje('Por favor ingrese al menos 2 caracteres para buscar', 'warning');
            return;
        }

        // Mostrar loading
        $('#loading_busqueda').show();
        $('#resultados_container').hide();
        $('#sin_resultados').hide();
        $('#mensaje_busqueda').hide();

        // Realizar búsqueda
        $.ajax({
            url: '{{ url("buscador-trabajadores/buscar") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                termino: termino,
                tipo: tipo
            },
            success: function(response) {
                $('#loading_busqueda').hide();
                
                if (response.success) {
                    if (response.data.length > 0) {
                        mostrarResultados(response.data);
                        mostrarMensaje(response.message, 'success');
                    } else {
                        $('#sin_resultados').html(
                            '<i class="fas fa-exclamation-circle fa-3x mb-3 text-warning"></i>' +
                            '<p>No se encontraron resultados para "' + termino + '"</p>'
                        ).show();
                        mostrarMensaje('No se encontraron resultados', 'info');
                    }
                } else {
                    mostrarMensaje(response.message, 'danger');
                    $('#sin_resultados').show();
                }
            },
            error: function(xhr, status, error) {
                $('#loading_busqueda').hide();
                mostrarMensaje('Error al realizar la búsqueda: ' + error, 'danger');
                $('#sin_resultados').show();
            }
        });
    }

    function mostrarResultados(resultados) {
        var tbody = $('#tbody_resultados');
        tbody.empty();

        console.log('=== DEBUG COMPLETO ===');
        console.log('Resultados recibidos:', JSON.stringify(resultados, null, 2)); // Debug completo

        $.each(resultados, function(index, resultado) {
            console.log('-------------------');
            console.log('Procesando índice:', index);
            console.log('Objeto resultado completo:', resultado);
            console.log('ID del resultado:', resultado.id);
            console.log('Tipo del resultado:', resultado.tipo);
            console.log('Nombre del resultado:', resultado.nombre);
            
            var tipo_url = resultado.tipo === 'Empleado' ? 'empleado' : 'trabajador';
            console.log('tipo_url calculado:', tipo_url);
            
            var url_base = '{{ url("buscador-trabajadores") }}';
            console.log('URL base:', url_base);
            
            var url_detalle = url_base + '/' + tipo_url + '/' + resultado.id;
            console.log('URL detalle COMPLETA:', url_detalle);
            
            var row = '<tr class="clickable-row" data-url="' + url_detalle + '" style="cursor: pointer;">' +
                '<td><span class="badge badge-' + 
                (resultado.tipo === 'Empleado' ? 'primary' : 'info') + '">' + 
                resultado.tipo + '</span></td>' +
                '<td>' + resultado.rut + '</td>' +
                '<td><strong>' + resultado.nombre + '</strong></td>' +
                '<td>' + resultado.email + '</td>' +
                '<td>' + resultado.area + '</td>' +
                '<td>' + resultado.comuna + '</td>' +
                '<td>' + resultado.direccion + '</td>' +
                '<td>' + resultado.fecha_ingreso + '</td>' +
                '</tr>';
            tbody.append(row);
            
            console.log('Fila HTML creada (primeros 100 chars):', row.substring(0, 100));
        });

        console.log('=== FIN DEBUG ===');
        console.log('Total filas creadas:', resultados.length);
        $('#cantidad_resultados').text(resultados.length);
        $('#resultados_container').show();
    }

    function mostrarMensaje(mensaje, tipo) {
        var alertClass = 'alert-' + tipo;
        $('#mensaje_busqueda')
            .removeClass('alert-success alert-danger alert-warning alert-info')
            .addClass(alertClass)
            .html('<i class="fas fa-info-circle"></i> ' + mensaje)
            .fadeIn()
            .delay(5000)
            .fadeOut();
    }

    // Mostrar mensaje inicial
    $('#sin_resultados').show();
});
</script>
@endpush
