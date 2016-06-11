<?php
namespace GoogleAuth;

class GoogleAuth extends Google_Client
{
	public function connect()
	{
        $access_token = Configure::read('google.access_token');
        $credentials_file = Configure::read('google.credentials_file');
        $this->setAuthConfigFile($credentials_file);
        $this->setScopes('https://www.googleapis.com/auth/spreadsheets');

        if (isset($access_token) && $access_token)
        {
            $this->setAccessToken($access_token);
            if ($this->isAccessTokenExpired())
            {
                $this->refreshToken($access_token);
                $access_token = $this->getRefreshToken();
                Configure::write('google.access_token', $access_token);
                Configure::dump('google', 'default', ['google']);
            }
            return true;
        }
        else
            return false;
    }
}