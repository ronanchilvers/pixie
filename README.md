# Silex-Base

A [silex] skeleton project with ideas borrowed from fabpot's [silex skeleton]. I have rejigged the filesystem layout a bit, turned on and configured some core providers and added some third party providers.

## Using this package

The easiest way to use this package is via composer like so:

```bash
composer create-project ronanchilvers/silex-base
```

Currently the project is not in packagist so you'll probably need to add the repository to your global ~/.composer/config.json file.

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/ronanchilvers/silex-base.git"
        }
    ]
}
```

Once composer finishes you should have a checked out and composed copy of the project ready to run. To view the test page do:

```bash
php -S localhost:9000 -t web
```

To run in dev mode do:

```bash
php -S localhost:9000 -t web web/index_dev.php
```

## Filesystem Layout

Here's a description of the main components of the project and what they are.

- app
    - config
        - app.php - the main configuration file for the app. Registers the main providers
        - console.php - the configuration file for the console tool
        - dev.php - configuration file for dev environment
        - prod.php - configuration file for the production environment
    - resources
        - js - javascript source files
        - sass - SASS source files with some default components
    - views - The application views. The Twig service provider is configured to look in here for its views.
        - error - Error handling templates
        - test - test controller templates. Can be removed.
- bin
    - console - CLI console script
- src - the php source code for the application, configured as PSR-4 in the App namespace
    - Controller - the application controllers. This directory is searched by the annotations service for routes
        - ErrorController.php - Standard error controller
        - TestController.php - Test controller. Can be removed.
    - Application.php - Base application subclass with some traits added
    - Controller.php - Base controller class with some useful shortcuts
- var
    - cache - Cache directory for annotations, web profiler, etc
    - data - Data directory holding the base sqlite database by default
    - log - Default location for application logs
- web
    - css - Default location for compiled CSS
    - js - Default location for compiled javascript
    - index.php - Production bootstrap
    - index_dev.php - Development bootstrap
- deploy.php.dist - A sample deployment recipe for use with [deployer]
- gulpfile.js - The default gulpfile recipe for the project

## Providers

The following providers are enabled by default:

### Silex core / official providers

 - [ServiceControllerServiceProvider] - allows registering controllers as services
 - [RoutingServiceProvider] - routing component provider, enabling url generation
 - [SessionServiceProvider] - enables session handling
 - [MonologServiceProvider] - integrate monolog logging with a default application log in var/log/application.log
 - [WebProfilerServiceProvider] - (dev only) turns on the symfony web profiler / debug bar
 - [TwigServiceProvider] - twig engine integration
 - [HttpFragmentServiceProvider] - enables http fragment handling, required by the web profiler component

### Third party providers

 - [AnnotationServiceProvider] - allows the use of annotations in controllers for routing
 - [Spot2ServiceProvider] - enables the Spot2 data mapper with a default sqlite database in var/data/database.sqlite


## Frontend resources

The base project is configured to use gulp for compiling frontend resources stored in app/resources. We recommend using [yarn] instead of npm for a more pleasant experience all round. (You'll need [nodejs] installed to run this stuff at all - version 6.9.X seems to work). You can run it by doing:

```bash
yarn install
./node_modules/.bin/gulp
```

[silex]: https://github.com/silexphp/Silex
[silex skeleton]: https://github.com/silexphp/Silex-Skeleton
[ServiceControllerServiceProvider]: https://silex.sensiolabs.org/doc/2.0/providers/service_controller.html
[RoutingServiceProvider]: https://github.com/silexphp/Silex-Providers/blob/master/RoutingServiceProvider.php
[HttpFragmentServiceProvider]: https://silex.sensiolabs.org/doc/2.0/providers/http_fragment.html
[MonologServiceProvider]: https://silex.sensiolabs.org/doc/2.0/providers/monolog.html
[TwigServiceProvider]: https://silex.sensiolabs.org/doc/2.0/providers/twig.html
[AnnotationServiceProvider]: https://github.com/danadesrosiers/silex-annotation-provider
[Spot2ServiceProvider]: https://github.com/ronanchilvers/silex-spot2-provider
[WebProfilerServiceProvider]: https://github.com/silexphp/Silex-WebProfiler
[yarn]: https://yarnpkg.com
[nodejs]: https://nodejs.org
[deployer]: https://deployer.org
