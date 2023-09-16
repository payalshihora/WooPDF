<?php
/**
* Plugin Name: Woo PDF
* Description: A Plugin to show PDF field in admin order list and download it.
* Version: 0.1
* Author: Payal Shihora
**/
add_action('admin_menu', 'my_menu');
function my_menu() {
    add_menu_page('Woo PDF', 'Woo PDF Plugin', 'manage_options', 'woo-pdf-generate', 'woo_pdf_generate');
}
function woo_pdf_generate() {
    include('order_pdf_download.php');
}
add_filter( 'manage_edit-shop_order_columns', 'wc_custom_order_column' );
function wc_custom_order_column($columns)
{
    $reordered_columns = array();
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            // Inserting a new column after "Order Status" column
            $reordered_columns['order_pdf_col'] = __( 'PDF','theme_domain');
        }
    }
    return $reordered_columns;
}

//add action to the new added field
add_action( 'manage_shop_order_posts_custom_column', 'custom_order_pdf', 20, 2 ); 
function custom_order_pdf( $column, $post_id ) {
    if ( $column == 'order_pdf_col') {
        $order = wc_get_order( $post_id );
        echo '<a href="' . admin_url( "admin.php?page=woo-pdf-generate&order_id=" . $post_id ). '" class="button">Download PDF</a>';
	}
}
//adding a setting for email notification in backend
add_filter('woocommerce_settings_tabs_array', 'email_notify_set', 50);
add_action('woocommerce_settings_tabs_settings_tab_order_email_notification', 'order_email_notify');
add_action('woocommerce_update_options_settings_tab_order_email_notification', 'update_order_email');

function email_notify_set($settings_tabs) {
    $settings_tabs['settings_tab_order_email_notification'] = 'Order Email Notification';
    return $settings_tabs;
}

function order_email_notify() {
    woocommerce_admin_fields(order_email_notify_fields());
}

function order_email_notify_fields() {
    return array(
        'section_title' => array(
            'name' => 'Order Email Notification Settings',
            'type' => 'title',
            'desc' => '',
            'id'   => 'order_email_notification_settings_section_title',
        ),
        'recipient_email' => array(
            'name' => 'Recipient Email Address',
            'type' => 'email',
            'desc' => 'Enter Recipient Email Address to receive order completion notifications.',
            'id'   => 'order_email_notification_recipient_email',
        ),
        'enable_notification' => array(
            'name' => 'Enable Email Notification',
            'type' => 'checkbox',
            'desc' => 'Enable email notification when order status changed to completed',
            'id'   => 'order_email_notification_enable',
        ),
        'section_end' => array(
            'type' => 'sectionend',
            'id'   => 'order_email_notification_settings_section_end',
        ),
    );
}

function update_order_email() {
    woocommerce_update_options(order_email_notify_fields());
}

// Hook into WooCommerce order status changes and send email notifications when orders are completed
add_action('woocommerce_order_status_completed', 'send_order_completed_notification_email');

function send_order_completed_notification_email($order_id) {
    // Check if email notification is enabled
    $enable_notification = get_option('order_email_notification_enable', 'no');
    if ($enable_notification !== 'yes') {
        return;
    }

    // Get the recipient email address from the settings
    $recipient_email = get_option('order_email_notification_recipient_email');

    // Get the order object
    $order = wc_get_order($order_id);

    // Prepare email subject and message
    $subject = 'Order Completed: #' . $order_id;
    $message = 'The order #' . $order_id . ' has been completed.';

    // Send the email
    wp_mail($recipient_email, $subject, $message);
}

function costliest_order()
{
    include('front_order_list.php');
    return ob_get_clean();
}
add_shortcode('My_Orders', 'costliest_order')
?>