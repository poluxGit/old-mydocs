<?php
/*
 * Serveur HTTP simple.
 *
 * Pour le tester :
 * 1) Exécutez-le sur le port de votre choix, i.e. :
 * $ php examples/http.php 8010
 * 2) Dans un autre terminal, connectez-vous sur une adresse de ce port
 * et effectuez des requêtes GET ou POST (les autres types sont désactivées dans cet exemple), i.e. :
 * $ nc -t 127.0.0.1 8010
 * POST /about HTTP/1.0
 * Content-Type: text/plain
 * Content-Length: 4
 * Connection: close
 * (press Enter)
 *
 * Il devrait afficher :
 * a=12
 * HTTP/1.0 200 OK
 * Content-Type: text/html; charset=ISO-8859-1
 * Connection: close
 *
 * $ nc -t 127.0.0.1 8010
 * GET /dump HTTP/1.0
 * Content-Type: text/plain
 * Content-Encoding: UTF-8
 * Connection: close
 * (press Enter)
 *
 * Il devrait afficher :
 * HTTP/1.0 200 OK
 * Content-Type: text/html; charset=ISO-8859-1
 * Connection: close
 * (press Enter)
 *
 * $ nc -t 127.0.0.1 8010
 * GET /unknown HTTP/1.0
 * Connection: close
 *
 * Il devrait afficher :
 * HTTP/1.0 200 OK
 * Content-Type: text/html; charset=ISO-8859-1
 * Connection: close
 *
 * 3) Voir ce que le serveur affiche dans le terminal précédent.
 */

function _http_dump($req, $data)
{
    static $counter      = 0;
    static $max_requests = 2;

    if (++$counter >= $max_requests) {
        echo "Le compteur a atteint le nombre maximal de requête ($max_requests). Sortie !\n";
        exit();
    }

    echo __METHOD__, " called\n";
    echo "Requête :";
    var_dump($req);
    echo "Données :";
    var_dump($data);

    echo "\n===== DUMP =====\n";
    echo "Commande :", $req->getCommand(), PHP_EOL;
    echo "URI :", $req->getUri(), PHP_EOL;
    echo "En-têtes en entrée :";
    var_dump($req->getInputHeaders());
    echo "En-têtes en sortie :";
    var_dump($req->getOutputHeaders());

    echo "\n >> Envoi de la réponse ...";
    $req->sendReply(200, "OK");
    echo "OK\n";

    echo "\n >> Lecture du buffer d'entrée ...\n";
    $buf = $req->getInputBuffer();
    while ($s = $buf->readLine(EventBuffer::EOL_ANY)) {
        echo $s, PHP_EOL;
    }
    echo "Il n'y a plus de données dans le buffer\n";
}

function _http_about($req)
{
    echo __METHOD__, PHP_EOL;
    echo "URI : ", $req->getUri(), PHP_EOL;
    echo "\n >> Envoi de la réponse ...";
    $req->sendReply(200, "OK");
    echo "OK\n";
}

function _http_default($req, $data)
{
    echo __METHOD__, PHP_EOL;
    echo "URI: ", $req->getUri(), PHP_EOL;
    echo "\n >> Envoi de la réponse ...";
    $req->sendReply(200, "OK");
    echo "OK\n";
}

$port = 8010;
if ($argc > 1) {
    $port = (int) $argv[1];
}
if ($port <= 0 || $port > 65535) {
    exit("Port invalide");
}

$base = new EventBase();
$http = new EventHttp($base);
$http->setAllowedMethods(EventHttpRequest::CMD_GET | EventHttpRequest::CMD_POST);

$http->setCallback("/dump", "_http_dump", array(4, 8));
$http->setCallback("/about", "_http_about");
$http->setDefaultCallback("_http_default", "valeur de données personnalisées");

$http->bind("0.0.0.0", 8010);
$base->loop();
