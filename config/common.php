<?php
/**
 * custom values using in the project
 * Also have some custom env values
 */

return [

    'email_message_content' => "Stock is below ".env('STOCK_ALERT_VALUE', 50)."% please upgrade a stock.",
    'email_due_invoice_subject' => "Due Invoice",
    'email_due_invoice_template' => "emails.sent",
    'email_from' => env('MAIL_FROM_ADDRESS'),
    'email_from_name' => env('MAIL_FROM_NAME'),
    'email_attatchment_path' => 'files/',
    'admin' => "admin",
    'staff' => "collection-staff",
    'xero_webhook_key' => env('XERO_WEBHOOK_KEY'),
    'create_event_type' => 'CREATE',
    'update_event_type' => 'UPDATE',
    'invoice_event_category' => 'INVOICE',
    'contact_event_category' => 'CONTACT',
    'xero_api_invoice_url' => env('XERO_API_INVOICE_URL'),
    'xero_api_url' => env('XERO_API_URL'),
    'success' => "SUCCESS",
    'failed' => "FAILED",
    'authorized' => "AUTHORISED",
    'submitted' => "SUBMITTED",
    'legal_message_1' => "This action incurs additional costs - Collections staff will attempt contact by telephone to chase the outstanding payment and report back to you - Cost of successful telephone contact is $9",
    'legal_message_2' => "This action incurs additional costs - The Collections Letter will be sent to the recipients postal address by registered post at a cost of $16",
    'legal_message_3' => "This action incurs additional costs - Collections will forward your file to our Legal partners who will contact you with your options and associated costs",
    'legal_action_subject' => "Legal Action",
    'phone_action_button_email' => env('PHONE_ACTION_BUTTON_EMAIL'),
    'post_action_button_email' => env('POST_ACTION_BUTTON_EMAIL'),
    'legal_action_button_email' => env('LEGAL_ACTION_BUTTON_EMAIL'),
    'mobile' => "MOBILE",
    'contacts' => "Contacts"
];
