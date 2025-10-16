<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name',  old('name')
      , ['class' => 'form-control', 'required' => 'required']) !!}
      <small class="text-danger">{{ $errors->first('name') }}</small>
</div>
<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
      {!! Form::label('email', 'Correo Electrónico') !!}
      @if($textoBoton !== 'Actualizar')
        {!! Form::email('email', old('email')
          , ['class' => 'form-control'
          , 'required' => 'required'
          , 'placeholder' => 'ej: correo@bosquesdelmauco.cl']) !!}
      @else
         <span>{{$usuario->email}}</span>
      @endif
      <small class="text-danger">{{ $errors->first('email') }}</small>
  </div>
  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        {!! Form::label('password', 'Contraseña') !!}
        {!! Form::password('password'
          , ['class' => 'form-control']) !!}
          <small class="text-danger">{{ $errors->first('password') }}</small>
  </div>

  <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
          {!! Form::label('password_confirmation', 'Repita Contraseña') !!}
          {!! Form::password('password_confirmation'
            , ['class' => 'form-control']) !!}
            <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
  </div>

  <div class="btn-group float-right">
        {!! Form::submit($textoBoton, ['class' => 'btn btn-success']) !!}
  </div>
