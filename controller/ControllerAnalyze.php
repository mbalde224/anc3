<?php

require_once "framework/Controller.php";
require_once "model/Question.php";
require_once 'model/Form.php';
require_once 'model/Answer.php';

class ControllerAnalyze extends Controller {
    
    private $questions;
    private $Users;
    private $form_id;
    private $form;
    private $selected;
    private $nombre_elem = 0;
    private $QuestionId;

    public function __construct($form_id=null)
    {
        $this->form_id = $form_id;
    }


    public function index() : void {
       // $this->get_user_or_redirect();
        $this->form = Form::get_form_by_id($_GET['param1']);
        $this->questions = $this->form->get_questions();
        $this->QuestionId = "-Question-";
        (new View("Analyze"))->show(["questions" =>$this->questions,"form" => $this->form,"controller"=>$this,"selected"=>$this->selected,"QuestionId"=>$this->QuestionId]);
        //print("in");
        
        
    }
    public function show_statistic(){
        // si une question ne possede pas de reponse j actualise la page
        $this->form_id = $_POST['formId'];
        
            if ( $_POST["questionId"] != "-Question-"){
                if (isset($_POST['questionId']) && $_POST['questionId'] != "") {
                    $this->QuestionId = $_POST['questionId'];
                    $liste_reponse =  Answer::get_answer_by_questionId($this->QuestionId);
                    if ( $liste_reponse == null){
                        $this->redirect("Analyze","index",$this->form_id);
                    }
                    else{
                        $this->form = Form::get_form_by_id($this->form_id);
                        $this->questions = $this->form->get_questions();
                        $occurrences = $this->trouver_count($liste_reponse);
                        $this->recall($occurrences);
                    }
                    
                }
            }
            else{
                echo "in else";
                $this->redirect("Analyze","index",$this->form_id);
            }
        
        
    }
    public function trouver_count($liste_reponse){
        $occurrences = [];
        foreach ($liste_reponse as $element) {
            if (isset($occurrences[$element->get_value()])) {
                $occurrences[$element->get_value()]++; // Incrémenter si l'élément existe déjà
                $this->nombre_elem = $this->nombre_elem + 1;
            } else {
                $occurrences[$element->get_value()] = 1; // Initialiser à 1 si c'est la première occurrence
                $this->nombre_elem = $this->nombre_elem + 1;
            }
        }
        return $occurrences;
    }
    public function recall($liste_reponse){
        $titre_current = Question::get_question_by_id($this->QuestionId)->get_title();
        (new View("Analyze"))->show(["questions" =>$this->questions,"form" => $this->form,"controller"=>$this,"selected"=>Question::get_question_by_id($_POST['questionId'])->get_title(),"liste_reponse"=>$liste_reponse,"nombre_elem"=>$this->nombre_elem,"QuestionId"=>$this->QuestionId,"titre_current"=>$titre_current]);

    }
}


