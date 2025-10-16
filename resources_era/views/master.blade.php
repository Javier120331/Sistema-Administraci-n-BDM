<!DOCTYPE html>
<html lang="es">

@include('templates._head')
<body>

  @include('templates/_navbar')
  <main class="container-fluid pt-3">

    {!! Form::hidden('token',csrf_token(), ['id'=> 'token']) !!}
    @if(Auth::check())
      <div class="row">
        <div class="mx-auto col-lg-7 col-sm-12 col-md-12 tituloHeader">
          <span> Sistema de Administración de Remuneraciones</span>
        </div>
        <div class="mx-auto col-lg-9 col-sm-3 col-md-3">
          @if(isset($menuActual))
            <div class="menuNavegacion">
              {!! Breadcrumbs::render($menuActual) !!}
            </div>
          @endif
        </div>
      </div>
    @endif
    <div class="row pt-5">
      @yield('contenido')
    </div>
  </main>
  @include("templates/_footer")

  <!-- jQuery -->
  <script src="{!! asset('js/jquery-3.3.1.min.js') !!}" type="text/javascript"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="{!! asset('vendor/bootstrap/js/bootstrap.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('DataTables-1.10.18/js/jquery.dataTables.min.js') !!}" type="text/javascript"></script>

  <script src="{!! asset('js/sedevel.ajax.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('DataTables-1.10.18/js/dataTables.bootstrap4.min.js') !!}"
  type="text/javascript"></script>
  <script src="{!! asset('js/sedevel.dataTablesBMauco.js') !!}"
  type="text/javascript"></script>
  <script src="{!! asset('js/konami.js') !!}"
                            type="text/javascript"></script>
  <script src="{!! asset('vendor/moment/moment.min.js') !!}"
  type="text/javascript"></script>
  <script src="{!! asset('vendor/moment/locales.min.js') !!}"
  type="text/javascript"></script>
  <script src="{!! asset('bootstrap-datetimepicker/tempusdominus-bootstrap-4.min.js') !!}"
  type="text/javascript"></script>
  <script src="{!! asset('vendor/bootstrap-select/js/bootstrap-select.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('vendor/jasny-bootstrap/js/jasny-bootstrap.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('js/sedevel.inputTable.js') !!}"></script>
  <script src="{!! asset('js/jQuery.print.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('vendor/select2/js/select2.full.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('vendor/select2/js/i18n/es.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('vendor/bootstrap-toggle/js/bootstrap4-toggle.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('js/bmauco.generales.js') !!}"
  type="text/javascript"></script>

  @stack('javascript')
</body>

</html>
