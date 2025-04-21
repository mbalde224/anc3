<?php

require_once "model/User_form_accesses.php";


class ControllerUser_form_accesses extends Controller {
    private $formId;
    public function index(): void
    {
        
    }
    public function delete() : void {
        if (isset($_POST['userId']) && $_POST['userId'] != "") {
            $userId = $_POST['userId'];
            if (isset($_POST['formId']) && $_POST['formId'] != "") {
                $this->formId = $_POST['formId'];
                $message = UserFormAccess::deleteAccess($userId,$this->formId);
                $controller = $_POST["controller"];
                $this->redirect("Manage_shares", "index",$this->formId);
            }
        }
    }
    public function reverse(): void {
        if (isset($_POST['userId']) && $_POST['userId'] != "") {
            $userId = $_POST['userId'];
            if (isset($_POST['formId']) && $_POST['formId'] != "") {
                $this->formId = $_POST['formId'];
                $array = UserFormAccess::getAccessByForm_and_user($this->formId,$userId);
                $access_type = $array["access_type"];
                if ($access_type!="") {
                    if ($access_type == "editor"){
                        UserFormAccess::updateAccess($userId,$this->formId,"user");
                        $this->redirect("Manage_shares", "index",$this->formId);
                    }
                    else{
                        UserFormAccess::updateAccess($userId,$this->formId,"editor");
                        $this->redirect("Manage_shares", "index",$this->formId);
                    }
                }
            }
        }
    }
    public function add_User(): void {
        if (isset($_POST['selected_user']) && $_POST['selected_user'] != "") {
            $selected_user = $_POST['selected_user'];
            if ($selected_user != 'User'){
                if (isset($_POST['selected_user2']) && $_POST['selected_user2'] != "") {
                    $selected_user2 = $_POST['selected_user2'];
                    $this->formId = $_POST['formId'];
                    UserFormAccess::addAccess($selected_user,$this->formId,$selected_user2);
                    $this->redirect("Manage_shares", "index",$this->formId);
                }
            }
            else
            {
                $this->formId = $_POST['formId'];
            $this->redirect("Manage_shares", "index",$this->formId);

            }
        }
    }
}