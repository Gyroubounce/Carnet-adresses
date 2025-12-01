<?php
declare(strict_types=1);

/**
 * Point d'entrée du programme CLI.
 * Lancer avec : php main.php
 * Boucle de commandes avec messages d'erreur si non reconnues ou mal formatées.
 */

require_once __DIR__ . '/Command.php';

$cmd = new Command();

echo "Carnet d'adresses (CLI)\n";
echo "Tapez 'help' pour les commandes. 'quit' pour sortir.\n";

while (true) {
    $line = readline('> ');
    if ($line === false) {
        // Fin de l'entrée (CTRL+D/EOF)
        echo "\nFin du programme.\n";
        break;
    }
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $cmd->execute($line);
}
