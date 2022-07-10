<?php
class Database
{
    protected $pdo = null;

    public function __construct()
    {
        $dsn = "mysql:host=localhost";
        $options = [
        PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];

        try 
        {
            $this->pdo = new PDO($dsn, "root", "", $options);
            $dbname = "anwbapi";
            $this->pdo->query("CREATE DATABASE IF NOT EXISTS $dbname");
            $this->pdo->query("use $dbname");
        } 
        catch (Exception $e) 
        {
            error_log($e->getMessage());
            throw new Exception($e->getMessage());
        }
        
        $this->pdo->query("CREATE TABLE IF NOT EXISTS `Traffic` (
            `ID` INT AUTO_INCREMENT NOT NULL,
            `Road` varchar(50) NOT NULL,
            `From_Location` varchar(100) NOT NULL,
            `To_Location` varchar(100) NOT NULL,
            `From_Loc_Lat` FLOAT,
            `From_Loc_Lng` FLOAT,
            `To_Loc_Lat` FLOAT,
            `To_Loc_Lng` FLOAT,
            `Distance` INT, # Distance in m
            `Delay` INT, # Delay in seconds
            `Reason` varchar(500),
            `Valid_From` datetime,
            `Valid_To` datetime,
            `TrafficType` ENUM('Jam', 'Roadwork'),
            `Polyline` varchar(5000),
            PRIMARY KEY (`ID`))
            CHARACTER SET utf8 COLLATE utf8_general_ci
        ");
    }

    public function executeQuery($query = "", $params = [])
    {
        try
        {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    } 
}


