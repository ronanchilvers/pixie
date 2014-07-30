<?php

namespace Pixie\Web\Route\Deployment;

use Pixie\Environment;
use Pixie\Web\Route\Base;
use Pixie\Item\App;

class Add extends Base
{
    protected $verbs        = array('GET', 'POST');
    protected $path         = '/add';
    protected $template     = 'deployment/add.phtml';

    public function execute(\Pixie\Web\Route\Context $context)
    {
        $newApp = new App();
        $request = $context->app->request;

        if ($request->post('action')) {
            $data = $request->post('app');
            $newApp->setFromArray($data);
            if ($newApp->save(false)) {
                $context->app->redirect('/listing');
            } else {
                var_dump($newApp->getErrors()); //$newApp->getErrors());
                exit;
            }
        }

        $this->view()->app = $newApp;
    }
}
