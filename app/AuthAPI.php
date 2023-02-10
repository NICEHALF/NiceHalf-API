<?php

/**
 * Nicehalf Auth API
 *
 * Works only with Laravel, 
 * if you want to use it with other framework, 
 * you need to change only session() and redirect() functions
 * 
 * @package     Nicehalf License API
 * @version     1.1.0
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
    private $api_url = 'https://nicehalf.com/api/auth';

    /**
     * Auth API
     *
     * @return void
     */
    public function auth()
    {
        $current_page_url = $this->getCurrentPageURL();
        $website_url = $this->getWebsiteURL();

        // save the current page url without query string if exists in session (for redirect after login)
        if (!session()->has('current_page_url')) {
            session(['current_page_url' => $current_page_url]);
        }

        if (!session()->has('website_url')) {
            session(['website_url' => $website_url]);
        }

        // Check if token is set
        if (isset($_GET['token'])) {
            // Check token
            return $this->checkToken();
        }

        // check if user is logged in
        if (session()->has('nicehalf_auth_user')) {
            // Log user 
            session(['nicehalf_auth_user' => $this->getUser()]);

            // redirect to home page
            return redirect()->route('home')->with('success', 'Successfully logged in');
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
        return redirect()->away($url);
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
     * Get website URL
     * 
     * @return string
     */

    private function getWebsiteURL()
    {
        // get base url
        $url = url('/');

        // remove last slash if exists
        if (substr($url, -1) == '/') {
            $url = substr($url, 0, -1);
        }

        return $url;
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
            session()->flush();

            // Redirect to login page
            return $this->redirect($this->api_url . '?redirect=' . $this->getCurrentPageURL());
        }

        // Check token
        $check_token = $this->verifyToken($token);

        // Check if token is valid
        if ($check_token['status'] != 'success') {
            // Destroy session
            session()->flush();

            // Redirect to login page
            return $this->redirect($this->api_url . '?redirect=' . $this->getCurrentPageURL());
        }

        // Save user data in session
        session(['nicehalf_auth_user' => $check_token['user']]);

        // redirect to current page
        if (session()->has('current_page_url')) {
            $url = session('website_url');
            session()->forget('current_page_url');
            session()->forget('website_url');
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
        session()->flush();

        // Redirect to login page
        return redirect()->route('home')->with('success', 'Successfully logged out');
    }

    /**
     * Get user
     * 
     * @return void
     */

    public function getUser()
    {
        // Check if user is logged in
        if (session()->has('nicehalf_auth_user')) {
            return session('nicehalf_auth_user');
        }

        return null;
    }
}
