<?php

require_once "framework/Controller.php";
require_once "model/Answer.php";
require_once "model/Question.php";
require_once "model/Form.php";
require_once "model/Instance.php";


class ControllerAnswer extends Controller {

    public function index(): void {
        $instance_id = $_POST["instance_id"] ?? null;
        $current_question_id = $_POST["question_id"] ?? null;
        $answer_value = $_POST['answer_value'] ?? null;
        $direction = $_POST['direction'] ?? null;

        $instance = Instance::get_instance_by_id($instance_id); 
        $current_question = Question::get_question_by_id($current_question_id); 

        $answer = Answer::save($instance, $current_question, $answer_value);

        $form_id = $instance->get_form_id();
        $form = Form::get_form_by_id($form_id);

        $questions = Question::get_form_questions($form_id);

        // find current position
        $current_idx = null;
        foreach($questions as $q) {
            if ((int)$q->get_id() === (int)$current_question_id) { 
                $current_idx = $q->get_idx();
                break;
            }
        }

        if ($current_idx === null) {
            throw new Exception("Current question idx not found.");
        }

        // calculate target idx
        $target_idx = $current_idx;
        if ($direction === 'prev') {
            $target_idx = $current_idx - 1;
        } elseif ($direction === 'next') {
            $target_idx = $current_idx + 1;
        }

        $this->redirect("instance", "index", $form_id, (string)$target_idx);

    }
}