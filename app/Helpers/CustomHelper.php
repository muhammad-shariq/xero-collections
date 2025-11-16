<?php

namespace App\Helpers;
class CustomHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function check_null($data)
    {
        if(is_null($data))
            return "";
        return $data;
    }

    public function parseLastEmailTemplateID($template_ids)
    {
        $array = explode(",",$template_ids);
        return str_replace("--", "", $array[count($array) - 1]);
    }

    public function hash_hmac_base64_decode($content)
    {
        $webhookKey = config("common.xero_webhook_key");
        return base64_encode(hash_hmac('sha256', $content, $webhookKey, true));
    }

}
