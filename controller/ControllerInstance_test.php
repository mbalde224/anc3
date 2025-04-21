<?php
require_once 'model/Form.php';
require_once 'model/User.php';
require_once 'Controller/ControllerInstances.php';

class ControllerInstance extends Controller
{   
    private Form $form;
    private $model;
    private ControllerInstances $controller;
    private array $users;

    public function __construct()
    {
        //User user = User::get_user_by_id(2)
        $formulaire = Form::get_form_by_id(15);
        $this->controller = new ControllerInstances($formulaire);
        
        //trouver liste uttilisateur 
        // puis afficher
    }
    public function index(): void
    {
        $this->controller->index();
    }
}