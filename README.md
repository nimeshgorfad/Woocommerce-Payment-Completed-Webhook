# WordPress Plugin: WooCommerce Payment Completed Webhook

## Description
This WordPress plugin triggers a webhook call whenever a payment is successfully completed in WooCommerce. It allows seamless integration with external systems to handle post-payment actions, such as updating CRM, sending notifications, or initiating fulfillment processes.

## Features
- Automatically triggers a webhook when a payment is marked as completed in WooCommerce.
- Customizable webhook URL and payload.
- Secure webhook calls with optional signature verification.
- Easy-to-use admin interface for configuration.
- Lightweight and optimized for performance.

## Requirements
- WordPress 5.0 or higher
- WooCommerce 4.0 or higher
- PHP 7.4 or higher

## Installation
1. Download the plugin files.
2. Upload the plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Navigate to the plugin settings page to configure the webhook.

## Setup Instructions
1. Go to the plugin settings page in your WordPress admin dashboard.
2. Enter the **Webhook URL** by defining it in the plugin file:
```php
define( 'NKG_WEBHOOK_URL', 'YOUR_WEB_HOOK_URL');
```



## Usage
The plugin will automatically trigger a webhook call with the following payload when a payment is marked as completed:

```json
{
  "order_id": 1234,
  "order_total": "49.99",
  "currency": "USD",
  "payment_method": "credit_card",
  "customer_email": "customer@example.com",
  "customer_name": "John Doe"
}
```

### Example Webhook Handling
Here is an example of handling the webhook in PHP:

```php
$data = json_decode(file_get_contents('php://input'), true);

if ($data && $data['order_id']) {
    // Process the webhook payload
    error_log('Received webhook for order ID: ' . $data['order_id']);
}
```



## Contributing
We welcome contributions! Feel free to submit issues or pull requests on our [GitHub repository](#).

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Support
For any questions or issues, please contact  nimeshgorfad@gmail.com,https://www.freelancer.in/u/nimeshgorfad.
