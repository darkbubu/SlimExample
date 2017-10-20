<?php

session_start();
require '../vendor/autoload.php';
//CONFIGURACIONES DEL CONTENEDOR
$config['displayErrorDetails']=true;
$config['db']['driver'] = 'mysql';
$config['db']['host'] = 'localhost';
$config['db']['database'] = 'SlimTest';
$config['db']['username'] = 'rainier';
$config['db']['password'] = '20863638';
$config['db']['charset'] = 'utf8';
$config['db']['collation'] = 'utf8_unicode_ci';

//GENERAR UN CONTENEDOR DIC USANDO LAS CONFIGURACIONES ANTERIORES 
$app = new \Slim\App(['settings'=>$config]);

//REFERENCIAR AL CONTADOR
$container = $app->getContainer();
//**************DEPENDENCIAS DE TERCEROS*************************************

//CONFIGURACION DE ELLOQUENT
$capsule = new \Illuminate\Database\Capsule\Manager;
//AÑADIR UNA CONEXION A ELOQUENT USANDO LOS DATOS DE CONFIGURACION EN EL CONTENEDOR
$capsule->addConnection($container['settings']['db']);
//DECLARAR ELOQUENT COMO GLOBAL PARA SER UTILIZADO EN CUALQUIER CLASE
$capsule->setAsGlobal();
//INICIAR ELOQUENT
$capsule->bootEloquent();
//GETTER DE ELOQUENT
$container['db'] = function($c) use ($capsule){
    return new $capsule;
};
//AÑADIR LA CLASE AUTH AL CONTENEDOR
$container['auth'] = function($c){
    return new \App\Auth\Auth;
};
//CONFIGURACION DE LAS VIEWS
$container['view']= function($c){
    //NUEVA INSTANCIA DE TWIG INDICANDO EL DIRECTORIO DONDE SE UBICARAN LAS VISTAS
  $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views',[
    'cache'=>false, //IMPIDE QUE LAS VISTAS SE GUARDEN EN CACHE, DESACTIVAR EN PRODUCCION.
]);
    
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->router,
        $c->request->getUri()
    ));
    //PERMITE UTILIZAR LA CLASE AUTH COMO GLOBAL EN EL CONTEXTO DE LAS VISTAS.
    //PARA HACER MAS EFICIENTE ESTE PROCESO, NO SE INCLUYE DIRECTAMENTE LA CLASE SINO EL RESULTADO DE SUS METODOS PAR AEVITAR CONTSTANTES QUERYS SQL INNECESARIOS
    $view->getEnvironment()->addGlobal('auth',[
        'check' => $c->auth->check(),
        'user' => $c->auth->user(),
    ]);

    return $view;
};


//*********************************************************************

//*************************************** DEPENDENCIAS DEL PROYECTO *************************

//AÑADIR HOMECONTROLLER AL CONTENEDOR
$container['HomeController'] = function($c){
    return new \App\Controllers\HomeController($c);
};
//AÑADIR EL VALIDADOR AL CONTENEDOR
$container['validator'] = function($c){
    return new App\Validation\Validator;
};

//AÑADIR AUTHCONTROLLER AL CONTENEDOR
$container['AuthController'] =  function ($c){
    return new \App\Controllers\AuthController($c);
};
//**************************************************************************
//************************** MIDDLEWARE**************************
$app->add(new App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new App\Middleware\FormDataPersistMiddleware($container));
//*******************************************

//************************ RULES ************************

//*******************************************************
require '../app/routes.php';

