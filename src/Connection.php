<?php

/**
 * Description of Connection
 *
 * @author Devblocks42 <devblocks42@keemail.me>
 */
class Connection 
{
    private static $instance = null;
    private $connection;
    
    /**
     * constructeur privé : connexion à la BDD
     * @param string $login 
     * @param string $pwd
     * @param string $db
     * @param string $server
     * @param string $port
     */
    private function __construct(string $login, string $pwd, string $db, string $server, string $port)
    {
        try 
        {
            $this->connection = new \PDO("mysql:host=$server;dbname=$db;port=$port", $login, $pwd);
            $this->connection->query('SET CHARACTER SET utf8');
        } 
        catch (\Exception $e) 
        {
            throw $e;
        }
    }
    /**
     * méthode statique de création de l'instance unique
     * @param string $login
     * @param string $pwd
     * @param string $bd
     * @param string $server
     * @param string $port
     * @return Connexion instance unique de la classe
     */
    public static function getInstance(string $login, string $pwd, string $db, string $server, string $port)
    {
        if(self::$instance == null)
        {
            self::$instance = new Connection($login, $pwd, $db, $server, $port);
        }
        return self::$instance;
    }
    /**
     * prépare la requête
     * @param string $requete
     * @param array|null $param
     * @return \PDOStatement requête préparée
     */
    private function queryPrepare(string $query, ?array $params=null) : \PDOStatement
    {
        try 
        {
            $preparedStatement = $this->connection->prepare($query);
            if($params != null && is_array($params))
            {
                foreach($params as $key => &$value)
                {
                    $preparedStatement->bindParam(":$key", $value);
                }
            }
            return $preparedStatement;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
    /**
     * exécute une requête de mise à jour (insert, update, delete)
     * @param string $query
     * @param array|null $params
     * @return int|null nombre de lignes affectées ou null si erreur
     */
    public function updateDB(string $query, ?array $params=null) : ?int
    {
        try
        {
            $result = $this->queryPrepare($query, $params);
            $response = $result->execute();
            if($response === true)
            {
                return $result->rowCount();
            }
            else
            {
                return null;
            }
        }
        catch(Exception $e)
        {
            return null;
        }
    }
    /**
     * execute une requête select retournant 0 à plusieurs lignes
     * @param string $query
     * @param array|null $params
     * @return array|null lignes récupérées ou null si erreur
     */
    public function queryDB(string $query, ?array $params=null) : ?array
    {     
        try
        {
            $result = $this->queryPrepare($query, $params);
            $response = $result->execute();
            if($response === true)
            {
                return $result->fetchAll(PDO::FETCH_ASSOC);
            }
            else
            {
                return null;
            } 
        }
        catch(Exception $e)
        {
            return null;
        }
    }
}
