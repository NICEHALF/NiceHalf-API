<?php

/**
 * Nicehalf CRUD API
 *
 * Works with any PHP framework,
 * just include this file in your project and use the functions.
 * 
 * @package     Nicehalf CRUD API
 * @version     1.1.0
 * @author      Nicehalf
 * @link        https://nicehalf.com
 */

// Namespace
namespace Nicehalf\Api;

// CRUD API class
class CrudAPI
{
    /**
     * CRUD API URL
     *
     * @var string
     */
    private $api_url = 'https://nicehalf.com/api/crud';

    /**
     * CRUD API Key
     * 
     * Currently it's not public, we will make it public soon.
     * 
     * @var string
     */
    private $api_key = '';

    /**
     * Constructor
     * 
     * @param string $api_key
     * @return void
     */
    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * Get rows
     * 
     * @param string $table
     * @param array $params
     * @param array $order_by
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_rows($table, $where = [], $order_by = [], $limit = null, $offset = null)
    {
        $params = [
            'table' => $table,
            'where' => $where,
            'order_by' => $order_by,
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this->request('GET', $params);
    }

    /**
     * Get one row
     * 
     * @param string $table
     * @param array $where
     * @return array
     */
    public function get_row($table, $where = [])
    {
        $params = [
            'table' => $table,
            'where' => $where,
        ];

        return $this->request('GET', $params);
    }

    /**
     * Insert record
     * 
     * @param string $table
     * @param array $data
     * @return array
     */
    public function insert($table, $data = [])
    {
        $params = [
            'table' => $table,
            'data' => $data,
        ];

        return $this->request('POST', $params);
    }

    /**
     * Update record
     * 
     * @param string $table
     * @param array $data
     * @param array $where
     * @return array
     */
    public function update($table, $data = [], $where = [])
    {
        $params = [
            'table' => $table,
            'data' => $data,
            'where' => $where,
        ];

        return $this->request('PUT', $params);
    }

    /**
     * Delete record
     * 
     * @param string $table
     * @param array $where
     * @return array
     */
    public function delete($table, $where = [])
    {
        $params = [
            'table' => $table,
            'where' => $where,
        ];

        return $this->request('DELETE', $params);
    }

    /**
     * Request
     * 
     * @param string $method
     * @param array $params
     * @return array
     */
    private function request($method, $params = [])
    {
        // Set API URL
        $url = $this->api_url;

        // Set API Key
        $params['api_key'] = $this->api_key;

        // Set request method
        $params['method'] = $method;

        // Set request params
        $params = http_build_query($params);

        // Set request headers
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($params),
            'NICEHALF-CRUD-API-URL: ' . $url,
            'NICEHALF-CRUD-API-KEY: ' . $this->api_key,
            'NICEHALF-CRUD-API-METHOD: ' . $method,
        ];

        // Set request options
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => $headers,
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set request options
        curl_setopt_array($curl, $options);

        // Execute request
        $response = curl_exec($curl);

        // Close cURL
        curl_close($curl);

        // Return response
        return json_decode($response, true);
    }
}
