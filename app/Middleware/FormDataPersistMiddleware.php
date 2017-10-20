<?php
namespace App\Middleware;

class FormDataPersistMiddleware extends Middleware{
    
    public function __invoke($request,$response,$next){
        
        $this->container->view->getEnvironment()->addGlobal('persist',isset($_SESSION['persist'])?$_SESSION['persist']:null);
        $_SESSION['persist'] = $request->getParams();
        $response= $next($request,$response);
        return $response;
    }
    
}