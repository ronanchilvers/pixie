<?php

namespace Pixie;

use Pixie\Context\Singleton;
use Pixie\File;
// use Pixie\DB\Connection;

class Environment extends Singleton
{
    protected static function setup($instance)
    {
        $instance
            ->service('version',
                '%PIXIE_VERSION%'
            )

            // Base directories
            ->once('rootdir', function() {
                return PIXIE_ROOT;
            })
            ->once('configdir', function($env){
                $root = $env->getRootDir();
                return File::join($root);
            })
            ->once('templatedir', function($env){
                $root = $env->getRootDir();
                return File::join($root, 'templates');
            })

            // Configuration
            ->once('config', function($env){
                $config = File::join($env->getConfigDir(), 'pixie.json');
                if (!file_exists($config)) {
                    return array();
                }

                $config = file_get_contents($config);
                return (array) json_decode($config);
            })

            // Services
            ->once('db', function(){
                return new DB\Connection();
            });
            ;
    }
}
