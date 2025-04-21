<?php

require_once "framework/Model.php";
require_once "model/Question.php";
require_once "model/Instance.php";
require_once "model/Validation.php";

class Answer extends Model {
    private  $instance;
    private  $question;
    private string $value;

    public function __construct( $instance, $question, string $value) {
        $this->instance = $instance;
        $this->question = $question;
        $this->value = $value;
    }

    public static function is_answered(int $instance_id, int $question_id) : bool {
        $query = self::execute(
            "SELECT * FROM Answers WHERE instance = :instance_id AND question = :question_id",
            [
                "instance_id" => $instance_id,
                "question_id" => $question_id,
            ]
        );

        return $query->rowCount() > 0;
    }

    public static function get_answer(Instance $instance, Question $question) : ?self {
        $query = self::execute(
            "SELECT value FROM Answers WHERE instance = :instance AND question = :question",
            [
                "instance" => $instance->get_id(),
                "question" => $question->get_id()
            ]
        );

        $answer_data = $query->fetch();

        if ($answer_data) {
            return new Answer(
                $instance,
                $question,
                $answer_data["value"]
            );
        }

        return null;
    }
    public static function get_answer_by_questionId($question)  {
        $query = self::execute(
            "SELECT * FROM Answers WHERE  question = :question",
            [
                "question" => $question
            ]
        );

        $answer_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $liste_answer = [];
        if ($answer_data) {
            foreach ($answer_data as $data){

             $liste_answer[] =  new Answer(
                $data["instance"],
                $question,
                $data["value"]
                );
            }
            return $liste_answer;
        }


        return null;
    }

    public static function save(Instance $instance, Question $question, string $value): self {
        $answer = new Answer($instance, $question, $value);
        return $answer->persist();
    }

    private function persist(): self {
        // check if answer already exists
        $query = self::execute(
            "SELECT * FROM Answers WHERE instance = :instance AND question = :question",
            [
                "instance" => $this->instance->get_id(),
                "question" => $this->question->get_id()
            ]
        );
        // if record was returned, update, else, insert new record
        if ($query->fetch()) {
            self::execute(
                "UPDATE Answers SET value = :value 
                 WHERE instance = :instance AND question = :question",
                [
                    "instance" => $this->instance->get_id(),
                    "question" => $this->question->get_id(),
                    "value" => $this->value
                ]
            );
        } else {
            self::execute(
                "INSERT INTO Answers(instance, question, value) 
                 VALUES(:instance, :question, :value)",
                [
                    "instance" => $this->instance->get_id(),
                    "question" => $this->question->get_id(),
                    "value" => $this->value
                ]
            );
        }

        return $this;
    }

    public function get_value(): string {
        return $this->value;
    }

    public static function validate_format(Question $question, string $value) : array {
        $errors = [];

        switch ($question->get_type()) {
            case "email":
                if (!Validation::validate_email($value)) {
                    $errors[] = "Invalid email format";
                }
                break;
            case "date":
                if (!Validation::validate_date($value)) {
                    $errors[] = "Invalid date format";
                }
                break;
        }
        return $errors;   
    }
}