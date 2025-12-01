<?php
require_once 'Command.php';

$cmd = new Command();

while (true) {
    $line = readline("Entrez votre commande : ");
    $cmd->execute($line);
}
