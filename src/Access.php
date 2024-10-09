<?php

include_once("DatabaseAccess.php");

/**
 * Description of Access
 *
 * @author Devblocks42 <devblocks42@keemail.me>
 */
class Access extends DatabaseAccess
{
    public function __construct()
    {
        try 
        {
            parent::__construct();
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
    /**
     * demande de suppression (delete)
     * @param string $table
     * @param array|null fields nom et valeur de chaque champ
     * @return int|null nombre de tuples supprimés ou null si erreur
     * @override
     */	
    protected function deleteStatement(string $table, ?array $fields) : ?int
    {
        switch($table)
        {
            case "" :
                // return $this->uneFonction(parametres);
            default:                    
                // cas général
                return $this->deleteTuplesOneTable($table, $fields);	
        }
    }
    /**
     * demande d'ajout (insert)
     * @param string $table
     * @param array|null $fields nom et valeur de chaque champ
     * @return int|null nombre de tuples ajoutés ou null si erreur
     * @override
     */	
    protected function insertStatement(string $table, ?array $fields): ?int 
    {
        switch($table)
        {
            case "":
                return [];
            default:
                return $this->insertOneTupleOneTable($table, $fields);
        }
    }
    /**
     * demande de recherche
     * @param string $table
     * @param array|null fields nom et valeur de chaque champ
     * @return array|null tuples du résultat de la requête ou null si erreur
     * @override
     */	
    protected function selectStatement(string $table, ?array $fields): ?array 
    {
        switch($table)
        {
            case "produit_specifique":
                return $this->selectLike($fields);
            default:
                return $this->selectTuplesOneTable($table, $fields);
        }
    }
    /**
     * demande de modification (update)
     * @param string $table
     * @param string|null $id
     * @param array|null $fields nom et valeur de chaque champ
     * @return int|null nombre de tuples modifiés ou null si erreur
     * @override
     */	
    protected function updateStatement(string $table, ?string $id, ?array $fields): ?int 
    {
        switch($table)
        {
            case "":
                return [];
            default:
                return $this->updateOneTupleOneTable($table, $id, $fields);
        }
    }
    /**
     * récupère les tuples d'une seule table
     * @param string $table
     * @param array|null $fields
     * @return array|null 
     */
    private function selectTuplesOneTable(string $table, ?array $fields) : ?array
    {
        if(empty($fields))
        {
            // tous les tuples d'une table
            $query = "select * from $table;";
            return $this->connection->queryDB($query);  
        }
        else
        {
            // tuples spécifiques d'une table
            $query = "select * from $table where ";
            foreach ($fields as $key => $value)
            {
                $query .= "$key=:$key and ";
            }
            $query = substr($query, 0, strlen($query)-5);	 
            return $this->connection->queryDB($query, $fields);
        }
    }
    /**
     * demande d'ajout (insert) d'un tuple dans une table
     * @param string $table
     * @param array|null $fields
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */	
    private function insertOneTupleOneTable(string $table, ?array $fields) : ?int
    {
        if(empty($fields))
        {
            return null;
        }
        // construction de la requête
        $query = "insert into $table (";
        foreach ($fields as $key => $value)
        {
            $query .= "$key,";
        }
        // (enlève la dernière virgule)
        $query = substr($query, 0, strlen($query)-1);
        $query .= ") values (";
        foreach ($fields as $key => $value)
        {
            $query .= ":$key,";
        }
        // (enlève la dernière virgule)
        $query = substr($query, 0, strlen($query)-1);
        $query .= ");";
        return $this->connection->updateDB($query, $fields);
    }	
    /**
     * demande de modification (update) d'un tuple dans une table
     * @param string $table
     * @param string\null $id
     * @param array|null $champs 
     * @return int|null nombre de tuples modifiés (0 ou 1) ou null si erreur
     */	
    private function updateOneTupleOneTable(string $table, ?string $id, ?array $fields) : ?int 
    {
        if(empty($fields))
        {
            return null;
        }
        if(is_null($id))
        {
            return null;
        }
        // construction de la requête
        $query = "update $table set ";
        foreach ($fields as $key => $value)
        {
            $query .= "$key=:$key,";
        }
        // (enlève la dernière virgule)
        $query = substr($query, 0, strlen($query)-1);				
        $fields["id"] = $id;
        $query .= " where id=:id;";		
        return $this->connection->dbUpdate($query, $champs);	        
    }
    /**
     * demande de suppression (delete) d'un ou plusieurs tuples dans une table
     * @param string $table
     * @param array|null $fields
     * @return int|null nombre de tuples supprimés ou null si erreur
     */
    private function deleteTuplesOneTable(string $table, ?array $fields) : ?int
    {
        if(empty($fields))
        {
            return null;
        }
        // construction de la requête
        $query = "delete from $table where ";
        foreach ($fields as $key => $value)
        {
            $query .= "$key=:$key and ";
        }
        // (enlève le dernier and)
        $query = substr($query, 0, strlen($query)-5);   
        return $this->connection->updateDB($query, $fields);	        
    }
    /**
     * récupère le nom, la description et les détails des produits
     * dont 'description' ou 'détails' contient le mot clé présent dans $champs
     * @param array|null $fields contient juste 'clef' avec une valeur de clef
     * @return ?array
     */
    private function selectLike(?array $fields) : ?array
    {
        if(empty($fields) || !array_key_exists('clef', $fields))
        {
            return null;
        }
        $query = "SELECT p.nom, p.description, dp.details FROM produit p LEFT JOIN details_produits dp ON (p.id = dp.idproduit) ";
        $query .= "WHERE p.description LIKE :clef OR dp.details LIKE :clef ORDER BY p.nom;";
        $fields['clef'] = '%' . $fields['clef'] . '%'; 
        return $this->connection->queryDB($query, $fields);
    }
}
