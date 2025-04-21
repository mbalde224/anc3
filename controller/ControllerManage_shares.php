<?php

require_once "framework/Controller.php";
require_once "model/Question.php";
require_once 'model/Form.php';

class ControllerManage_shares extends Controller {
    
    private $formulaire;
    private $Users;
    private $simple_Users;

    public function __construct($formulaire=null)
    {
        $this->formulaire = $formulaire;
    }


    public function index() : void {
        //$this->get_user_or_redirect();
        $this->formulaire = Form::get_form_by_id($_GET['param1']);

        $this->Users = Form::get_Form_Users($this->formulaire);
        $this->simple_Users = User::get_all_simple_Users();
        (new View("Manage_shares"))->show(["users" =>$this->Users,"form" => $this->formulaire,"controller"=>$this,"simple_Users"=>$this->simple_Users]);
        //print("in");
        
        
    }
    public function get_table() : void {
        (new View("Manage_shares"))->show();
        //print("in");
        
        
    }

    public function manage(): void {
        $form_id = $_GET['param1'] ?? null;

        if ($form_id === null) {
            echo "Error: Form ID is missing.";
            return;
        }

        $form = Form::get_form_by_id($form_id);
    
        $questions = Question::get_form_questions($form_id);
    
        (new View("Manage_shares"))->show(['form' => $form, 'questions' => $questions]);
    }

    private function move_question(int $direction) : void {
        $question_id = $_GET['param1'] ?? null;

        if ($question_id === null) {
            echo "Error : Question ID is missing.";
            return;
        }

        $question = Question::get_question_by_id($question_id);

        if (!$question) {
            echo "Error : Question not found.";
            return;
        }

        $form_id = $question->get_form_id();

        $current_idx = $question->get_idx();
        
        Question::swap_questions($form_id, $current_idx, $direction); // 1 to move up, -1 to move down
        $this->redirect("question", "manage", $form_id);
    }

    public function move_up() : void {
        $this->move_question(1);
    }

    public function move_down() : void {
        $this->move_question(-1);
    }
    
}


