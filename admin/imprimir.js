function imprimirResumenPedido() {
  // Clonar el contenido que deseas imprimir
  const contenido = document.querySelector(".content-wrapper").cloneNode(true);

  // Eliminar o ocultar elementos no deseados
  const elementosAEliminar = [
    ".botones-container", // Contenedor de botones rápidos
    ".card-footer", // Pie de página de la tarjeta
    ".comprobantes-container", // Información de pago
    ".col-md-4", // Columna derecha (acciones rápidas e información de pago)
    "th:nth-child(6)", // Encabezado de la columna "acciones" en la tabla
    "td:nth-child(6)", // Celdas de la columna "acciones" en la tabla
    ".content-header", // Elimina el encabezado con "Resumen del Pedido" y las rutas
  ];

  elementosAEliminar.forEach((selector) => {
    const elementos = contenido.querySelectorAll(selector);
    elementos.forEach((elemento) => elemento.remove());
  });

  // Crear una ventana nueva para imprimir
  const ventanaImpresion = window.open("", "_blank");
  ventanaImpresion.document.write(
    "<html><head><title>Resumen del Pedido</title>"
  );
  ventanaImpresion.document.write(
    '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">'
  );
  ventanaImpresion.document.write("<style>");
  ventanaImpresion.document.write("@media print { body { padding: 20px; } }"); // Estilos para impresión
  ventanaImpresion.document.write("</style>");
  ventanaImpresion.document.write("</head><body>");
  ventanaImpresion.document.write(contenido.innerHTML);
  ventanaImpresion.document.write("</body></html>");
  ventanaImpresion.document.close();

  // Esperar a que se cargue el contenido y luego imprimir
  ventanaImpresion.onload = function () {
    ventanaImpresion.print();
    ventanaImpresion.close();
  };
}
