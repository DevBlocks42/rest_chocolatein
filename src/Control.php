<?php

include_once "DatabaseAccess.php";
include_once "Access.php";

/**
 * Description of Control
 *
 * @author Devblocks42 <devblocks42@keemail.me>
 */
class Control 
{
    private Access $databaseAccess;
    
    public function __construct()
    {
        try
        {
            $this->databaseAccess = new Access();
        } 
        catch (Exception $ex) 
        {
            $this->response(500, "Erreur interne du serveur.");
            die();
        }
    }
    public function unauthorized()
    {
        $this->response(401, "Authentification nécéssaire.");
    }
    /**
     * réception d'une demande de requête
     * demande de traiter la requête puis demande d'afficher la réponse
     * @param string $method
     * @param string $table
     * @param string|null $id
     * @param array|null $fields
     */
    public function request(string $method, string $table, ?string $id, ?array $fields)
    {
        $result = $this->databaseAccess->request($method, $table, $id, $fields);
        $this->checkResult($result);
    }
    /**
     * réponse renvoyée (affichée) au client au format json
     * @param int $code code standard HTTP (200, 500, ...)
     * @param string $message message correspondant au code
     * @param array|int|string|null $result
     */
    private function response(int $code, string $message, array|int|string|null $result="")
    {
        $response = array
        (
            "code" => $code,
            "message" => $message,
            "result" => $result
        );
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    /**
     * contrôle si le résultat n'est pas null
     * demande l'affichage de la réponse adéquate
     * @param array|int|null $result résultat de la requête
     */
    private function checkResult(array|int|null $result) 
    {
        if (!is_null($result))
        {
            $this->response(200, "OK", $result);
        }
        else
        {	
            $this->response(400, "requete invalide");
        }        
    }
    
}
