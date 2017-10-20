<?php
namespace App\Controllers;
use App\Models\User; //PERMITE USAR LA CLASE USER
use App\Auth\Auth; // PERMITE USAR LA CLASE AUTH
use Respect\Validation\Validator as v; //PERMITE USAR LA CLASE DE VALIDACION

class AuthController extends Controller {
    
    public function getSignUp($request,$response){
        return $this->view->render($response,'signup.twig');
    }
    
    public function postSignUp($request,$response){
        $data = $request->getParsedBody();
        
        $validation = $this->validator->validate($request,[
           'email' => v::noWhitespace()->notEmpty()->email(),
           'name' =>  v::alpha()->notEmpty(),
           'password' =>  v::noWhitespace()->notEmpty(),
        ]);
        
        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor('signup'));           
        }
        $create =  User::create([
                'nombre'=>$request->getParam('name'),
                'email'=>$request->getParam('email'),
                'password'=>password_hash($data['password'], PASSWORD_DEFAULT),
           ]);
                   return $response->withRedirect($this->router->pathFor('home')); 

        
    }
    
    public function getSignIn($request,$response){
        return $this->view->render($response,'signin.twig');
    }
    
    public function postSignIn($request,$response){
        $data = $request->getParsedBody();
        $signin =  Auth::attempt($data['email'],$data['password']);
        if(!$signin){
            return $response->withRedirect($this->router->pathFor('signin'));
        }
        return $response->withRedirect($this->router->pathFor('home'));
    }
}