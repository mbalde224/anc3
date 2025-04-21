<?php
require_once 'model/Form.php';
require_once 'model/Instance.php';
class ControllerInstances extends Controller
{
    private $questions;
    private $Users;
    private $form_id;
    private $form;
    private $selected;
    private $nombre_elem = 0;
    private $listes_instance;
    private $liste_Users;

    public function index(): void
    {
        $this->form = Form::get_form_by_id($_GET['param1']);
        // j'ai changé get_instances_by_formId() par get_completed_instances_by_formId()
        $this->listes_instance = Instance::get_completed_instances_by_formId($_GET['param1']);
        $this->liste_Users = [];
        foreach($this->listes_instance as $Instance){
        
            $this->liste_Users[] = User::get_user_by_id($Instance->get_User_id());
            
        }
        
        (new View("Instances"))->show(["questions" =>$this->questions,"form" => $this->form,"controller"=>$this,"selected"=>"-Question-","listes_instance"=>$this->listes_instance,"liste_Users"=>$this->liste_Users]);

    }
    public function delete(){
        if (isset($_POST['ids']) ) {
            $this->form = Form::get_form_by_id($_POST["form_id"]);
            $liste = $_POST['ids'];
            print_r( $liste);
            if(isset($_POST['ids']) && $liste[0]!=""){
                foreach ($liste as $id){
                    Instance::delete_by_Id($id);
                }
                $this->redirect("Instances","index",$this->form->get_id());
                
            }
            else{
                $this->redirect("Instances","index",$this->form->get_id());
            }
        }
        else{
            echo "in else";
            $this->form = Form::get_form_by_id($_POST["form_id"]);
            $this->redirect("Instances","index",$this->form->get_id());
        }
    
       
        
    }
}
