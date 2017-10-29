<?php

namespace App\Controller;

use App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * Test index action
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function errorAction(\Exception $ex, Request $request, $code)
    {
        if ($this->app()['debug']) {
            return;
        }
        $engine   = $this->app()['twig'];
        $templates = [
            'error/'.$code.'.html.twig',
            'error/'.substr($code, 0, 2).'X.html.twig',
            'error/'.substr($code, 0, 1).'XX.html.twig',
            'error/default.html.twig',
        ];

        return new Response(
            $this->app()['twig']
                ->resolveTemplate($templates)
                ->render(['exception' => $ex, 'code' => $code]),
            $code
        );
    }
}
