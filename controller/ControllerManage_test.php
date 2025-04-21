<?php
require_once 'model/Form.php';
require_once 'Controller/ControllerManage_shares.php';
class ControllerManage_test extends Controller
{
    private $model;
    private $controller;

    public function __construct()
    {
        //User user = User::get_user_by_id(2)
        $formulaire = Form::get_form_by_id(15);
        $this->controller = new ControllerManage_shares($formulaire);
        
        //trouver liste uttilisateur 
        // puis afficher
    }
    public function index(): void
    {
        $this->controller->index();
    }
}