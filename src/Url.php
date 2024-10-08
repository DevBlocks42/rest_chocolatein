<?php

require '../vendor/autoload.php';
use Dotenv\Dotenv;

/**
 * Description of Url
 *
 * @author Devblocks42 <devblocks42@keemail.me>
 */
class Url
{
    private static ?Url $instance = null;
    private array $data = [];
    
    private function __construct()
    {
        $this->dotenv = Dotenv::createImmutable(__DIR__);
        $this->dotenv->load();
        $this->data = $this->getAllData();
    }
    public static function getInstance()
    {
        if(self::$instance == null)
        {
            self::$instance = new Url();
        }
        return self::$instance;
    }
    /**
     * récupération de toutes les variables envoyées par l'URL
     * nettoyage et retour dans un tableau associatif
     * @return array
     */
    private function getAllData() : array 
    {
        $data = [];
        if(!empty($_GET))
        {
            $data = array_merge($data, $_GET);
        }
        if(!empty($_POST))
        {
            $data = array_merge($data, $_POST);
        }
        $input = file_get_contents("php://input");
        parse_str($input, $postData);
        $data = array_merge($data, $postData);
        $data = array_map(function($value) 
        {
            return htmlspecialchars($value, ENT_NOQUOTES);
        }, $data);
        return $data;
    }
    /**
     * retour d'une variable avec les caractères spéciaux convertis
     * et au format array si format "json" reçu
     * possibilité d'ajouter d'autres 'case' de conversions
     * @param string $nom
     * @param string $format
     * @return string|array|null
     */
    public function getVariable(string $name, string $format = "string") : string|array|null
    {
        $variable = $this->data[$name] ?? '';
        switch($format)
        {
            case "json":
                $variable = $variable ? json_decode($variable, true) : null;
                break;
        }
        return $variable;
    }
    /**
     * récupère la méthode HTTP utilisée pour le transfert
     * @return string
     */
    public function getHTTPMethod() : string 
    {
        return filter_input(INPUT_SERVER, "REQUEST_METHOD");
    }
    private function basicAuthentication() : bool
    {
        $user = htmlspecialchars($_ENV['AUTH_USER'] ?? '');
        $pass = htmlspecialchars($_ENV['AUTH_PW'] ?? '');
        $authUser = htmlspecialchars($_SERVER['PHP_AUTH_USER'] ?? '');
        $authPass = htmlspecialchars($_SERVER['PHP_AUTH_PW'] ?? '');
        return ($user === $authUser && $pass === $authPass);
    }
    /**
     * vérifie l'authentification suivant la demande
     * possibilité d'ajouter des 'case' et de nouvelles fonctions 
     * si besoin d'un autre type d'authentification
     * @return bool
     */
    public function authentication() : bool
    {
        $authentication = htmlspecialchars($_ENV['AUTHENTICATION'] ?? '');
        switch($authentication)
        {
            case '': return true;
            case 'basic':
            {
                return $this->basicAuthentication();
            }
            default: return true;
        }
    }
}