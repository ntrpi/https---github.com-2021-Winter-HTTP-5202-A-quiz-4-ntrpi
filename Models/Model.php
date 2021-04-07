<?php
// File created by Sandra Kupfer 2021/03.

namespace Codesses\php\Models
{
    use Codesses\php\Models\{Database};
    require_once "Database.php";
    require_once "utl.php";

    // This class standardizes how Models interact with the database. 
    class Model 
    {
        // The name of the database table.
        protected $tableName;

        // The name of the primary key.
        protected $idName;

        // An array of all the column names, including the primary key.
        protected array $columns;

        // The sql to get the number of rows in the table.
        protected $countSql;

        // The sql to get everything from the table.
        protected $listSql;

        // The sql to get a row from the table with the given primary key.
        protected $findSql;

        // The sql to delete a row from the table with the given primary key.
        protected $deleteSql;

        // The sql to update a row from the table with the given primary key.
        protected $updateSql1;
        protected $updateSql2;

        // This will be the name if the submit input for any form for this table.
        // Use getSubmitAdd() to get this name.
        public $submitName;

        // This is protected because this class can't be instantiated, only extended.
        // $tableName: The name of the database table.
        // $idName: The name of the primary key.
        // $columns: An array of all the column names, including the primary key.
        protected function __construct( $tableName, $idName, array $columns )
        {
            $this->tableName = $tableName;
            $this->idName = $idName;
            $this->columns = $columns;

            $this->countSql = "SELECT COUNT(*) FROM " . $tableName;
            $this->listSql = "SELECT * FROM " . $tableName;
            $this->findSql = "SELECT * FROM " . $tableName . " WHERE " . $idName . " = :" . $idName;
            $this->deleteSql = "DELETE FROM " . $tableName . " WHERE " . $idName . " = :" . $idName;
            $this->updateSql1 = "UPDATE " . $tableName . " SET ";
            $this->updateSql2 = " WHERE " . $idName . " = :" . $idName;

            $this->submitName = "submit" . $tableName;
        }

        // Given an array of key names to use as keys and an associated array to use as values,
        // return an object with the key/value pairs as properties.
        public static function getParams( array $keyNames, array $values = null )
        {
            $params = new \stdClass();
            $numItems = sizeof( $keyNames );
            for( $i = 0; $i < $numItems; $i++ ) {
                $columnName = $keyNames[$i];
                $params->$columnName = $values != null ? $values[$i] : "";
            }
            return $params;
        }

        public static function getParam( $key, $value )
        {
            $param = new \stdClass();
            $param->$key = $value;
            return $param;
        }

        // Return the array of column names for this table.
        public function getColumns()
        {
            return $this->columns;
        }

        // Return the name of the submit input field.
        public function getSubmitName()
        {
            return $this->submitName;
        }

        // Return the number of rows for this table in the database.
        protected function getNumRows()
        {
            return Database::getDbResult( $this->countSql );
        }

        // Return an array of objects, where each object is a row in the database,
        // and the columns and values are its properties.
        protected function getRowObjects()
        {
            return Database::getDbResult( $this->listSql );
        }

        // Return an object with the primary key and passed in $id are the only property.
        protected function getIdParamsObject( $id )
        {
            return self::getParam( $this->idName, $id );
        }

        // Return an object that has the columns and values of the row in the database with the given primary key
        // as its properties.
        protected function getRowObject( $id )
        {
            $results = Database::getDbResultWithParams( $this->findSql, $this->getIdParamsObject( $id ) );
            return $results[0];
        }

        // Return the objects for the rows that have the value for the given column.
        // Basically SELECT * with a single WHERE clause.
        protected function getRowObjectsWithValue( $columnName, $value )
        {
            $sql = $this->listSql . " WHERE " . $columnName . " = :" . $columnName;
            return Database::getDbResultWithParams( $sql, self::getParam( $columnName, $value ) );
        }

        // Delete the row in the database with the given primary key.
        protected function deleteRow( $id )
        {
            return Database::getDbResultWithParams( $this->deleteSql, $this->getIdParamsObject( $id ) );
        }

        // Update the row in the database with the passed in parameters. One of the
        // properties of $params should be the primary key and value.
        // $params: An object with key == column and value == value pairs.
        protected function updateRow( $params )
        {
            // Set up the sql.
            $sql = $this->updateSql1;
            foreach( $params as $key=>$value ) {
                if( $key == $this->idName ) {
                    continue;
                }
                $sql .= "{$key} = :{$key}, ";
            }
            
            // Strip trailing comma.
            $sql = substr( $sql, 0, strlen( $sql ) - 2 );

            // Finish sql.
            $sql .= $this->updateSql2;

            wl( $sql );
            Database::prettyPrintObj( $params );

            return Database::getDbResultWithParams( $sql, $params );
        }

        // Add a new row in the database with the passed in parameters.
        // $params: An object with key == column and value == value pairs.
        protected function addRow( $params )
        {
            $idName = $this->idName;
            unset( $params->$idName );

            // Set up the sql.
            $sql = "INSERT INTO " . $this->tableName . " (";
            $values = " ) VALUES ( ";
            foreach( $params as $key=>$value ) {
                if( $key == $this->idName ) {
                    continue;
                }
                $sql .= "{$key}, ";
                $values .= ":{$key}, ";
            }

            // Strip trailing comma.
            $sql = substr( $sql, 0, strlen( $sql ) - 2 );
            $values = substr( $values, 0, strlen( $values ) - 2 );

            // Finish sql.
            $sql .= $values . " );";

            return Database::getDbResultWithParams( $sql, $params );
        }
    }
}

?>