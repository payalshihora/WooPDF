<?php
ob_end_clean();
require_once(get_home_path() . '/vendor/autoload.php');
if ($_GET['order_id'])
{
  $order = wc_get_order( $_GET['order_id'] );
 
    $pdf = new Dompdf\Dompdf();

    $html = '<h1>Order Details</h1>';
    $html .= '<p>Order ID: ' . $order->get_id() . '</p>';
    $html .= '<br><p>Customer First Name: ' . $order->get_billing_first_name() . '</p>';
    $html .= '<br><p>Customer Last Name: ' . $order->get_billing_last_name() . '</p>';
    $html .= '<br><p>Order Total: ' . $order->get_total() . '</p>';

    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();
    
    // Output the PDF to the browser for download
    ob_end_clean();
    $pdf->stream('order_details_' . $order->get_id() . '.pdf');
  
}
else{
  wp_redirect(get_admin_url());
}
?>