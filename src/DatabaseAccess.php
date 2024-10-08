<?php

include_once "Connection.php";

/**
 * Description of DatabaseAccess
 *
 * @author Devblocks42 <devblocks42@keemail.me>
 */
abstract class DatabaseAccess 
{
    protected Connection $connection;
    
    protected function __construct()
    {
        try 
        {
            $login = htmlspecialchars($_ENV['BDD_LOGIN'] ?? '');
            $pwd = htmlspecialchars($_ENV['BDD_PWD'] ?? '');
            $db = htmlspecialchars($_ENV['BDD_BD'] ?? '');
            $server = htmlspecialchars($_ENV['BDD_SERVER'] ?? '');
            $port = htmlspecialchars($_ENV['BDD_PORT'] ?? '');    
            $this->connection = Connection::getInstance($login, $pwd, $db, $server, $port);
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
    /**
     * demande de traitement de la demande
     * @param string $methodeHTTP
     * @param string $table
     * @param string|null $id
     * @param array|null $fields
     * @return array|int|null
     */
    public function request(string $method, string $table, ?string $id, ?array $fields) : array|int|null 
    {
        if(is_null($this->connection))
        {
            return null;
        }
        switch ($method)
        {
            case 'GET' : 
                return $this->selectStatement($table, $fields);
            case 'POST' : 
                return $this->insertStatement($table, $fields);
            case 'PUT' : 
                return $this->updateStatemennt($table, $id, $fields);
            case 'DELETE' : 
                return $this->deleteStatement($table, $fields);
            default :
                return null;
        }       
    }
    abstract protected function selectStatement(string $table, ?array $fields) : ?array;
    abstract protected function insertStatement(string $table, ?array $fields) : ?int;
    abstract protected function updateStatement(string $table, ?string $id, ?array $fields) : ?int;
    abstract protected function deleteStatement(string $table, ?array $fields) : ?int;
}
