<?php

require_once "framework/Controller.php";
require_once "model/Question.php";
require_once 'model/Form.php';
require_once 'ControllerAnalyze.php';

class ControllerAnalyse_test extends Controller {
    
    private $controller;
    public function __construct()
    {
        $this->controller = new ControllerAnalyze(16);
    }


    public function index() : void {

        $this->controller->index();        
        
    }
   


 
}
