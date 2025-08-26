<?php
require_once 'User.php';

class Auth {
    private $user;
    public $errors = [];

    public function __construct(User $user =null ) {
        $this->user = $user;
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Connexion et création de session **/
    public function login() {

        if($this->user->signin()) {
            // Stocke les informations dans la session
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['is_admin'] = $this->user->is_admin;
            return true;
        } else {
            $this->errors = $this->user->errors;
            return false;
        }
    }

    /*** Vérifie si un utilisateur est connecté ***/
    public function check() {
        return isset($_SESSION['user_id']);
    }

    /*** Déconnexion ***/
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    /*** Vérifie si l'utilisateur connecté est admin ***/
    public function isAdmin() {
        return $this->check() && !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    /*** Récupère les informations de l'utilisateur connecté ***/
    public function userInfo() {
        if($this->check()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'is_admin' => $_SESSION['is_admin']
            ];
        }
        return null;
    }
}
?>
