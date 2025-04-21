<?php

require_once "framework/Model.php";
require_once "model/User.php";
require_once 'model/Question.php';
require_once 'model/Instance.php';

class Form extends Model {
    private ?int $id;
    private string $title;
    private ?string $description;
    private User $owner;
    private int $is_public;

    public function __construct(User $owner, string $title, int $is_public, ?string $description = null, ?int $id = null) {
        $this->owner = $owner;
        $this->title = $title;
        $this->is_public = $is_public;
        $this->description = $description;
        $this->id = $id;

    }
    public function delete(): void {
        if ($this->id !== null) {
            //  transaction to ensure all related data is deleted
            self::execute("START TRANSACTION", []);
            
            try {
                self::execute(
                    "DELETE FROM Answers WHERE question IN 
                    (SELECT id FROM Questions WHERE form = :form_id)",
                    ["form_id" => $this->id]
                );
                
                self::execute(
                    "DELETE FROM Questions WHERE form = :form_id",
                    ["form_id" => $this->id]
                );
                
                self::execute(
                    "DELETE FROM user_form_accesses WHERE form = :form_id",
                    ["form_id" => $this->id]
                );
                
                self::execute(
                    "DELETE FROM Forms WHERE id = :form_id",
                    ["form_id" => $this->id]
                );
                
                self::execute("COMMIT", []);
            } catch (Exception $e) {
                // if anything goes wrong rollback all changes
                self::execute("ROLLBACK", []);
                throw $e;
            }
        }
    }
    public function is_Instances(){
        return !empty(Instance::get_instances_by_formId($this->id));
    }

    // droit d acces
    public function get_user_access_type($id) : ?string{
        $form_id = $this->get_id();
        $query = self::execute(
        "SELECT user_form_accesses.access_type FROM user_form_accesses where user_form_accesses.form = :form_id AND user_form_accesses.user = :id",
        [
            "form_id" => $form_id,
            "id" => $id
        ]

        );
        $forms_data = $query->fetchAll(PDO::FETCH_ASSOC);
        return $forms_data[0]["access_type"] ?? null ;
    }
    
    public static function get_Form_Users($formt) : array {
        $form_id = $formt->get_id();
        $query = self::execute(
        "SELECT user_form_accesses.user FROM user_form_accesses where user_form_accesses.form = :form_id",
        [
            "form_id" => $form_id
        ]

        );
        $forms_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $Users = [];
        foreach($forms_data as $form_data) {
            $user_id = $form_data['user'];
            $user = User::get_user_by_id($user_id);
            
            $Users[] = $user;
        }
        return $Users;

    }
    public function count_question(){
       return count(Question::get_form_questions($this->id));
    }

    public static function get_user_forms($user) : array {
        $user_id = $user->get_id();

        $query = self::execute(
            "SELECT DISTINCT forms.* FROM forms 
            LEFT JOIN user_form_accesses
                ON forms.id = user_form_accesses.form
            WHERE 
                forms.is_public = TRUE
                OR forms.owner = :user_id
                OR (user_form_accesses.user = :user_id)
            ORDER BY forms.title ASC",
            [
                "user_id" => $user_id
            ]

        );
    

        $forms_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $forms = [];

        foreach($forms_data as $form_data) {
            $owner_id = $form_data['owner'];
            $owner = User::get_user_by_id($owner_id);
            
            $forms[] = new Form(
                $owner,
                $form_data['title'],
                $form_data['is_public'],
                $form_data['description'] ?? null,
                $form_data['id']
            );
        }

        return $forms;
    }

    public static function get_form_by_id( int $form_id): ?self {
        $query = self::execute(
            "SELECT * FROM forms WHERE id = :form_id",
            ["form_id" => $form_id]
        );
        $form_data = $query->fetch(PDO::FETCH_ASSOC);
    
        if (!$form_data) {
            return null;
        }
    
        $owner_id = $form_data['owner'];
        $owner = User::get_user_by_id($owner_id);
        return new Form(
            $owner,
            $form_data['title'],
            $form_data['is_public'],
            $form_data['description'] ?? null,
            $form_data['id']
        );
    }

