<?php
namespace PHPixie\Auth\Controller;

/**
 * Abstract Odnoklassniki login controller. To use it you need to extend this class
 * and override the new_user() method, which handles the situation when a user
 * logs in with your app for the very first time (basically you need to register him
 * at that point).
 *
 * It can be used both for popup login and page login.
 * To use the page login, make a link pointing to the controllers 'index' action,
 * for popup login open a popup that points to its 'popup' action.
 * Optionally you can pass a ?return_url =<url> parameter to specify where to redirect the
 * user after he is logged in. You can also specify a default redirect url in the auth.php config file.
 */
abstract class Odnoklassniki extends Facebook
{
    /**
     * @var \PHPixie\Auth\Login\Odnoklassniki
     */
    protected $provider;

    /**
     * User ID of the odnoklassniki user
     * @var string
     */
    protected $user_id = '';

    /*
     * Initializes the controller parameters
     *
     * @return void
     */
    public function before()
    {
        $config = $this->request->param('config', 'default');
        $this->provider = $this->pixie->auth->provider('odnoklassniki', $config);
        $this->default_return_url = $this->pixie->config->get("auth.{$config}.login.odnoklassniki.return_url", null);
        $this->return_url_key = "auth_{$config}_odnoklassniki_return";
    }

    /**
     * Handles odnoklassniki login.
     *
     * @param string $display_mode Display mode of the odnoklassniki login.
     *                             Either 'page' or 'popup'
     * @return void
     */
    public function handle_request($display_mode)
    {

        if ($error = $this->request->get('error')) {
            $this->error($display_mode);
            return;
        }

        if ($code = $this->request->get('code')) {
            $params = $this->provider->exchange_code($code, $this->request->url());
            if ($params['access_token']) {
                $this->user_id = $params['user_id'];
                $this->success($params, $display_mode);

            } else {
                $this->error($display_mode);
            }

            return;
        }

        $return_url = $this->request->get('return_url', $this->default_return_url);

        if (!$return_url && $display_mode == 'page') {
            $return_url = $this->request->server('HTTP_REFERER');
            if (empty($return_url))
                $return_url = 'http://' . $this->request->server('HTTP_HOST');
        }

        $this->pixie->session->set($this->return_url_key, $return_url);
        $url = $this->provider->login_url('', $this->request->url(), $display_mode);
        $this->response->redirect($url);
    }

    /**
     * Called upon the completion of exchange of code for an access token.
     *
     * @param array $params Parsed odnoklassniki server response for the exchange.
     *                      Access token is under the 'access_token' key.
     * @param string $display_mode Display mode of the odnoklassniki login.
     *                             Either 'page' or 'popup'
     * @return void
     */
    public function success($params, $display_mode)
    {
        $return_url = $this->pixie->session->get($this->return_url_key);
        if ($this->provider->login($params['access_token'], $this->user_id, $params['expires_in'])) {
            $this->return_to_url($display_mode, $return_url);
        } else {
            $this->new_user($params['access_token'], $return_url, $display_mode);
        }
    }
}
