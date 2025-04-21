<?php

require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Role.php";
require_once 'model/Instance.php';
require_once 'framework/View.php';


class ControllerMain extends Controller {

    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("form");
            return;
        }
        
        (new View("signup"))->show([
            "email" => "",
            "password" => "",
            "full_name" => "",
            "errors" => []
        ]);
    }

    public function login() : void {
        if ($this->user_logged()) {
            $this->redirect("form");
            return;
        }
        
        $email = $_POST['email'] ?? ''; 
        $password = $_POST['password'] ?? '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // checks if form was submitted
            $errors = User::validate_login($email, $password);

            if (empty($errors)) {
                $this->log_user(User::get_user_by_email($email));
            }
        }
        $this->show_login_view($email, $password, $errors);
    }

    private function show_login_view(string $email = '', string $password = '', array $errors = []) {
        (new View("login"))->show([
            "email" => $email,
            "password" => $password,
            "errors" => $errors
        ]);
    }

    public function signup() : void {
        if ($this->user_logged()) {
            $this->redirect("form");
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = User::validate_signup($email, $full_name, $password, $confirm_password);

            if (empty($errors)) {
                $user = new User($full_name, $email, password_hash($password, PASSWORD_BCRYPT), Role::USER);
    
                $user->persist();
                $this->log_user($user);
            }
        }

        (new View("signup"))->show([
            "email" => $email,
            "full_name" => $full_name,
            "password" => $password,
            "errors" => $errors
        ]);
    }

     public function guest_login(){
        // Vérifie utilisateur guest
        $guest_user = User::get_user_by_email('guest@epfc.eu');

        if ($guest_user) {
            // Connecter l'utilisateur en tant que guest
            $this->log_user($guest_user);
            $this->redirect('forms', 'index'); // Redirige vers la page des formulaires
        } else {
            die("Impossible pour le moment");
        }
    }


    public function settings(): void {
        if (!isset($_SESSION['user'])){
            $this->redirect("main", "login");
        }
        $user = $this->get_user_or_redirect();
        (new View("settings"))->show(["user" => $user]);
    }

    public function edit_profile(): void {
        $user = $this->get_user_or_redirect();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            
            $errors = User::validate_profile_edit($email, $full_name, $user->get_email());
            
            if (empty($errors)) {
                $user->set_email($email); // setters to update the object in memory
                $user->set_full_name($full_name);
                $user->persist();
                
                $_SESSION['message'] = "Password successfully changed.";
                $this->redirect("main", "settings");
                return;
            }
            
            (new View("edit_profile"))->show([
                "user" => $user,
                "email" => $email,
                "full_name" => $full_name,
                "errors" => $errors
            ]);
            return;
        }
        
        (new View("edit_profile"))->show([
            "user" => $user,
            "email" => $user->get_email(),
            "full_name" => $user->get_full_name(),
            "errors" => []
        ]);
    }

    public function change_password(): void {
        $user = $this->get_user_or_redirect();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $errors = User::validate_password_change($current_password, $new_password, $confirm_password, $user);
            
            if (empty($errors)) {
                $user->set_password(password_hash($new_password, PASSWORD_BCRYPT));
                $user->persist();
                
                $_SESSION['message'] = "Profile successfully changed.";
                $this->redirect("main", "settings");
                return;
            }
            
            (new View("change_password"))->show([
                "errors" => $errors
            ]);
            return;
        }
        
        (new View("change_password"))->show([
            "errors" => []
        ]);
    }

    public function update_settings(): void {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
    
            if ($action === 'edit_profile') {
                $this->redirect("main", "edit_profile");
            } elseif ($action === 'change_password') {
                $this->redirect("main", "change_password");
            } else {
                // action non reconnue
                $this->redirect("main", "settings");
            }
        } else {
            $this->redirect("main", "settings");
        }
    }

    public function login_as() {
        if (!Configuration::is_dev()) {
            die("Cette fonctionnalité est uniquement disponible en mode développement.");
        }

        if (!isset($_GET['email'])) {
            die("Aucun email fourni.");
        }

        $email = $_GET['email'];
        $user = User::get_user_by_email($email);

        if ($user) {
            $this->log_user($user);
            $_SESSION['user'] = $user;
            $this->redirect('form/index');  // Redirection vers la page d'accueil après connexion
        } else {
            die("Utilisateur introuvable.");
        }
    }

    public function alert() : void {
        // On a droit à un troisième argument mais pas besoin forcément (message ou title)

        // recup parametres depuis l'URL
        $type = $_GET['param1'] ?? 'info';
        $redirect_url = $_GET['param2'] ?? 'main'; // ?? "index.php" je ne sais pas lequel est mieux
        $id = isset($_GET['param3']) ? (int) $_GET['param3'] : 0; // conversion en entier
        $form_first_error = null;
        // infos alerte
        $title = "";
        $description = "";
        $icon = "";
        $color = "";
        // infos boutons btn btn-primary btn-lg active
        $button_color = "#2b3035";
        $button_class = "btn btn-primary btn-lg active";
        $buttons = [];

        // gestion param3
        $instance = null;
        $form_id = null;

        if($id) {
            $instance = Instance::get_instance_by_id($id);
            if ($instance) {
                $form_first_error = $instance->get_first_error_idx($instance->validate());
            } else {
                $form_id = $id;
            }
        }

        if($type === "success_submission") {
            $title = "";
            $icon = "bi bi-check2-all";
            $color = "#198754";
            $description = "The form has been successfully submitted";

            $buttons[] = [
                'label' => 'OK',
                'action' => "$redirect_url/index",
                'class' => $button_class,
                'method' => 'get',
                'button_color' => $button_color
            ];
        } elseif($type === 'error_submission') {
            $title = "Are you sure?";
            $icon = "bi bi-exclamation-circle";
            $color = "#dc3545";
            $description = "You must correct all errors before submitting the form.";

            $action = "edit_instance/index/{$instance->get_form_id()}/{$form_first_error}";

            $buttons[] = [
                'label' => 'OK',
                'action' => $action,
                'class' => $button_class,
                'method' => 'post',
                'button_color' => $button_color
            ];
        } elseif($type === 'public-form') {
            $title = "";
            $icon = "";
            $color = "";
            $description = "Are you sure you want to make this form public? <br> 
            This will delete all existing " . '"' . "user" . '"' . " shares";

            $action = "set_form_private/{$form_id}";

            $buttons = [
                [
                    'label' => 'Yes',
                    'action' => 'form/persist_pending_form',
                    'class' => $button_class,
                    'method' => 'post',
                    'button_color' => '#dc3545'
                ],
                [
                    'label' => 'No',
                    'action' => 'form/set_form_private',
                    'class' => $button_class,
                    'method' => 'post',
                    'button_color' => '#6c757d'
                ]
            ];
        }

        /*$action = 'index';
        if ($redirect_url === 'edit_instance') {
            $action = 'index';
        }*/

        /*$redirect_url = $redirect_url . '/' . $action;
        if ($redirect_param) {
            $redirect_url .= '/' . $redirect_param;
        }*/

        (new View("alert"))->show([
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'icon' => $icon,
            'color' => $color,
            'buttons' => $buttons
        ]);

    }

}