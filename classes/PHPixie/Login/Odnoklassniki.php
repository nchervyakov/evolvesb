<?php

namespace PHPixie\Auth\Login;
use PHPixie\Auth\Service;

/**
 * Odnoklassniki login provider
 *
 * @package    Auth
 */
class Odnoklassniki extends Provider {

    /**
     * App ID of the odnoklassniki app
     * @var string
     */
    protected $app_id;

    /**
     * App Secret of the odnoklassniki app
     * @var string
     */
    protected $app_secret;

    /**
     * @var string Odnoklassniki application public key
     */
    protected $app_key;

    /**
     * Permissions to request
     * @var array
     */
    protected $permissions;

    /**
     * Field in the users table where the users
     * odnoklassniki id is stored.
     * @var string
     */
    protected $okid_field;

    /**
     * Users access token
     * @var string
     */
    public $access_token;

    /**
     * Token expiry time
     * @var int
     */
    public $token_expires;

    /**
     * Session key to store the token in
     * @var string
     */
    protected $access_token_key;

    /**
     * Session key to store token expiry time in
     * @var string
     */
    protected $token_expires_key;

    /**
     * Name of the login provider
     * @var string
     */
    protected $name = 'odnoklassniki';

    /**
     * Constructs password login provider for the specified configuration.
     *
     * @param \PHPixie\Pixie $pixie Pixie dependency container
     * @param Service $service Service instance that this login provider belongs to.
     * @param string $config Name of the configuration
     */
    public function __construct($pixie, $service, $config) {
        parent::__construct($pixie, $service, $config);
        $this->app_id = $pixie->config->get($this->config_prefix."app_id");
        $this->app_key = $pixie->config->get($this->config_prefix."app_public");
        $this->app_secret = $pixie->config->get($this->config_prefix."app_secret");
        $this->permissions = $pixie->config->get($this->config_prefix."permissions",array());
        $this->vkid_field = $pixie->config->get($this->config_prefix."okid_field");

        $this->access_token_key = "auth_{$config}_odnoklassniki_token";
        $this->token_expires_key = "auth_{$config}_odnoklassniki_token_expires";
    }

    /**
     * Attempts to log the user in using his access token.
     *
     * @param string $access_token Users access token
     * @param int $token_lifetime Amount of seconds until the token expires
     * @return bool If the user exists.
     */
    public function login($access_token, $user_id, $token_lifetime = null) {

        if(isset($user_id)){
            $user = $this->service->user_model()->where($this->vkid_field, $user_id)->find();
            if ($user->loaded()) {
                $this->set_user($user, $access_token, $token_lifetime);
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the user logged in via this provider.
     * Stores users id, his token and token expiry time
     * inside the session.
     *
     * @param \PHPixie\ORM\Model $user Logged in user
     * @param string $access_token Users access token
     * @param int $token_lifetime Token lifetime
     * @return void
     */
    public function set_user($user, $access_token = null, $token_lifetime = null) {
        parent::set_user($user);

        if ($access_token){
            $this->access_token = $access_token;
            $this->token_expires = $token_lifetime?(time() + $token_lifetime):null;
        }

        $this->pixie->session->set($this->access_token_key, $this->access_token);
        $this->pixie->session->set($this->token_expires_key, $this->token_expires);
    }

    /**
     * Performs user logout.
     * Also clears session variables associated with
     * the users access token.
     *
     * @return void
     */
    public function logout() {
        parent::logout();
        $this->pixie->session->remove($this->access_token_key);
        $this->pixie->session->remove($this->token_expires_key);
    }

    /**
     * Gets odnoklassniki logout URL
     *
     * @param string $redirect_url URL to redirect the user to after the logout
     * @return string odnoklassniki logout URL
     * @throws \Exception
     * @throw \Exception If the user is not logged in
     */
    public function logout_url($redirect_url) {
        if ($this->access_token == null)
            throw new \Exception("User is not logged in with Odnoklassniki");
        return '';
    }

    /**
     * Checks if the user is logged in with odnoklassniki, if so
     * notifies the associated Service instance about it.
     *
     * @return bool If the user is logged in
     */
    public function check_login() {

        if (parent::check_login()) {
            $this->access_token = $this->pixie->session->get($this->access_token_key);
            $this->token_expires = $this->pixie->session->get($this->token_expires_key);
            return true;
        }

        return false;
    }

    /**
     * Returns login url for the server-side odnoklassniki login flow.
     *
     * @param string $return_url URL to return the user after he authorizes the app.
     * @param string $display_mode Determines the odnoklassniki page look.
     *                             Can be either 'page' or 'popup'
     * @return string Login url.
     */
    public function login_url($state, $return_url, $display_mode) {
        return "http://www.odnoklassniki.ru/oauth/authorize?"
            ."client_id={$this->app_id}"
            ."&redirect_uri={$return_url}"
            ."&response_type=code"
            ."&scope=";
            //."&scope=".implode(';', $this->permissions)
            //."&display={$display_mode}";
    }

    /**
     * Exchanges OAuth code for the access token.
     *
     * @param string $code OAuth code
     * @param string $return_url URL to return the user after he authorizes the app.
     * @return array Parsed result of the odnoklassniki call.
     */
    public function exchange_code($code, $return_url) {
        $url = "http://api.odnoklassniki.ru/oauth/token.do";
        $response = $this->request($url, "POST", [
            "client_id" => $this->app_id,
            "redirect_uri" => $return_url,
            "client_secret" => $this->app_secret,
            "code" => $code,
            "grant_type" => "authorization_code"
        ]);

        $data = json_decode($response, true);

        if ($data['access_token']) {
            $userUrl = "http://api.odnoklassniki.ru/fb.do?"
                ."method=users.getCurrentUser"
                ."&access_token=" . $data['access_token']
                ."&application_key=".$this->app_key
                ."&sig=".$this->getSignature($data['access_token']);
            $userData = json_decode($this->request($userUrl), true);

            $data['user_id'] = $userData['548775234'];

            if (is_array($userData['name'])) {
                $data['first_name'] = $userData['first_name'];
                $data['last_name'] = $userData['last_name'];
            }
        }

        return $data;
    }

    /**
     * Requests a url using CURL
     *
     * @param string $url URL to fetch
     * @param string $method
     * @param array $params
     * @return array Parsed result of the vkontakte call.
     * @throws \Exception If the request failed
     */
    public function request($url, $method = 'GET', $params = []) {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => array('Expect:'),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_URL            => $url
        ));
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $response = curl_exec($ch);
        if($response === false)
            throw new \Exception("URL request failed:".curl_error($ch));

        return $response;
    }

    /**
     * Generates the signature for requests to OK API
     * @param $accessToken
     * @return string
     */
    public function getSignature($accessToken)
    {
        return md5(sprintf('application_key=%smethod=users.getCurrentUser%s',
            $this->app_key, md5($accessToken . $this->app_secret)
        ));
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->app_key;
    }
}
