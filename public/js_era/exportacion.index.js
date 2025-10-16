$(function() {
  $('body').on('click','.incluirTodosCrops', function(){
    if($(this).is(':checked')){
      $('.crops').prop('disabled',true);
    } else {
      $('.crops').prop('disabled',false);
    }
  });
});
