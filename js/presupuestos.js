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
  const beneficio = document.getElementById('beneficio');

  document.getElementById('precio').textContent = "";

  const url = 'https://presupuestosgraficasandalusi.com/presupuesto?tirada=' +
              tirada.value + '&paginas=' + paginas.value + '&dimension=' + dimension.value +
              '&papelInt=brillo&gramajeInt=' + gramajeInt.value + '&papelExt=brillo&gramajeExt=' + gramajeExt.value +
              '&encuadernacion=' + encuadernacion.value + '&destino=' + destino.value + '&plastificado=' + (plastificado.value == 'si') +
              '&plastificadoBrillo=' + (plastificadoBrillo.value == 'si') + '&tintas=' + tintas.value +
              '&plastificadoDosCaras=' + (plastificadoDosCaras.value == 'si') + '&beneficio=' + beneficio.value;

  $.get(url, function(data, status) {
    const precioSpan = document.getElementById('precio');
    const precioSpanNueva = document.getElementById('precioMaquinaNueva');
    for (const key in data.preciosMaquinaVieja) {
    	precioSpan.innerHTML += (key + ": " + data.preciosMaquinaVieja[key] + "<br>");
    }

    for (const key in data.preciosMaquinaNueva) {
    	precioSpanNueva.innerHTML += (key + ": " + data.preciosMaquinaNueva[key] + "<br>");
    }
    console.log(data);
  });
}
