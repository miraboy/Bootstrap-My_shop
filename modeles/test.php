<?php
session_start();
require_once 'Database.php';
require_once 'User.php';
require_once 'Validator.php';
require_once 'Auth.php';
require_once 'logError.php';

function assertTest($condition, $successMsg, $failMsg) {
    if($condition) {
        echo "[PASS] " . $successMsg . "\n";
    } else {
        echo "[FAIL] " . $failMsg . "\n";
    }
}

// --- Connexion à la base ---
$dbInstance = new Database();
$conn = $dbInstance->getConnection();

// --- TEST AUTOMATISÉ DE LA CLASSE USER ---
echo "=== TEST AUTOMATISÉ DE LA CLASSE USER ===\n";

// 1. Création utilisateur normal
$user = new User($conn);
$user->username = "TestUser";
$user->email = "testuser@example.com";
$user->password = "StrongP@ss1";
assertTest($user->signup(), "Utilisateur créé", "Erreur création: ".implode(", ", $user->errors));die;
/*
// 2. Création admin
$admin = new User($conn);
$admin->username = "TestAdmin";
$admin->email = "testadmin@example.com";
$admin->password = "AdminP@ss2";
assertTest($admin->signup(true), "Admin créé", "Erreur création admin: ".implode(", ", $admin->errors));

// 3. Signin utilisateur normal

// 5. Find user by email
$found = $loginUser->findUser('email','testuser@example.com');
assertTest($found != false, "Utilisateur trouvé par email", "Utilisateur non trouvé");


// 4. Signin admin


// 6. Find all users (admin)
$allUsers = $loginAdmin->findAll();
assertTest($allUsers !== false && count($allUsers) >= 2, "FindAll réussi", "Erreur FindAll: ".implode(", ", $loginAdmin->errors));

$updateData = [
    'username' => 'UserUpdated',
    'password' => 'NewStr0ngP@ss1'
];
assertTest($loginUser->update($updateData), "Update utilisateur réussi", "Erreur update: ".implode(", ", $loginUser->errors));*/

$loginUser = new User($conn);
$loginUser->email = "userbyadmin@example.com";
$loginUser->password = "NewStr0ngP@ss1";
$auth= new Auth($loginUser);
assertTest($auth->login(), "Connexion utilisateur réussie", "Erreur connexion utilisateur: ".implode(", ", $loginUser->errors));
// 7. Update utilisateur (username + mot de passe)

$loginAdmin = new User($conn);
$loginAdmin->email = "userbyadmin@example.com";
$loginAdmin->password = "AdminP@ss2";
$auth= new Auth($loginAdmin);
assertTest($auth->login(), "Connexion admin réussie", "Erreur connexion admin: ".implode(", ", $loginAdmin->errors));
/* 8. Admin update autre utilisateur (username + email)
$updateByAdmin = [
    'username' => 'UserByAdmin',
    'email' => 'userbyadmin@example.com'
];
assertTest($loginAdmin->update($updateByAdmin, $loginUser->id), "Admin a mis à jour l'utilisateur", "Erreur update admin: ".implode(", ", $loginAdmin->errors));*/

// 9. Suppression utilisateur (par lui-même)
assertTest($loginUser->delete(), "Utilisateur supprimé par lui-même", "Erreur suppression utilisateur: ".implode(", ", $loginUser->errors));

// 10. Suppression admin
assertTest($loginAdmin->delete($loginAdmin->id), "Admin supprimé", "Erreur suppression admin: ".implode(", ", $loginAdmin->errors));die;

echo "=== FIN DES TESTS AUTOMATISÉS ===\n";
?>
