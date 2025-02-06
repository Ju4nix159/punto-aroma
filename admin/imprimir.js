function printOrder(orderId) {
    // Open the print template in a new window
    let printWindow = window.open('print_order.php?id_pedido=' + orderId, '_blank');
    // Automatically trigger print when the page loads
    printWindow.onload = function() {
        printWindow.print();
    };
}