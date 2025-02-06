function imprimirResumenPedido() {
  // Clonar el contenido que deseas imprimir
  const contenido = document.querySelector(".content-wrapper").cloneNode(true);

  // Eliminar o ocultar elementos no deseados
  const elementosAEliminar = [
      ".botones-container",
      ".card-footer",
      ".comprobantes-container",
      ".col-md-4",
      "th:nth-child(6)",
      "td:nth-child(6)",
      ".content-header"
  ];

  elementosAEliminar.forEach((selector) => {
      const elementos = contenido.querySelectorAll(selector);
      elementos.forEach((elemento) => elemento.remove());
  });

  // Crear una ventana nueva para imprimir
  const ventanaImpresion = window.open("", "_blank");
  
  // Crear el contenido HTML con los estilos necesarios
  const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
          <title>Resumen del Pedido</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
          <style>
              @media print {
                  body { 
                      padding: 20px;
                  }
                  @page {
                      size: auto;
                      margin: 10mm;
                  }
              }
              .container {
                  max-width: 100%;
                  margin: 20px auto;
                  padding: 0 15px;
              }
          </style>
      </head>
      <body>
          <div class="container">
              ${contenido.innerHTML}
          </div>
      </body>
      </html>
  `;

  ventanaImpresion.document.write(htmlContent);
  ventanaImpresion.document.close();

  // Esperar a que los estilos de Bootstrap se carguen
  const linkElement = ventanaImpresion.document.querySelector('link');
  
  linkElement.onload = function() {
      // Dar un pequeño tiempo adicional para asegurar que todo esté renderizado
      setTimeout(() => {
          ventanaImpresion.print();
          // Solo cerrar la ventana después de que el diálogo de impresión se cierre
          ventanaImpresion.onafterprint = function() {
              ventanaImpresion.close();
          };
      }, 500);
  };

  // Manejar errores de carga
  linkElement.onerror = function() {
      console.error('Error al cargar los estilos de Bootstrap');
      alert('Hubo un error al preparar la impresión. Por favor, inténtelo de nuevo.');
      ventanaImpresion.close();
  };
}