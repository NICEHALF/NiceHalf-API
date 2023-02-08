<?php

/**
 * Nicehalf Auth API
 *
 * @package     Nicehalf License API
 * @version     1.0.0
 * @author      Nicehalf
 * @link        https://nicehalf.com
 */

// Namespace
namespace Nicehalf\Api;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Auth API class
class AuthAPI
{
    /**
     * Auth API URL
     *
     * @var string
     */
    private $api_url = 'https://nicehalf.com/api/auth';


    /**
     * Auth API constructor
     *
     * @return void
     */
    public function __construct()
    {
        // save the current page full url
        $current_page_url = $this->getCurrentPageURL();

        // save the current page url without query string if exists in session (for redirect after login)
        if (!isset($_SESSION['current_page_url'])) {
            $_SESSION['current_page_url'] = $current_page_url;
        }

        // redirect to login page if not logged in
        if (!isset($_SESSION['nicehalf_auth_token'])) {
            return $this->redirect($this->api_url . '?redirect=' . $current_page_url);
        }

        // check if the token is valid
        $this->checkToken();

        // redirect to login page if not logged in
        if (!isset($_SESSION['nicehalf_auth_token'])) {
            return $this->redirect($this->api_url . '?redirect=' . $current_page_url);
        }
    }

    /**
     * Redirect
     * 
     * @param string $url
     * @return void
     */

    private function redirect($url)
    {
        return header('Location: ' . $url);
    }

    /**
     * Get current page URL
     * 
     * @return string
     */

    private function getCurrentPageURL()
    {
        $pageURL = 'http';

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }

    /**
     * Check token
     * 
     * @return void
     */

    private function checkToken()
    {
        // Token
        $token = $_GET['token'] ?? $_SESSION['nicehalf_auth_token'] ?? null;

        // Check token
        $check_token = $this->apiRequest('check-token', ['token' => $token]);

        // Check if token is valid
        if (!$check_token['status']) {
            // Destroy session
            session_destroy();

            // Redirect to login page
            $this->redirect($this->api_url . '?redirect=' . $this->getCurrentPageURL());
        }

        // Save user data in session
        $_SESSION['nicehalf_auth_user'] = $check_token['user'];

        // Save token in session
        $_SESSION['nicehalf_auth_token'] = $check_token['token'];

        // redirect to current page url without query string if exists
        if (isset($_SESSION['current_page_url'])) {
            $this->redirect($_SESSION['current_page_url']);
        }

        // Unset current page url
        unset($_SESSION['current_page_url']);

        // redirect to home page
        $this->redirect('/');
    }

    /**
     * API Request
     * 
     * @param string $endpoint
     * @param array $data
     * @return array
     */

    private function apiRequest($endpoint, $data = [])
    {
        // API URL
        $api_url = $this->api_url . '/' . $endpoint;

        // Data
        $data = array_merge($data, ['app' => 'app']);

        // Init curl
        $curl = curl_init();

        // Set curl options
        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "content-type: application/json"
            ],
        ]);

        // Execute curl
        $response = curl_exec($curl);

        // Close curl
        curl_close($curl);

        // Return response
        return json_decode($response, true);
    }
}
