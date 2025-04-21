<?php

require_once "framework/Controller.php";
require_once "model/Question.php";
require_once 'model/Form.php';
require_once "model/Type.php";

class ControllerAdd_Question extends Controller {
    private $message = "ok";
    public function index() : void {
        //$this->get_user_or_redirect();
        $form_id = $_GET['param1'] ?? null;
        $form = Form::get_form_by_id((int)$form_id);

          (new View("add_question"))->show([
            "form" => $form,
            "message"=>$this->message
        ]);

    }

    public function is_persit_question($title,$description,$form_id){
        if(strlen($title)>= 3 && strlen($description)>=3){
            foreach(Question::get_form_questions($form_id) as $question){
                if( $question->get_title() == $title){
                    echo "in if2";

                    return false;
                }
            }
            return true;

        }else{
            echo "in else";
            return false;
        }
    }


    public function add(): void {
        $form_id = $_POST['form_id'];
        
        if( isset($_POST["title"]) && isset($_POST['description'])){
            if( $_POST['selected_type']!='--Select à type--')
            {
                $title = $_POST["title"];
                $description = $_POST['description'];
                $selected_type = $_POST['selected_type'];
                if(isset($_POST["required"])){
                    $required = 1;
                }
                else{
                    $required = 0;
                }
                if($this->is_persit_question($title,$description,$form_id)){
                  $form = Form::get_form_by_id($form_id);
                
                $question = new Question($form_id,$form->count_question()+1,$title,Type::tryFrom($selected_type),$required,$description);
                $question->persist();
                $this->redirect("Add_question","index",$form_id);                }
                else
                {
                   $this->message = "title non unique or (title and or description ) under 3 character";
                   $this->redirect("Add_question","index",$form_id);
                }
               
                
            }
            else{
                $this->message =" select type ";
                $this->redirect("Add_question","index",$form_id);

            }
            
            
       
        }else{
            $this->message ="sommething went wrong";
            $this->redirect("Add_question","index",$form_id);
        }
        
        
       
        
        
        
      
    }

}


