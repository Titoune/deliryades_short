<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $api_response_code = 200;
    public $api_response_data = [];
    public $api_response_flash = '';

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);


        if (isset($this->viewVars['code']) && isset($this->viewVars['message'])) {
            $this->api_response_code = $this->viewVars['code'];
            $this->api_response_flash = $this->viewVars['message'];
        }

        $return['code'] = $this->api_response_code;
        $return['flash'] = $this->api_response_flash;
        $return['data'] = $this->api_response_data;

        $this->response = $this->response
            ->withCharset('utf-8')
            ->withType('application/json')
            ->withStatus($this->api_response_code)
            ->withDisabledCache()
            ->withStringBody(json_encode($return));

        return $this->response;
    }
}
