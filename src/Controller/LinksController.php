<?php

namespace App\Controller;

use Cake\Routing\Router;

/**
 * Links Controller
 *
 * @property \App\Model\Table\LinksTable $Links
 *
 * @method \App\Model\Entity\Link[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LinksController extends AppController
{
    public function create()
    {
        $this->request->allowMethod('post');

            $link = $this->Links->find()->where(['Links.url' => $this->request->getData('url')])->order(['Links.created' => 'desc'])->first();
            if ($link) {
                $this->api_response_data['uid'] = $link->uid;
                $this->api_response_data['formated_url'] = Router::url(['controller' => 'links', 'action' => 'read', $link->uid], true);
            } else {
                $link = $this->Links->newEntity(['url' => $this->request->getData('url')]);

                if (!$link->getErrors()) {
                    $link->uid = $this->_getRandomHash();

                    while ($this->Links->exists(['uid' => $link->uid]) == true) {
                        $link->uid = $this->_getRandomHash();
                    }

                    if ($this->Links->save($link)) {
                        $this->api_response_data['uid'] = $link->uid;
                        $this->api_response_data['formated_url'] = Router::url(['controller' => 'links', 'action' => 'read', $link->uid], true);
                    } else {
                        $this->api_response_code = 400;
                        $this->api_response_flash = "Une erreur est survenue";
                    }
                } else {
                    $this->api_response_code = 400;
                    $errors = $link->getError('url');
                    $this->api_response_flash = reset($errors);
                }
            }

    }

    public function read($uid)
    {
        $this->request->allowMethod('get');

        $link = $this->Links->find()->where(['Links.uid' => $uid])->first();

        if ($link) {
            $link->click_count++;
            $this->Links->save($link);
//           $this->response = $this->response->withStatus(302)->withLocation($link->url);
            $this->response = $this->response->withStringBody("<script>setTimeout(function() {window.location.href = '".$link->url."';}, 1);</script>");
            return $this->response;

        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = "Ce lien n'existe pas";
        }
    }

    private static function _getRandomHash()
    {
        $str = null;
        $characters = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));

        $max = (count($characters) - 1);
        for ($i = 0; $i < 10; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }

        return $str;
    }
}
