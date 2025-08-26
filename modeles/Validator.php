<?php

class Validator {

    /**
     * Vérifie si une chaîne n'est pas vide
     * @param string $value
     * @return bool
     */
    public static function isNotEmpty($value) {
        return !empty(trim($value));
    }

    /**
     * Vérifie si un email est valide
     * @param string $email
     * @return bool
     */
    public static function isEmailValid($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Vérifie si un mot de passe est fort
     * Doit contenir au moins :
     * - 8 caractères
     * - 1 lettre majuscule
     * - 1 lettre minuscule
     * - 1 chiffre
     * - 1 caractère spécial
     * @param string $password
     * @return bool
     */
    public static function isPasswordStrong($password) {
        $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        return preg_match($pattern, $password);
    }

    /**
     * Vérifie si deux mots de passe sont identiques
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public static function passwordsMatch($password, $confirmPassword) {
        return $password === $confirmPassword;
    }

    /**
     * Vérifie la longueur minimale d'une chaîne
     * @param string $value
     * @param int $minLength
     * @return bool
     */
    public static function minLength($value, $minLength) {
        return strlen(trim($value)) >= $minLength;
    }

    /**
     * Vérifie la longueur maximale d'une chaîne
     * @param string $value
     * @param int $maxLength
     * @return bool
     */
    public static function maxLength($value, $maxLength) {
        return strlen(trim($value)) <= $maxLength;
    }

    /**
     * Assainit une donnée pour éviter les injections et caractères indésirables
     * @param string $data
     * @return string
     */
    public static function sanitize($data) {
        $data = trim($data);                  // Supprime les espaces en début/fin
        $data = str_replace(" ","_",$data); //Remplace les espaces au milieu pour un 
        $data = stripslashes($data);          // Supprime les antislashs
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convertit caractères spéciaux
        return $data;
    }
}
?>
