<?php

namespace DejwCake\ExtendedAuthenticate\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Exception\Exception;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\HttpException;
use Cake\ORM\TableRegistry;
use DateTime;

class TokenAuthenticate extends BaseAuthenticate
{

    /**
     * Parsed token.
     *
     * @var string|null
     */
    protected $_token;

    /**
     * Constructor.
     *
     * Settings for this object.
     *
     * - `header` The token header value.
     * - `parameter` The url parameter name of the token.
     * - `userModel` The model name of the User, defaults to Users.
     * - `fields` The fields to use to identify a user by. Make sure `'token'` and
     *    `'expiry_at'` has been added to the array
     * - `scope` Additional conditions to use when looking up and authenticating users,
     *    i.e. `['Users.is_active' => 1].`
     * - `contain` Extra models to contain.
     * - `continue` Continue after trying token authentication or just throw the
     *   `unauthenticatedException` exception.
     * - `unauthenticatedException` Exception name to throw or a status code as an integer.
     *
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry
     *   used on this request.
     * @param array $config Array of config to use.
     * @throws \Cake\Core\Exception\Exception If header is not present.
     */
    public function __construct(ComponentRegistry $registry, $config)
    {
        $this->_registry = $registry;
        $this->setConfig([
            'header' => 'authorization',
            'prefix' => 'bearer',
            'parameter' => 'token',
            'fields' => ['token' => 'token', 'password' => 'password', 'expiry_at' => 'expiry_at'],
            'continue' => false,
            'unauthenticatedException' => '\Cake\Network\Exception\UnauthorizedException',
            'tokenModel' => 'DejwCake/ExtendedAuthenticate.UserTokens',
        ]);
        $this->setConfig($config);
        if (empty($this->_config['parameter']) &&
            empty($this->_config['header'])
        ) {
            throw new Exception(__d(
                'authenticate',
                'You need to specify token parameter and/or header'
            ));
        }
    }

    /**
     * Get user record based on info available in JWT.
     *
     * @param ServerRequest $request The request object.
     * @param Response $response Response object.
     * @return array|bool User record array or false on failure.
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * If unauthenticated, try to authenticate and respond.
     *
     * @param ServerRequest $request The request object.
     * @param Response $response The response object.
     * @return bool False on failure, user on success.
     */
    public function unauthenticated(ServerRequest $request, Response $response)
    {
        if ($this->_config['continue']) {
            return false;
        }
        if (is_string($this->_config['unauthenticatedException'])) {
            // @codingStandardsIgnoreStart
            throw new $this->_config['unauthenticatedException'];
            // @codingStandardsIgnoreEnd
        }
        $message = __d('authenticate', 'You are not authenticated.');
        throw new HttpException($message, $this->_config['unauthenticatedException']);
    }

    /**
     * Get token from header or query string.
     *
     * @param ServerRequest|null $request Request object.
     * @return null|string Token string if found else null.
     */
    public function getToken(ServerRequest $request = null)
    {
        $config = $this->_config;
        if (!$request) {
            return $this->_token;
        }
        $header = $request->getHeaderLine($config['header']);
        if ($header && stripos($header, $config['prefix']) === 0) {
            return $this->_token = str_ireplace($config['prefix'] . ' ', '', $header);
        }
        if (!empty($this->_config['parameter']) &&
            !empty($request->getQuery($this->_config['parameter']))
        ) {
            $this->_token = $request->getQuery($this->_config['parameter']);
        }
        return $this->_token;
    }

    /**
     * Get token information from the request.
     *
     * @param ServerRequest $request Request object.
     * @return mixed Either false or an array of user information
     */
    public function getUser(ServerRequest $request)
    {
        if ($token = $this->getToken($request)) {
            return $this->_findUser($token);
        }
        return false;
    }


    /**
     * Find a user record.
     *
     * @param string $username The token identifier.
     * @param string $password Unused password.
     * @return Mixed Either false on failure, or an array of user data.
     */
    protected function _findUser($username, $password = null)
    {
        $tokenModel = $this->_config['tokenModel'];
        list($plugin, $tokenModelName) = pluginSplit($tokenModel);
        $fields = $this->_config['fields'];
        $conditions = [
            $tokenModelName . '.' . $fields['token'] => $username,
            $tokenModelName . '.' . $fields['expiry_at'] . ' >=' => new DateTime(),
        ];
        $tableToken = TableRegistry::get($tokenModel)->find('all');
        $result = $tableToken
            ->where($conditions)
            ->enableHydration(false)
            ->first();
        if (empty($result)) {
            return false;
        }

        $userModel = $this->_config['userModel'];
        list($plugin, $userModelName) = pluginSplit($userModel);
        $tableUser = TableRegistry::get($userModel)->find('all');
        $conditions = [
            $userModelName . '.id' => $result['user_id'],
        ];
        if ($this->_config['contain']) {
            $tableUser = $tableUser->contain($this->_config['contain']);
        }
        $result = $tableUser
            ->where($conditions)
            ->enableHydration(false)
            ->first();
        if (empty($result)) {
            return false;
        }
        unset($result[$fields['password']]);
        return $result;
    }
}
