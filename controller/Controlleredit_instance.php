<?php

require_once "framework/Controller.php";
require_once "model/Instance.php";
require_once "model/User.php";
require_once "model/Form.php";
require_once "model/Question.php";
require_once "model/Answer.php";
require_once "model/Utils.php";

class Controlleredit_instance extends Controller {

    public function index(): void {
        $form_id = $_GET['param1'] ?? null;
        $question_idx = $_GET['param2'] ?? 1; // default to first question if not specified
        $user_id = self::get_user_or_false()->get_id();

        if ($user_id === false) {
            $this->redirect("main");
            return;
        }

        if ($form_id === null) {
            $this->redirect("form");
            return;
        }

        $instance = Instance::get_current_instance($user_id, $form_id);
        $form = Form::get_form_by_id($form_id);
        $questions = Question::get_form_questions($form_id);

        $current_question = null;
        foreach ($questions as $question) {
            if ($question->get_idx() == $question_idx) {
                $current_question = $question;
                break;
            }
        }

        if (!$current_question) {
            $this->redirect("form");
            return;
        }

        $answer = Answer::get_answer($instance, $current_question);

        $current_validation = [
            'is_valid' => true,
            'message' => null
        ];

        if ($answer) {
            $format_errors = Answer::validate_format($current_question, $answer->get_value());
            if (!empty($format_errors)) {
                $current_validation = [
                    'is_valid' => false,
                    'message' => $format_errors[0]
                ];
            }
        }

        // calculate next/previous question indices for display of buttons
        $next_idx = null;
        $prev_idx = null;
        foreach ($questions as $question) {
            if ($question->get_idx() == $question_idx + 1) {
                $next_idx = $question->get_idx();
            }
            if ($question->get_idx() == $question_idx - 1) {
                $prev_idx = $question->get_idx();
            }
        }
        
        $started_time_ago = time_ago($instance->get_started()->format('Y-m-d H:i:s'));
        // $started_time_ago = time_ago($instance->get_started()->format('Y-m-d H:i:s')); faux, c'est celui juste d'avant

        (new View("edit_instance"))->show([
            "form" => $form,
            "instance" => $instance,
            "current_question" => $current_question,
            "answer" => $answer,
            "next_idx" => $next_idx,
            "prev_idx" => $prev_idx,
            "total_questions" => count($questions),
            "started_time_ago" => $started_time_ago,
            "validation" => $current_validation,  // Add validation data
            "questions" => $questions,
            "is_read_only" => false // Mode edition
        ]);
    }

    public function submit(): void {
        $instance_id = $_POST['instance_id'] ?? null;
        $question_id = $_POST['question_id'] ?? null;
        $answer_value = $_POST['answer_value'] ?? null;
        $action = $_POST['action'] ?? null;

        $instance = Instance::get_instance_by_id($instance_id);
        if (!$instance) {
            $this->redirect("form");
            return;
        }

        // Save the current question's answer
        if ($question_id && $answer_value !== null) {
            $current_question = Question::get_question_by_id($question_id);
            if ($current_question) {
                Answer::save($instance, $current_question, $answer_value);
            }
        }

        if ($action === "save_cancel") {
            $this->redirect("form");
            return;
        }

        // Handle navigation
        if ($action === "next") {
            $next_question = Question::next_question($instance->get_form_id(), $current_question->get_idx());
            if ($next_question) {
                $this->redirect("edit_instance", "index", $instance->get_form_id(), $next_question->get_idx());
                return;
            }
        } elseif ($action === "previous") {
            $previous_question = Question::previous_question($instance->get_form_id(), $current_question->get_idx());
            if ($previous_question) {
                $this->redirect("edit_instance", "index", $instance->get_form_id(), $previous_question->get_idx());
                return;
            }
        } elseif ($action === "submit") {
            $errors = $instance->validate();
            if (!empty($errors)) {
                $form_first_error = $instance->get_first_error_idx($errors);
                //$form_first_error = $instance->get_form_id() . '/' . $instance->get_first_error_idx($errors);

                // Stocker cette info dans l'instance
                $instance->set_first_error_idx($instance->$form_first_error);
                $this->redirect("main", 
                    "alert", 
                    "error_submission",
                    "edit_instance", 
                    $instance->get_id()
                );
                // je suis pas sur mais on aura poeut etre besoin de ces param suivants :
                // strval($instance->get_form_id())
                
                //$this->redirect("edit_instance", "index", $instance->get_form_id(), $first_error_idx);
                // il faudra mettre ça comme direction du bouton OK 
                return;
            }

            // Submit the instance
            $instance->submit();
            $this->redirect("main", 
                "alert", 
                "success_submission", 
                "form");
            //$this->redirect("form");
            // il faudra mettre ça dans le bouton OK
        }
    }

    // biss mode guest
    public function start_new_instance(): void {
        $form_id = $_GET['form_id'] ?? null;
        $user = $this->get_user_or_false();

        if (!$form_id) {
            $this->redirect("form", "index");
            return;
        }

        if (!$user) {
            $user = User::get_user_by_email('guest@epfc.eu'); // Retrieve the guest user
        }

        $current_timestamp = new DateTime();

        // Create a new instance
        $instance = new Instance(
            $form_id,
            $user->get_id(),
            $current_timestamp,
            null,
            null
        );

        // Save the new instance
        $instance->save();

        // Redirect to the edit page of the newly created instance
        $this->redirect("edit_instance", "index", $form_id, 1);

        error_log("Form ID: " . $form_id);
        error_log("User ID: " . $user->get_id());
        error_log("Current DateTime: " . $current_timestamp->format('Y-m-d H:i:s'));
    }

        // permet de voir le form soumis en tant qu'éditeur ou utilisateur
        public function view() {
            $instance_id = $_GET['param1'] ?? null;
            $user = $this->get_user_or_redirect();

            if(!$instance_id) {
                $this->redirect("form", "index");
                return;
            }

            $instance = Instance::get_instance_by_id($instance_id);

            if (!$instance) {
                $this->redirect("form", "index");
                return;
            }

            // droit acces utilisateur
            $form = Form::get_form_by_id($instance->get_form_id());
            if (!$form->is_owner($user) && !$form->is_editor($user) && $instance->get_User_id() !== $user->get_id()) {
                $this->redirect("form", "index");
                return;
            }

            // récupérer les réponses et questions
            $questions = Question::get_form_questions($instance->get_form_id());
            $answers = [];
            foreach ($questions as $question) {
                $answer = Answer::get_answer($instance, $question);
                $answers[$question->get_id()] = $answer ? $answer->get_value() : null;
            }

            (new View("edit_instance"))->show([
                "form" => $form,
                "instance" => $instance,
                "questions" => $questions,
                "answers" => $answers,
                "is_read_only" => true // Mode lecture seule
            ]);
        }
}
