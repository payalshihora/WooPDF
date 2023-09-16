<?php
$user_id = get_current_user_id(); //get current user's ID
$args = array(
    'customer_id' => $user_id,
    'limit' => -1,
    //'status' => array('completed'), 
);
$user_orders = wc_get_orders($args);
// custom sort function to compare order total
function compare_orders($a, $b) {
    return $b->get_total() - $a->get_total();
}
usort($user_orders, 'compare_orders');
$top_5_orders = array_slice($user_orders, 0, 5);

// Check if there are orders
if ((!empty($top_5_orders)) && is_user_logged_in() ){
    $output = '<table border=1 cellpadding=10><tr><th>Order ID</th><th>Total</th><th>Date</th></tr>';

    foreach ($top_5_orders as $order) {
        $order_id = $order->ID;
        $order_total = get_post_meta($order_id, '_order_total', true);
        $order_date = get_post_field('post_date', $order_id);
        $output .= '<tr>';
        $output .= '<td> ' . $order_id . '</td><td>' . wc_price($order_total) . '</td><td>' . $order_date;
        $output .= '</td></tr>';
    }

    $output .= '</table>';
} else {
    $output = 'You have no orders yet.';
}
   // Reset the post data 
   wp_reset_postdata();
   echo $output;
?>