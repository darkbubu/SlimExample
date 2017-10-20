<?php 
    $app->get('/','HomeController:index')->setName('home');
    
    $app->get('/auth/signup','AuthController:getSignUp')->setName('signup');
    $app->post('/auth/signup','AuthController:postSignUp');
    
    $app->get('/auth/signin','AuthController:getSignIn')->setName('signin');
    $app->post('/auth/signin','AuthController:postSignIn');
