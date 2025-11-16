<?php
namespace App\Common;

use App\Common\Xero2Api;
use App\Models\XeroConnection;
use Carbon\Carbon;
use XeroAPI\XeroPHP\Api\PayrollAuApi;
use GuzzleHttp\Client;
use XeroAPI\XeroPHP\ApiException;
use XeroAPI\XeroPHP\Configuration;
use Exception;

class Xero2PayrollApi extends Xero2Api
{
    private $payrollApi;
    public function __construct( XeroConnection $xero_connection)
    {
        if( $xero_connection === NULL )
            throw new Exception('Invalid user account selected.');

        $this->xero_connection = $xero_connection;
        $this->access_token = $xero_connection->auth_token;
        $this->refresh_token = $xero_connection->auth_refresh_token;
        $this->expiry = Carbon::parse( date("Y-m-d H:i:s", $xero_connection->auth_token_expiry));
        $this->tenant_id = $xero_connection->xero_tenant_id;

        $config = Configuration::getDefaultConfiguration()->setAccessToken( $this->access_token );
        $this->payrollApi = new PayrollAuApi(
        // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
        // This is optional, `GuzzleHttp\Client` will be used as default.
            new Client(),
            $config
        );
    }

    public function getPayRuns( $where = null, $order = null, $if_modified_since = null, $page = null ):array
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== 'SUCCESS' )
                    return $result;
            }
            // Get payrollApi details
            $response = $this->payrollApi->getPayRuns($this->tenant_id, $if_modified_since, $where, $order, $page);
            return [
                'status' => 'SUCCESS',
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getPayRuns( $where, $order, $if_modified_since, $page);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => 'FAILED',
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getSettings():array
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== 'SUCCESS' )
                    return $result;
            }
            // Get payrollApi details
            $response = $this->payrollApi->getSettings($this->tenant_id);
            return [
                'status' => 'SUCCESS',
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getSettings();
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => 'FAILED',
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }
}
