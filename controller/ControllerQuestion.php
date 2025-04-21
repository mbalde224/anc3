<?php

require_once "framework/Controller.php";
require_once "model/Question.php";
require_once "model/Instance.php";
require_once 'model/Form.php';
require_once "model/Type.php";

class ControllerQuestion extends Controller {

    public function index() : void {
        
    }

    public function manage(): void {
        $user = $this->get_user_or_false();
        $form_id = $_GET['param1'] ?? null;

        if ($form_id === null) {
            echo "Error: Form ID is missing.";
            return;
        }

        $form = Form::get_form_by_id($form_id);

        $instance = Instance::get_current_instance_bis($user->get_id(), $form_id);
    
        $questions = Question::get_form_questions($form_id);
    
        (new View("form"))->show(['form' => $form, 'questions' => $questions, 'user' => $user, 'instance' => $instance]);
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

    public function delete(): void {
        $question_id = $_GET['param1'] ?? null;
        
        if (!$question_id) {
            $this->redirect("form");
            return;
        }
        
        $question = Question::get_question_by_id($question_id);
        
        if (!$question) {
            $this->redirect("form");
            return;
        }
        
        $form_id = $question->get_form_id();
        $question->delete();
        
        $this->redirect("question", "manage", (string)$form_id);
    }

    public function add(): void {
        $form_id = $_GET['param1'] ?? null;
        $question_id = $_GET['param2'] ?? null;
        
        if (!$form_id) {
            $this->redirect("form");
            return;
        }
        
        $form = Form::get_form_by_id($form_id);
        
        if (!$form) {
            $this->redirect("form");
            return;
        }

        $question = null;

        if ($question_id) {
            $question = Question::get_question_by_id($question_id);
            if (!$question || $question->get_form_id() !== (int)$form_id) {
                $this->redirect("form");
                return;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $type = $_POST['type'] ?? 'short'; // default to short if missing
            $required = isset($_POST['required']);

            if ($question) {
                $question->set_title($title);
                $question->set_description($description);
                $question->set_type(Type::from($type));
                $question->set_required($required);
            } else {
                $new_idx = Question::get_max_idx($form_id) + 1;
                $question = new Question(           
                    $form_id,
                    $new_idx,
                    $title,
                    Type::from($type),
                    $required,
                    $description,
                    null
                );
            }

            $question->persist();
            $this->redirect("question", "manage", (string)$form_id);
            return;
        }
        
        (new View("add_question"))->show([
            "form" => $form,
            "question" => $question
        ]);
    }

}


