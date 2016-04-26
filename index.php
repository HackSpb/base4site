<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

/* настройте базовую папку для шаблонов */

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views',
));


$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    	$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return sprintf('%s/%s', trim($app['request']->getBasePath()), ltrim($asset, '/'));
    }));
        $twig->addGlobal('pi', 3.14);
    return $twig;
}));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->before(function ($request) use ($app) {
    
	/*благодаря такой конструкции можно добавить глобальные переменныев шаблоны перед их запуском*/
    $app['twig']->addGlobal('active', $request->get("_route"));
    $app['twig']->addGlobal('year', date("Y"));	

});


/********************************************************/
/***********  дальше идет подключение страниц      *********/


$app->get('/', function() use ($app) {
	return $app['twig']->render('index.html');
})->bind('home');

$app->get('/about', function() use ($app) {
	return $app['twig']->render('about.twig');
})->bind('about');

$app->get('/contact', function() use ($app) {
	return $app['twig']->render('contact.twig');
})->bind('contact');


$app->run();