<?php
namespace DejwCake\ExtendedAuthenticate\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\ConflictException;
use Cake\Network\Exception\UnauthorizedException;
use DateTime;
use DejwCake\ExtendedAuthenticate\Controller\AppController;

/**
 * UserTokens Controller
 *
 * @property \DejwCake\ExtendedAuthenticate\Model\Table\UserTokensTable $UserTokens
 */
class UserTokensController extends AppController
{
    protected $expiryLengthHours = '+2 hours';

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['getToken']);
    }

    public function getToken()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);

                $userToken = $this->UserTokens->find('all')
                    ->where(['user_id' => $user['id'],
                        'expiry_at >=' => (new DateTime())->format('Y-m-d H:i:s')
                    ])
                    ->hydrate(false)
                    ->first();
                if(!$userToken) {
                    $userToken = $this->UserTokens->newEntity();
                    $data = [
                        'user_id' => $user['id'],
                        'token' => md5(uniqid(rand(), true)),
                        'expiry_at' => (new DateTime())->modify($this->expiryLengthHours)->format('Y-m-d H:i:s'),
                    ];
                    $userToken = $this->UserTokens->patchEntity($userToken, $data);
                    if(!$this->UserTokens->save($userToken)) {
                        throw new ConflictException(__('authenticate', 'Token could not be generated.'));
                    }
                }

                $this->RequestHandler->renderAs($this, 'json');
                $token = $userToken['token'];
                $this->response->statusCode(201);
                $this->set(compact('token'));
                $this->set('_serialize', ['token']);
            } else {
                throw new UnauthorizedException(__d('authenticate', 'You are not authenticated.'));
            }
        } else {
            throw new ConflictException(__('authenticate', 'Wrong method.'));
        }
    }
}
