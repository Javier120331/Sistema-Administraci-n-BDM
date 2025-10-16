$(function($) {
    var $token = $('#token')
      , valueToken = $token != undefined ? $token.val(): undefined
    ;
    $.ajaxSetup({
      data: {
        _token: valueToken
      }
    });
}(jQuery));
