<?php

require_once "framework/Model.php";
require_once "Role.php";

class User extends Model {
    private ?int $id; // null when user not persisted yet, then use lastInsertId() to assign ID to user after persist
    private string $full_name;
    private string $email;
    private string $hashed_password;
    private Role $role;

    public function __construct(string $full_name, string $email, string $hashed_password, Role $role, ?int $id = null) {
        $this->full_name = $full_name;
        $this->email = $email;
        $this->hashed_password = $hashed_password;
        $this->role = $role;
        $this->id = $id;
    }
    
    public function get_role() : Role{
        return $this->role;
    }
    public static function get_all_simple_Users() : array{
        $query = self::execute(
        "SELECT users.* FROM users WHERE users.full_name != 'Administrator' AND users.full_name != 'Anonymous User'",
        [
        
        ]

        );
        $forms_data = $query->fetchAll(PDO::FETCH_ASSOC);
        return $forms_data;
    }

    private static function get_user(string $column, $value) : User|false {
        $query = self::execute("SELECT * FROM Users WHERE {$column} = :value", ["value" => $value]);
        $data = $query->fetch();

        if ($query->rowCount() == 0) return false;

        return new self($data['full_name'], $data['email'], $data['password'], Role::from($data['role']), $data['id']);
    }

    public static function get_user_by_email(string $email) : User|false {
        return self::get_user("email", $email);
    }

    public static function get_user_by_id(int $id) : User|false {
        return self::get_user("id", $id);
    }
    
    private static function is_password_correct(string $clear_password, string $hashed_password) : bool {
        return password_verify($clear_password, $hashed_password);
    }

    public static function validate_login(string $email, string $password) : array {
        $errors = [];
        $user = self::get_user_by_email($email);

        if ($user) {
            if (!self::is_password_correct($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find user with email {$email}. Please sign up"; 
        }

        return $errors;
    }

    private static function validate_email_format(string $email) : array {
        $errors = [];
        if ((filter_var($email, FILTER_VALIDATE_EMAIL)) === false) {
            $errors[] = "Invalid email format.";
        }
        return $errors;
    }

    private static function validate_email_unicity(string $email) : array {
        $errors = [];

        $user = self::get_user_by_email($email);

        if ($user) {
            $errors[] = "This email is already taken.";
        }

        return $errors;
    }

    private static function validate_full_name(string $full_name) : array {
        $errors = [];
        if (strlen($full_name) < 3) {
            $errors[] = "Full name should be at least 3 characters.";
        }
        return $errors;
    }

    private static function validate_password(string $password) : array {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
    
        if (!preg_match('/\d/', $password)) {
            $errors[] = "Password must contain at least one digit.";
        }
    
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
    
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
    
        if (!preg_match('/\W/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }
    
        return $errors;
    }


    private static function validate_confirm_password(string $password, string $confirm_password) : array {
        $errors = [];

        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        return $errors;
    }

    public static function validate_signup(string $email, string $full_name, string $password, string $confirm_password) : array {
        $errors = [];

        $errors = array_merge($errors, self::validate_email_format($email));
        $errors = array_merge($errors, self::validate_email_unicity($email));
        $errors = array_merge($errors, self::validate_full_name($full_name));
        $errors = array_merge($errors, self::validate_password($password));
        $errors = array_merge($errors, self::validate_confirm_password($password, $confirm_password));

        return $errors;
    }

    public function persist() : self {
        if ($this->id !== null) {
            // Update existing user
            self::execute(
                "UPDATE Users SET full_name = :full_name, email = :email, password = :password, role = :role WHERE id = :id",
                [
                    "id" => $this->id,
                    "full_name" => $this->full_name,
                    "email" => $this->email,
                    "password" => $this->hashed_password,
                    "role" => $this->role->value
                ]
            );
        } else {
            self::execute(
                "INSERT INTO Users(full_name, email, password, role) VALUES(:full_name, :email, :password, :role)",
                [
                    "full_name" => $this->full_name,
                    "email" => $this->email,
                    "password" => $this->hashed_password,
                    "role" => $this->role->value
                ]
            );
            $this->id = self::lastInsertId();
        }
    
        return $this; 
    }

    public function get_forms() : array {
        $forms = Form::get_user_forms($this);
        return $forms;
    }

    public function get_id() : ?int {
        return $this->id;
    }

    public function get_full_name() : string {
        return $this->full_name;
    }
    
    public function get_email(): string {
        return $this->email;
    }

    public function get_hashed_password(): string {
        return $this->hashed_password;
    }

    public function set_email(string $email): void {
        $this->email = $email;
    }

    public function set_full_name(string $full_name): void {
        $this->full_name = $full_name;
    }

    public function set_password(string $hashed_password): void {
        $this->hashed_password = $hashed_password;
    }

    public static function validate_profile_edit(string $email, string $full_name, string $current_email): array {
        $errors = [];
        
        $errors = array_merge($errors, self::validate_email_format($email));
        // only check email uniqueness if different from current email
        if ($email !== $current_email) {
            $errors = array_merge($errors, self::validate_email_unicity($email));
        }
        $errors = array_merge($errors, self::validate_full_name($full_name));
        
        return $errors;
    }

    public static function validate_password_change(string $current_password, string $new_password, string $confirm_password, User $user): array {
        $errors = [];
        
        if (!password_verify($current_password, $user->get_hashed_password())) {
            $errors[] = "Current password is incorrect.";
            return $errors; // Return early to prevent further validation if current password is wrong
        }
        
        $errors = array_merge($errors, self::validate_password($new_password));
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
        
        if (password_verify($new_password, $user->get_hashed_password())) {
            $errors[] = "New password must be different from current password.";
        }
        
        return $errors;
    }

    // bis droit d acces
    public function can_edit_form(Form $form): bool {
        return $form->is_owner($this) || $form->get_user_access_type($this->get_id()) === 'editor';
    }

    // bis droit d acces 
    public function can_answer_form(Form $form): bool {
        return $form->can_view_and_answer($this);
    }
}