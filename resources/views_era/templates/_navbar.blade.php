<header>

<nav class="navbar navbar-expand-lg navbar-dark bg-suelditas">
  <a class="navbar-brand logo_login" href="{{action('HomeController@home')}}">
    {!! Html::image('img/logo.png') !!}
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#"
          id="dropDownMovimientosLink" role="button"
          data-toggle="dropdown" aria-haspopup="true"
          aria-expanded="false">
          Movimientos
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownMovimientosLink">
          <a class="dropdown-item"
              href="{{action('MovimientosController@create')}}">
            Ingresar Movimiento
          </a>
          <a class="dropdown-item" href="{{action('MovimientosController@index')}}">Ver Movimientos</a>
          <a class="dropdown-item" href="{{action('LicenciasController@index')}}">Licencias</a>
          <a class="dropdown-item" href="{{action('HistorialAsistenciaController@index')}}">Historial de Asistencia</a>
          <a class="dropdown-item"
            href="{{action('VacacionesController@index')}}">Vacaciones</a>
          <a class="dropdown-item"
              href="{{action('VacacionesController@consulta')}}">Consulta Saldo Vacaciones</a>
          <a class="dropdown-item"
              href="{{action('VacacionesController@indexProgresivas')}}">Vacaciones Progresivas</a>
          </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#"
          id="dropDownFiniquitosLink" role="button"
          data-toggle="dropdown" aria-haspopup="true"
          aria-expanded="false">
          Finiquitos
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownFiniquitosLink">
          <a class="dropdown-item"
              href="{{action('FiniquitosController@create')}}">
            Ingresar
          </a>
          <a class="dropdown-item" href="{{action('FiniquitosController@index')}}">Ver</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{action('ImportacionController@index')}}">
          Importación
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{action('LiquidacionController@index')}}">
          Liquidaciones
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#"
          id="dropDownFiniquitosLink" role="button"
          data-toggle="dropdown" aria-haspopup="true"
          aria-expanded="false">
          <i class="fas fa-cogs"></i>
          Configuraciones
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownFeriadosLink">
          <a class="dropdown-item" href="{{action('UsuarioController@index')}}">
            <i class="fas fa-users"></i>&nbsp;Usuarios
          </a>
          <a class="dropdown-item"
              href="{{action('FeriadosController@index')}}">
              <i class="far fa-calendar"></i>&nbsp;Feriados
          </a>
        </div>
      </li>
    </ul>
  </div>
  <div class="form-inline bg-white card p-1">
    <ul role="tablist" aria-orientation="vertical" class="nav flex-column nav-pills text-center">
      <li class="nav-item" >Bienvenido</li>
      <li class="nav-item">{{Auth::user()->name}}</li>
      <li class="nav-item">{{Auth::user()->email}}</li>
      <li class="nav-item">
        <a href="{{ route('logout') }}">Cerrar Sesión</a>

      </li>
    </ul>
  </div>
</nav>
</header>
