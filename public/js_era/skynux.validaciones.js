$(function() {

  /**
   * Redondea a N decimales
   * @param  float num
   * @param  int scale (default 2): cantidad de decimales a redondear
   * @return float redondeado a 2 decimales
   */
  window.redondear = function (num, scale = 2) {
    var number = Math.round(num * Math.pow(10, scale)) / Math.pow(10, scale);
    if(num - number > 0) {
      return (number + Math.floor(2 * Math.round((num - number) * Math.pow(10, (scale + 1))) / 10) / Math.pow(10, scale));
    } else {
      return number;
    }
  };

  /**
   * Verifica si una cadena es efectivamente un número (entero o flotante)
   * @param  String numero posible número
   * @return boolean true o false
   */
  window.esNumerico = function(numero){
    return $.isNumeric(numero);
  };

  /**
   * Verifica si una cadena representa efectivamente un porcentaje
   * entero y de 0 a 100
   * @param  String  numero posible porcentaje
   * @return boolean true o false
   */
  window.esPorcentajeValido = function(numero){
    if(window.esNumerico(numero)){
      if(+numero >=0 && +numero<=100){
        return true;
      }
    }
    return false;
  };
});
