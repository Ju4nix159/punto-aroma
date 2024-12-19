document.addEventListener('DOMContentLoaded', function () {
    const transferRadio = document.getElementById('transfer');
    const walletRadio = document.getElementById('wallet');
    const transferInfo = document.getElementById('transfer-info');
    const walletInfo = document.getElementById('wallet-info');
    const cancelOrderButton = document.getElementById('cancel-order');
    const paymentType = document.getElementById('payment-type');
    const totalLabel = document.getElementById('total-label');
    const totalAmount = document.getElementById('total-amount');

    // Actualiza la visibilidad de los datos de transferencia o billetera
    function updatePaymentDetails() {
        if (transferRadio.checked) {
            transferInfo.classList.remove('d-none');
            walletInfo.classList.add('d-none');
        } else if (walletRadio.checked) {
            walletInfo.classList.remove('d-none');
            transferInfo.classList.add('d-none');
        }
    }

    // Maneja el cambio del tipo de pago
    paymentType.addEventListener('change', function () {
        const subtotal = 1200.5; // Cambia este valor según sea necesario
        const envio = 5.0;
        if (paymentType.value === 'partial') {
            const partialPayment = subtotal * 0.6 + envio;
            totalLabel.textContent = 'Total (60% Seña)';
            totalAmount.textContent = `$${partialPayment.toFixed(2)}`;
        } else {
            const fullPayment = subtotal + envio;
            totalLabel.textContent = 'Total';
            totalAmount.textContent = `$${fullPayment.toFixed(2)}`;
        }
    });

    // Configura eventos iniciales
    transferRadio.addEventListener('change', updatePaymentDetails);
    walletRadio.addEventListener('change', updatePaymentDetails);

    // Acción de cancelar pedido
    cancelOrderButton.addEventListener('click', function () {
        if (confirm('¿Estás seguro de que deseas cancelar el pedido?')) {
            // Aquí puedes redirigir o limpiar el formulario según lo necesario
            alert('Pedido cancelado');
            document.querySelector('form').reset();
            updatePaymentDetails();
        }
    });

    // Inicializa la vista correcta en la carga
    updatePaymentDetails();
});
