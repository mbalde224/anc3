<?php

require_once "framework/Model.php";
require_once "model/Type.php";

class Question extends Model {
    private int $form_id;
    private int $idx;
    private string $title;
    private Type $type;
    private int $required;
    private ?string $description;
    private ?int $id;
    private const TEMP_IDX = -1;


    public function __construct(int $form_id, int $idx, string $title, Type $type, int $required, ?string $description,  $id=null) {
        $this->form_id = $form_id;
        $this->idx = $idx;
        $this->title = $title;
        $this->type = $type;
        $this->required = $required;
        $this->description = $description;
        $this->id = $id;
    }

    public static function get_question_by_id(int $id) : self {
        $query = self::execute(
            "SELECT * FROM Questions WHERE id = :id",
            ["id" => $id]
        );

        $question_data = $query->fetch();

        return new Question(
            $question_data['form'],
            $question_data['idx'],
            $question_data['title'],
            Type::from($question_data['type']),
            $question_data['required'],
            $question_data['description'],
            $question_data['id']
        );
    }

    public static function get_required_questions(int $form_id) : array {
        $query = self::execute(
            "SELECT * FROM Questions WHERE form = :form_id AND required = 1 ORDER BY idx ASC",
            ["form_id" => $form_id]
        );

        $questions_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $required_questions = [];

        foreach($questions_data as $question_data) {
            $required_questions[] = new Question(
                $question_data['form'],
                $question_data['idx'],
                $question_data['title'],
                Type::from($question_data['type']),
                $question_data['required'],
                $question_data['description'],
                $question_data['id']
            );
        }
        return $required_questions;
    }

    public static function get_form_questions( $form_id) : array {
        $query = self::execute(
            "SELECT * FROM Questions WHERE form = :form_id ORDER BY idx ASC",
            [
                "form_id" => $form_id
            ]
        );

        $questions_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $questions = [];

        foreach ($questions_data as $question_data) {
            $questions[] = new Question(
                $question_data['form'],
                $question_data['idx'],
                $question_data['title'],
                Type::from($question_data['type']),
                $question_data['required'],
                $question_data['description'],
                $question_data['id']
            );
        }

        return $questions;
    }

    public function get_title() : string {
        return $this->title;
    }

    public function get_description() : null|string {
        return $this->description;
    }

    public function get_id() : ?int {
        return $this->id;
    }

    public function get_type() : string {
        return $this->type->value;
    }

    public function is_required() : bool {
        return $this->required === 1;
    }

    public function get_idx() : int {
        return $this->idx;
    }

    public function get_form_id() : int {
        return $this->form_id;
    }

    public static function swap_questions(int $form_id, int $current_idx, int $direction): void {
        if ($direction === 1 && $current_idx === 1) {
            return; // The first question can't move up
        }
    
        if ($direction === -1 && $current_idx === self::get_max_idx($form_id)) {
            return; // last question can't move down
        }
    
        $adjacent_idx = $current_idx + ($direction * -1);
    
        // Temporarily set current question's idx to -1 to avoid unique constraint errors
        self::execute(
            "UPDATE Questions SET idx = :temp_idx WHERE form = :form_id AND idx = :current_idx",
            [
                "temp_idx" => self::TEMP_IDX,
                "form_id" => $form_id,
                "current_idx" => $current_idx
            ]
        );
    
        // Move the adjacent question to the current question's idx
        self::execute(
            "UPDATE Questions SET idx = :current_idx WHERE form = :form_id AND idx = :adjacent_idx",
            [
                "current_idx" => $current_idx,
                "form_id" => $form_id,
                "adjacent_idx" => $adjacent_idx
            ]
        );
    
        // Move the current question to the adjacent question's original idx
        self::execute(
            "UPDATE Questions SET idx = :adjacent_idx WHERE form = :form_id AND idx = -1",
            [
                "adjacent_idx" => $adjacent_idx,
                "form_id" => $form_id
            ]
        );
    }
    

    public static function get_max_idx(int $form_id) : int {
        $query = self::execute(
            "SELECT Max(idx) AS max_idx FROM Questions WHERE form = :form_id",
            ["form_id" => $form_id]
        );

        $result = $query->fetch();

        return $result ? (int)$result['max_idx'] : 0;
    }

    public function persist(): self {
        if ($this->id === null) {
            // New question: INSERT
            self::execute(
                "INSERT INTO Questions (form, idx, title, description, type, required) 
                 VALUES (:form_id, :idx, :title, :description, :type, :required)",
                [
                    "form_id" => $this->form_id,
                    "idx" => $this->idx,
                    "title" => $this->title,
                    "description" => $this->description,
                    "type" => $this->type->value,
                    "required" => $this->required
                ]
            );
            $this->id = self::lastInsertId();
        } else {
            // Existing question: UPDATE
            self::execute(
                "UPDATE Questions 
                 SET title = :title, description = :description, type = :type, required = :required 
                 WHERE id = :id",
                [
                    "title" => $this->title,
                    "description" => $this->description,
                    "type" => $this->type->value,
                    "required" => $this->required,
                    "id" => $this->id
                ]
            );
        }
        return $this;
    }
    

    public function delete(): void {
        $form_id = $this->form_id;
        $current_idx = $this->idx;
        
        self::execute(
            "DELETE FROM Questions WHERE id = :id",
            ["id" => $this->id]
        );
        
        // reindexing of questions after deletion
        self::execute(
            "UPDATE Questions 
             SET idx = idx - 1 
             WHERE form = :form_id 
             AND idx > :current_idx",
            [
                "form_id" => $form_id,
                "current_idx" => $current_idx
            ]
        );
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function set_description(?string $description): void {
        $this->description = $description;
    }

    public function set_type(Type $type): void {
        $this->type = $type;
    }

    public function set_required(bool $required): void {
        $this->required = $required;
    }

    public function set_idx(int $idx): void {
        $this->idx = $idx;
    }

    public function set_form_id(int $form_id): void {
        $this->form_id = $form_id;
    }

    public static function next_question(int $form_id, int $current_idx): ?self {
        $query = self::execute(
            "SELECT * FROM Questions WHERE form = :form_id AND idx > :current_idx ORDER BY idx ASC LIMIT 1",
            [
                "form_id" => $form_id,
                "current_idx" => $current_idx
            ]
        );
    
        $question_data = $query->fetch();
        if ($question_data) {
            return new Question(
                $question_data['form'],
                $question_data['idx'],
                $question_data['title'],
                Type::from($question_data['type']),
                $question_data['required'],
                $question_data['description'],
                $question_data['id']
            );
        }
        return null; // Aucun autre élément
    }
    
    public static function previous_question(int $form_id, int $current_idx): ?self {
        $query = self::execute(
            "SELECT * FROM Questions WHERE form = :form_id AND idx < :current_idx ORDER BY idx DESC LIMIT 1",
            [
                "form_id" => $form_id,
                "current_idx" => $current_idx
            ]
        );
    
        $question_data = $query->fetch();
        if ($question_data) {
            return new Question(
                $question_data['form'],
                $question_data['idx'],
                $question_data['title'],
                Type::from($question_data['type']),
                $question_data['required'],
                $question_data['description'],
                $question_data['id']
            );
        }
        return null; // Aucun élément précédent
    }

}