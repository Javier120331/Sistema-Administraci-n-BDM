<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de Administración de Remuneraciones">
    <meta name="author" content="SARAYAR">
    @include('templates._favicon')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Administración de Remuneraciones</title>

    <!-- Scripts -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    {!! Html::style(asset('fontawesome/css/all.min.css')) !!}
    <!-- Styles -->

    {!! Html::style(asset('vendor/bootstrap/css/bootstrap.min.css')) !!}
    {!! Html::style(asset('DataTables-1.10.18/css/dataTables.bootstrap4.min.css')) !!}
    {!! Html::style(asset('bootstrap-datetimepicker/tempusdominus-bootstrap-4.min.css')) !!}
    {!! Html::style(asset('vendor/bootstrap-select/css/bootstrap-select.min.css')) !!}
    {!! Html::style(asset('vendor/jasny-bootstrap/css/jasny-bootstrap.min.css')) !!}
    {!! Html::style(asset('vendor/select2/css/select2.min.css')) !!}
    {!! Html::style(asset('vendor/jasny-bootstrap/css/jasny-bootstrap.min.css')) !!}
      {!! Html::style(asset('vendor/bootstrap-toggle/css/bootstrap4-toggle.min.css')) !!}
    {!! Html::style(asset('css/konami.css')) !!}
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    {!! Html::style(asset('css/bmaucoRRHH.css')) !!}
    <!-- Custom CSS -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @stack('css')
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
