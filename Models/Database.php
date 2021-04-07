<?php
// File created by Sandra Kupfer 2021/03.

namespace quiz_4_ntrpi\Models
{

use PDOException;

    class Database {

        // These variables must be set to the specific database connection information.
        private static $dbName = "5202quiz4";
        private static $host = "localhost";
        private static $userName = "root";
        private static $password = "";

        // Private variables to interact with the database.
        private static $dataSourceName;
        private static $dataPdo;
    
        // Static class.
        private function __construct()
        {        
        }
        
        // Construct the PDO if required, then return PDO.
        private static function getPdo()
        {
            if( self::$dataPdo == null ) {
                self::$dataSourceName  = "mysql:host=" .self::$host . ";dbname=" . self::$dbName;
    
                try {
                // Establish the connection.
                self::$dataPdo = new \PDO( self::$dataSourceName, self::$userName, self::$password );
        
                } catch( PDOException $e ) {
                    echo $e->getMessage()();
                    exit();
                }

                // Set some connection attributes.
                self::$dataPdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            }
            return self::$dataPdo;
        }

        // Use the PDO to run the provided SQL string. Return the PDO statement that was run.
        public static function runSql( $sql )
        {
            // echo $sql;
            $dataPdo = self::getPdo();
            $pdoStatement = $dataPdo->prepare( $sql );
            $pdoStatement->execute();
            // $pdoStatement->debugDumpParams();
    
            return $pdoStatement;
        }
    
        // Prepare the PDO statement by binding the provided key/value pairs. 
        // Use the PDO to run the SQL, then return the PDO statement.
        public static function runSqlWithParams( $sql, $params )
        {
            // echo $sql;
            // var_dump( $params );

            $dataPdo = self::getPdo();
            $pdoStatement = $dataPdo->prepare( $sql );
    
            foreach( $params as $key=>$value ) {
                $pdoStatement->bindParam( ':' . $key, $params->$key );
            }
    
            $pdoStatement->execute();
            // $pdoStatement->debugDumpParams();
    
            return $pdoStatement;
        }

        // $pdoResult is what is returned by the call to PDO execute(). 
        // Get an array of all the rows retrieved from the database, close the cursor, then return the array.
        public static function getRows( $pdoResult )
        {
            $rows = $pdoResult->fetchAll( \PDO::FETCH_OBJ );
            $pdoResult->closeCursor();
            return $rows;
        }

        // Syntactic sugar to execute some SQL and return the array of rows retrieved from the database.
        public static function getDbResult( $sql )
        {
            $pdoResult = self::runSql( $sql );
            return self::getRows( $pdoResult );
        }

        // Syntactic sugar to bind the key/value pairs in $params, 
        // execute some SQL, and return the array of rows retrieved from the database.
        public static function getDbResultWithParams( $sql, $params )
        {
            $pdoResult = self::runSqlWithParams( $sql, $params );
            return self::getRows( $pdoResult );
        }

        // Close the database connection on destruction.
        public function __destruct() {
            $this->datapdo->close();
        }
        
        
    }
}


?>