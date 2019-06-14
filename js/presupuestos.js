function calcularPrecios() {
  const tirada = document.getElementById('tirada');
  const paginas = document.getElementById('paginas');
  const dimension = document.getElementById('dimension');
  const gramajeInt = document.getElementById('gramajeInt');
  const gramajeExt = document.getElementById('gramajeExt');
  const encuadernacion = document.getElementById('encuadernacion');
  const destino = document.getElementById('destino');
  const plastificado = document.getElementById('plastificado');
  const plastificadoBrillo = document.getElementById('plastificadoBrillo');
  const plastificadoDosCaras = document.getElementById('plastificadoDosCaras');
  const tintas = document.getElementById('tintas');

  document.getElementById('precio').textContent = tirada.value;

  const url = 'http://presupuestos.eu-west-1.elasticbeanstalk.com/presupuesto?tirada=' +
              tirada.value + '&paginas=' + paginas.value + '&dimension=' + dimension.value +
              '&papelInt=brillo&gramajeInt=' + gramajeInt.value + '&papelExt=brillo&gramajeExt=' + gramajeExt.value +
              '&encuadernacion=' + encuadernacion.value + '&destino=' + destino.value + '&plastificado=' + (plastificado.value == 'si') +
              '&plastificadoBrillo=' + (plastificadoBrillo.value == 'si') + '&tintas=' + tintas.value +
              '&plastificadoDosCaras=' + (plastificadoDosCaras.value == 'si');

  $.get(url, function(data, status) {
    console.log(data);
  });
}
