<?php

require_once "framework/Model.php";
require_once "model/Form.php";
require_once "model/Answer.php";



class Instance extends Model {
    private int $form_id;
    private int $user_id;
    private DateTime $started;
    private ?DateTime $completed;
    private ?int $id;
    private ?int $first_error_idx = null;

    public function __construct(int $form_id, int $user_id, DateTime $started, ?DateTime $completed, ?int $id) {
        $this->form_id = $form_id;
        $this->user_id = $user_id;
        $this->started = $started;
        $this->completed = $completed;
        $this->id = $id;
    }

    public static function get_instance_by_id(int $id): ?self {
        $query = self::execute(
            "SELECT * FROM Instances WHERE id = :id", ["id" => $id]
        );

        $instance_data = $query->fetch();

        if (!$instance_data) {
            return null;
        }

        return new Instance(
            $instance_data["form"],
            $instance_data["user"],
            new DateTime($instance_data["started"]),
            $instance_data["completed"] ? new DateTime($instance_data["completed"]) : null,
            $instance_data["id"]
        );
    }
    public static function get_instances_by_formId(int $formId)  {
    
        $query = self::execute(
            "SELECT * FROM Instances WHERE form = :form_Id", ["form_Id" => $formId]
        );
        $Instances_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $Listes_instance = [];
        foreach($Instances_data as $instance_data) {
            $Listes_instance[] = new Instance(
                $instance_data["form"],
                $instance_data["user"],
                new DateTime($instance_data["started"]),
                $instance_data["completed"] ? new DateTime($instance_data["completed"]) : null,
                $instance_data["id"]
            );
        }
        return $Listes_instance;

    }

    //bis : pour voir si le form est complété 
    public function get_completed(): ?DateTime {
        return $this->completed;
    }

    public static function get_current_instance($user_id, $form_id) : self {
        $query = self::execute(
            "SELECT * FROM Instances WHERE user = :user_id AND form = :form_id AND completed IS NULL LIMIT 1",
            [
                "user_id" => $user_id,
                "form_id" => $form_id
            ]
        );

        $instance_data = $query->fetch();

        if ($instance_data) {
            return new Instance(
                $instance_data["form"],
                $instance_data["user"],
                new DateTime($instance_data["started"]),
                $instance_data["completed"] ? new DateTime($instance_data["completed"]) : null,
                $instance_data["id"]
            );
        }

        // if no current instance we create a new one
        return self::create_new_instance($user_id, $form_id);
    }

    private static function create_new_instance($user_id, $form_id) : self {
        $instance = new Instance($form_id, $user_id, new DateTime(), null, null);
        return $instance->persist();
    }

    private function persist() : self {
        self::execute(
            "INSERT INTO Instances(form, user, started, completed) VALUES(:form_id, :user_id, :started, NULL)",
            [
                "user_id" => $this->user_id,
                "form_id" => $this->form_id,
                "started" => $this->started->format('Y-m-d H:i:s')
            ]
        );

        $this->id = self::lastInsertId();
        return $this;
    }

    public function submit() : void {
        self::execute(
            "UPDATE Instances SET completed = NOW() WHERE id = :id", ["id" => $this->id]
        );
    }

    public function get_id() : ?int {
        return $this->id;
    }

    public function get_started() : DateTime {
        return $this->started;
    }

    public function get_form_id(): int {
        return $this->form_id;
    }
    public function get_User_id():int {
        return $this->user_id;
    }

    //bis 
    public function is_completed() : bool {
        return $this->completed !== null;
    }

    public function delete(){

        self::execute(
            "DELETE FROM Instances  WHERE id = :id_", ["id_" => $this->id]
        );
    }
    public static function delete_by_Id($id){

        self::execute(
            "DELETE FROM Instances  WHERE id = :id", ["id" => $id]
        );
    }

    public function validate() : array {
        $form = Form::get_form_by_id($this->form_id);
        $required_questions = $form->get_required_questions();
        $errors = [];

        foreach($required_questions as $question) {
            $answer = Answer::get_answer($this, $question);

            if (!$answer || trim($answer->get_value()) === '') {
                $errors[$question->get_idx()] = [
                    'message' => 'This field is required'
                ];
                continue; // if question is unanswered skip format validation, go to next question
            }

            $format_errors = Answer::validate_format($question, $answer->get_value());
            if (!empty($format_errors)) {
                $errors[$question->get_idx()] = [
                    'message' => $format_errors[0]
                ];
            }
        }
        return $errors;
    }

    public function get_first_error_idx(array $errors) : ?int {
        if (empty($errors)) {
            return null;
        }
        return min(array_keys($errors));
    }

    //bis
    public static function get_last_instance_for_user(int $form_id, int $user_id): ?Instance {
        $query = self::execute(
            "SELECT * FROM instances WHERE form = :form_id AND user = :user_id ORDER BY started DESC LIMIT 1",
            [
                "form_id" => $form_id,
                "user_id" => $user_id
            ]
        );
        $instance_data = $query->fetch(PDO::FETCH_ASSOC);
    
        return $instance_data ? new Instance(
            $instance_data['form'], 
            $instance_data['user'], 
            new DateTime($instance_data['started']), 
            $instance_data['completed'] ? new DateTime($instance_data["completed"]) : null, 
            $instance_data['id']) : null;
    }

    // bis : pour garder en private persist
    public function save() : void {
        $this->persist();
    }

    public function set_first_error_idx(?int $idx) : void {
        $this->first_error_idx = $idx;
    }

    public static function get_completed_instances_by_formId(int $formId) {
        $query = self::execute(
            "SELECT * FROM Instances WHERE form = :form_Id AND completed IS NOT NULL",
            ["form_Id" => $formId]
        );
        $Instances_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $Listes_instance = [];
        foreach($Instances_data as $instance_data) {
            $Listes_instance[] = new Instance(
                $instance_data["form"],
                $instance_data["user"],
                new DateTime($instance_data["started"]),
                $instance_data["completed"] ? new DateTime($instance_data["completed"]) : null,
                $instance_data["id"]
            );
        }
        return $Listes_instance;
    }

    public static function get_current_instance_bis($user_id, $form_id) : ?self {
        $query = self::execute(
            "SELECT * FROM Instances WHERE user = :user_id AND form = :form_id AND completed IS NULL LIMIT 1",
            [
                "user_id" => $user_id,
                "form_id" => $form_id
            ]
        );

        $instance_data = $query->fetch();

        if ($instance_data) {
            return new Instance(
                $instance_data["form"],
                $instance_data["user"],
                new DateTime($instance_data["started"]),
                $instance_data["completed"] ? new DateTime($instance_data["completed"]) : null,
                $instance_data["id"]
            );
        }

        return null;
    }

}