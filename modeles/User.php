<?php
require_once 'Database.php';
require_once 'Validator.php';
require_once 'logError.php';

class User {
    private $conn;
    private $table = "users";
    public $errors = []; // Messages simples pour l'utilisateur

    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin = 0; // 0 = utilisateur normal, 1 = admin
    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    public function getErrors(){
        return $this->errors;
    }
    /** CREATE / SIGNUP **/
    public function signup($asAdmin = false) {
        try {

            $this->username = Validator::sanitize($this->username);
            $this->email = Validator::sanitize($this->email);
            $this->password = Validator::sanitize($this->password);

            if(!Validator::isNotEmpty($this->username) || !Validator::isNotEmpty($this->email) || !Validator::isNotEmpty($this->password)) {
                $this->errors[] = "Tous les champs sont obligatoires.";
                return false;
            }
            if(!Validator::isEmailValid($this->email)) {
                $this->errors[] = "Email invalide.";
                return false;
            }
            if(!Validator::isPasswordStrong($this->password)) {
                $this->errors[] = "Mot de passe trop faible. Il doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                return false;
            }

            
            if($this->findUser('email', $this->email)) {
                $this->errors[] = "Cet email est déjà utilisé.";
                logError(__CLASS__, __METHOD__, "Tentative d'inscription avec email existant: " . $this->email);
                return false;
            }
            $this->is_admin = $asAdmin ? 1 : 0;
            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
            
            $query = "INSERT INTO " . $this->table . " (username,email,password,is_admin,created_at) VALUES (:username,:email,:password,:is_admin,:created_at)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':is_admin', $this->is_admin);
            $stmt->bindParam(':created_at', date('Y-m-d H:i:s'));

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            $this->errors[] = "Impossible de créer l'utilisateur.";
            logError(__CLASS__, __METHOD__, $e->getMessage());
            return false;
        }
    }

    /** READ / SIGNIN **/
    public function signin() {
        try {
            $this->email = Validator::sanitize($this->email);
            $this->password = Validator::sanitize($this->password);

            if(!Validator::isNotEmpty($this->email) || !Validator::isNotEmpty($this->password)) {
                $this->errors[] = "Tous les champs sont obligatoires.";
                return false;
            }

            $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user && password_verify($this->password, $user['password'])) {
                $this->id = $user['id'];
                $this->username = $user['username'];
                $this->email = $user['email'];
                $this->is_admin = $user['is_admin'];
                return true;
            } else {
                $this->errors[] = "Email ou mot de passe incorrect.";
                logError(__CLASS__, __METHOD__, "Échec de connexion pour email: " . $this->email);
                return false;
            }

        } catch (PDOException $e) {
            $this->errors[] = "Impossible de se connecter.";
            logError(__CLASS__, __METHOD__, $e->getMessage());
            return false;
        }
    }

    /** FIND USER BY colonne **/
    public function findUser($colonne, $value) {
        try {
            $allowedFields = ['id', 'email', 'username'];
            if(!in_array($colonne, $allowedFields)) {
                $this->errors[] = "Critère de recherche invalide.";
                logError(__CLASS__, __METHOD__, "Critère invalide: $colonne");
                return false;
            }
            
            $query = "SELECT * FROM " . $this->table . " WHERE $colonne = :value LIMIT 1";
            $db=$this->conn;
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($user) 
                return $user;

            $this->errors[] = "Utilisateur non trouvé.";
            return false;

        } catch (PDOException $e) {
            $this->errors[] = "Impossible de récupérer l'utilisateur.";
            logError(__CLASS__, __METHOD__, $e->getMessage());
            return false;
        }
    }

    /** FIND ALL USERS (ADMIN ONLY) **/
    public function findAll() {
        if($this->is_admin != 1) {
            $this->errors[] = "Permission refusée.";
            return false;
        }

        try {
            $query = "SELECT id, username, email, is_admin FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($users) return $users;

            $this->errors[] = "Aucun utilisateur trouvé.";
            return false;

        } catch (PDOException $e) {
            $this->errors[] = "Impossible de récupérer les utilisateurs.";
            logError(__CLASS__, __METHOD__, $e->getMessage());
            return false;
        }
    }

    /** UPDATE USER (tous les champs optionnels sauf ID) **/
    public function update($data, $idToUpdate = null) {
        $idToUpdate = $idToUpdate ?? $this->id;
        if(!$idToUpdate) {
            $this->errors[] = "ID utilisateur requis pour la mise à jour.";
            return false;
        }

        if($idToUpdate != $this->id && $this->is_admin != 1) {
            $this->errors[] = "Permission refusée.";
            return false;
        }

        try {
            $fields = [];
            $params = [];

            if(isset($data['username'])) {
                $fields[] = "username=:username";
                $this->username = Validator::sanitize($data['username']);
                $params[':username'] = $this->username;
            }

            if(isset($data['email'])) {
                $fields[] = "email=:email";
                $this->email = Validator::sanitize($data['email']);
                $params[':email'] = $this->email;
            }

            if(isset($data['password'])) {
                if(!Validator::isPasswordStrong($data['password'])) {
                    $this->errors[] = "Mot de passe trop faible.";
                    return false;
                }
                $this->password = password_hash(Validator::sanitize($data['password']), PASSWORD_BCRYPT);
                $fields[] = "password=:password";
                $params[':password'] = $this->password;
            }

            if(empty($fields)) {
                $this->errors[] = "Aucun champ fourni pour la mise à jour.";
                return false;
            }

            $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':id', $idToUpdate, PDO::PARAM_INT);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            $this->errors[] = "Impossible de mettre à jour l'utilisateur.";
            logError(__CLASS__, __METHOD__, $e->getMessage());
            return false;
        }
    }

    /** DELETE USER **/
    public function delete($targetUserId = null) {
        $idToDelete = $targetUserId ?? $this->id;
        if($idToDelete != $this->id && $this->is_admin != 1) {
            $this->errors[] = "Permission refusée.";
            return false;
        }

        try {
            $query = "DELETE FROM " . $this->table . " WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $idToDelete);
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            $this->errors[] = "Impossible de supprimer l'utilisateur.";
            logError(__CLASS__, __METHOD__, $e->getMessage());
            return false;
        }
    }
}
?>