    public function get_questions() : array {
        return Question::get_form_questions($this->id);
    }

    public function get_required_questions() : array {
        return Question::get_required_questions($this->id);
    }

    public function get_title() : string {
        return $this->title;
    }

    public function get_id() : null|int {
        return $this->id;
    }

    public function get_description() : ?string {
        return $this->description;
    }

    public function get_owner() : User {
        return $this->owner;
    }

    public function is_public() : int {
        return $this->is_public;
    }

    public function persist(): void {
        if ($this->id !== null) {
            self::execute(
                "UPDATE Forms SET title = :title, description = :description, is_public = :is_public WHERE id = :id",
                [
                    "id" => $this->id,
                    "title" => $this->title,
                    "description" => $this->description,
                    "is_public" => $this->is_public
                ]
            );
        } else {
            self::execute(
                // j'ai modifié owner_id par owner (car ça n'existe pas dans la table la colonne owner_id et donc on peut pas créer une Form)
                "INSERT INTO Forms (owner, title, description, is_public) VALUES (:owner, :title, :description, :is_public)",
                [
                    "owner" => $this->owner->get_id(),
                    "title" => $this->title,
                    "description" => $this->description,
                    "is_public" => $this->is_public
                ]
            );
            $this->id = self::lastInsertId();
        }
    }

    private static function validate_title(string $title, User $owner) : array {
        $errors = [];

        if (strlen($title) < 3) {
            $errors[] = "Title must be at least 3 characters long.";
        }

        if (self::is_title_taken($title, $owner)) {
            $errors[] = "Title already taken.";
        }

        return $errors;

    }

    private static function validate_description(?string $description) : array {
        $errors = [];

        if ($description !== "" && strlen($description) < 3) {
            $errors[] = "Description must be at least 3 characters long.";
        }

        return $errors;
    }

    public static function validate_create(string $title, string $description, User $owner) : array {
        return [
            'title' => self::validate_title($title, $owner),
            'description' => self::validate_description($description)
        ];
    }

    private static function is_title_taken(string $title, User $owner) : bool {
        $statement = self::execute(
            "SELECT COUNT(*) AS count FROM Forms where title = :title AND owner = :owner",
            [
                "title" => $title,
                "owner" => $owner->get_id()
            ]
        );

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public static function validate_edit(string $title): array {
        $errors = [];

        if (strlen($title) < 3 || strlen($title) > 60) {
            $errors[] = "Title must be between 3 and 60 characters.";
        }

        return $errors;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function set_description(?string $description): void {
        $this->description = $description;
    }

    public function set_public(bool $is_public): void {
        $this->is_public = $is_public;
    }

    public function set_private(): void {
        $this->set_public(false);
    }

    public static function get_public_forms(): array {
        $query = self::execute("SELECT * FROM forms WHERE is_public = 1 ORDER BY title ASC", []);
        $forms_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $forms = [];

        foreach ($forms_data as $form_data) {
            $owner = User::get_user_by_id($form_data['owner']);
            $forms[] = new Form(
                $owner,
                $form_data['title'],
                $form_data['is_public'],
                $form_data['description'] ?? null,
                $form_data['id']
            );
        }

        return $forms;
    }

    // bis pour droit d acces
    public function is_owner(User $user) : bool {
        return $this->owner->get_id() === $user->get_id();
    }

    // bis pour droit d acces 
    public function is_editor(User $user) : bool {
        return $this->is_owner($user) || $this->get_user_access_type($user->get_id() === 'editor');
    }

    // bis pour droit d auteur
    // verifie si l'utilisateur est en acces limité
    public function can_view_and_answer(User $user): bool {
        return $this->is_owner($user) || in_array($this->get_user_access_type($user->get_id()), ['user', 'editor']);
    }

}
