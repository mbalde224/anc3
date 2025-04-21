<?php

require_once "framework/Controller.php";
require_once "model/Form.php";
require_once "model/User.php";
require_once "model/Instance.php";
require_once "model/Utils.php";

class ControllerForm extends Controller {

    public function index(): void {
        $user = $this->get_user_or_redirect();
        $forms = $user->get_forms();
        $form_data = [];
    
        foreach ($forms as $form) {
            // pour eviter les erreurs ailleurs faudrait garder $instance qq part ou modifier ailleur

            // $instance = Instance::get_current_instance($user->get_id(), $form->get_id());
            // $started_time_ago = $instance ? time_ago($instance->get_started()->format('Y-m-d H:i:s')) : null;

            // derniere instance si y'en a une
            $last_instance = Instance::get_last_instance_for_user($form->get_id(), $user->get_id());

            $started_time_ago = null;
            $completed_time_ago = null;
            $status = "Not started"; // statut par défaut si pas d'instance

            if ($last_instance !== null) {
                $started_time_ago = time_ago($last_instance->get_started()->format('Y-m-d H:i:s'));
                
                if($last_instance->get_completed()) {
                    $completed_time_ago = time_ago($last_instance->get_completed()->format('Y-m-d H:i:s'));
                    $status = "Completed: $completed_time_ago";
                } else {
                    $status = "In progress...";
                }
            } else { $started_time_ago === null; }


            
            $form_data[] = [
                'form' => $form,
                'started_time_ago' => $started_time_ago, 
                'completed_time_ago' => $completed_time_ago,
                'status' => $status
            ];
        }
    
        (new View("forms"))->show([
            'form_data' => $form_data,
            'user' => $user
        ]);
    }

    public function create() : void {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $is_public = isset($_POST['is_public']) ? 1 : 0;
        $owner = $this->get_user_or_redirect();
        $errors = [
            'title' => [],
            'description' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = Form::validate_create($title, $description, $owner);

            if (empty($errors['title']) && empty($errors['description'])) {
                $form = new Form($owner, $title, $is_public, $description);

                // si form public : alerte de confirmation
                if ($is_public === 1) {
                    // stocker temporairement le form dans la session
                    $_SESSION['pending_form'] = $form;
                    $this->redirect("main",
                    "alert",
                    "public-form", 
                    "form/index"
                );
                }
                // sinon enregistrer directement
                $form->persist();

                $this->redirect("form", "index");
            }
        }

        (new View("create_form"))->show(["errors" => $errors]);
    }

    public function edit(): void {
        $form_id = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $form = Form::get_form_by_id($form_id);

        if (!$form || !$form->is_editor($user)) {
            $this->redirect("form", "index");
            return;
        }

        $title = $_POST['title'] ?? $form->get_title();
        $description = $_POST['description'] ?? $form->get_description();
        $is_public = $_SERVER['REQUEST_METHOD'] === 'POST' ? (isset($_POST['is_public']) ? 1 : 0) : $form->is_public();

        // unchecked checkbox is not returned in post array; so we let variable explicitly know
        // if post array is not empty but no checkbox value (no public) then private
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = Form::validate_edit($title);

            if (empty($errors)) {
                $form->set_title($title);
                $form->set_description($description);
                $form->set_public($is_public);
                $form->persist();

                $this->redirect("form", "index");
                return;
            }
        }

        (new View("edit_form"))->show([
            "form" => $form,
            "title" => $title,
            "description" => $description,
            "is_public" => $is_public,
            "errors" => $errors
        ]);
    }

    public function toggle_visibility(): void {
        $form_id = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $form = Form::get_form_by_id($form_id);

        if (!$form || $form->get_owner()->get_id() !== $user->get_id()) {
            $this->redirect("form", "index");
            return;
        }

        $form->set_public(!$form->is_public());
        $form->persist();

        $this->redirect("question", "manage", $form_id);
    }

    public function delete(): void {
        $form_id = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $form = Form::get_form_by_id($form_id);
        
        if (!$form || !$form->is_owner($user)) {
            $this->redirect("form", "index");
            return;
        }

        $form->delete();
        $this->redirect("form", "index");
    }

    public function set_form_private(): void {
        $form = $_SESSION['pending_form'] ?? null;

        if ($form) {
            // rendre le form privé
            $form->set_private();
            //persister le changement 
            $form->persist();
            // supprimer le formulaire de la session
            unset($_SESSION['pending_form']);
            // rediriger vers la page précédente ou l'index 
            $this->redirect("form", "index");
        } 
    }

    public function persist_pending_form(): void {
        // Récupérer le formulaire temporaire de la session
        $form = $_SESSION['pending_form'] ?? null;

        if ($form) {
            // Persister le formulaire
            $form->persist();

            // Nettoyer la session
            unset($_SESSION['pending_form']);

            // Rediriger vers form/index
            $this->redirect("form", "index");
        } else {
            $this->redirect("error", "not_found");
        }
    }
}
