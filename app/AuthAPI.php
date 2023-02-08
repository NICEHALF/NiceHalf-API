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

// Auth API class
class AuthAPI
{
    /**
     * Auth API URL
     *
     * @var string
     */
    private $api_url = 'https://saas.com/nicehalf/public/api/auth';


    /**
     * Auth API
     *
     * @return void
     */
    public function auth()
    {
        $current_page_url = $this->getCurrentPageURL();

        // save the current page url without query string if exists in session (for redirect after login)
        if (!isset($_SESSION['current_page_url'])) {
            $_SESSION['current_page_url'] = $current_page_url;
        }

        // Check if token is set
        if (isset($_GET['token'])) {
            // Check token
            return $this->checkToken();
        }

        // check if user is logged in
        if (isset($_SESSION['nicehalf_auth_user'])) {
            print_r($_SESSION['nicehalf_auth_user']);
            return;
        }

        // Redirect to login page
        return $this->redirect($this->api_url . '?redirect=' . $current_page_url);
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

        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        // remove query string if exists
        $pageURL = explode('?', $pageURL)[0];

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
        $token = isset($_GET['token']) ? $_GET['token'] : null;

        // Check if token is not set
        if (!$token) {
            // Destroy session
            session_destroy();

            // Redirect to login page
            return $this->redirect($this->api_url . '?redirect=' . $this->getCurrentPageURL());
        }

        // Check token
        $check_token = $this->verifyToken($token);

        // Check if token is valid
        if ($check_token['status'] != 'success') {
            // Destroy session
            session_destroy();

            // Redirect to login page
            return $this->redirect($this->api_url . '?redirect=' . $this->getCurrentPageURL());
        }

        // Save user data in session
        $_SESSION['nicehalf_auth_user'] = $check_token['user'];

        // redirect to current page url without query string if exists
        if (isset($_SESSION['current_page_url'])) {
            $url = $_SESSION['current_page_url'];
            unset($_SESSION['current_page_url']);
            return $this->redirect($url);
        }

        // redirect to home page
        return $this->redirect('/');
    }

    /**
     * Verify token
     * 
     * @return void
     */

    public function verifyToken($token)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $this->api_url . '/verify-token/' . $token, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'ssl.certificate_authority' => 'system',
            'verify' => false,
            'http_errors' => false,
            'allow_redirects' => false,
            'debug' => false,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Logout
     * 
     * @return void
     */

    public function logout()
    {
        // Destroy session
        session_destroy();

        // Redirect to login page
        return $this->redirect($this->api_url . '?redirect=' . $this->getCurrentPageURL());
    }

    /**
     * Get user
     * 
     * @return void
     */

    public function getUser()
    {
        // Check if user is logged in
        if (isset($_SESSION['nicehalf_auth_user'])) {
            return $_SESSION['nicehalf_auth_user'];
        }

        return null;
    }
}
