<div class="modal resultado{{$accion}}" tabindex="-1"
    role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">{{$accion}} {{$entidad}}</h4>
          <button type="button" class="close"
            data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="alert resultadoAjax" role="alert">
            </div>
          </div>
          <div class="col-md-offset-10 col-lg-offset-10 col-sm-offset-10
            col-lg-2 col-md-2 col-sm-2">
              <button  data-dismiss="modal"
                class="btn btn-primary">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
