<?php
/**
 * Journalise une erreur détaillée dans logs/errors.log
 * @param string $class Nom de la classe
 * @param string $method Nom de la méthode
 * @param string $message Message détaillé
 */
function logError($class, $method, $message) {
    $logFile = __DIR__ . '/logs/errors.log';

    if(!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true); // Crée le dossier logs si inexistant
    }

    $time = date("Y-m-d H:i:s");
    $logMessage = "[$time] Classe: $class | Méthode: $method | Erreur: $message" . PHP_EOL;

    // Utilisation d'error_log avec option 3 pour écrire dans un fichier spécifique
    error_log($logMessage, 3, $logFile);
}
?>