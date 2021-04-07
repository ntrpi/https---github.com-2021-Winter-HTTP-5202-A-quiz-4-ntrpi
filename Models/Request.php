<?php
// File created by Sandra Kupfer 2021/03.

namespace quiz_4_ntrpi\Models
{
    use quiz_4_ntrpi\Models\{Model};
    require_once "Model.php";

    class Request extends Model
    {
        // There should be one item for every column in the database.
        public static array $columnNames = array( "id", "firstname", "lastname", "postalcode", "phone", "email", "insurance" );

        // These correspond to the names of the input fields of your form.
        // They may or may not be the same as the associated columns in the database, but if they are not,
        // you will need to deal with that manually. See createRequest( $params ) below.
        public static array $inputNames = array( "firstname", "lastname", "postalcode", "phone", "email", "insurance" );

        // Error messages that correspond 1-to-1 with the input fields.
        public static array $errorMessages = array(
            "firstname" => "Please enter a name that is a least two characters long and has only letters, hyphens, and apostrophes.",
            "lastname" => "Please enter a name that is a least two characters long and has only letters, hyphens, and apostrophes.",
            "postalcode" => "Please enter a postal code in this format: A1A 1A1",
            "phone" => "Please enter a valid phone number, which contains 10 digits.",
            "email" => "Please enter a valid email address.",
            "insurance" => "Please select a product."
        );

        public function __construct()
        {
            parent::__construct( "insrequests", "id", self::$columnNames );
        }

        // Syntactic sugar.
        public function getNumRequests()
        {
            return parent::getNumRows();
        }

        // Syntactic sugar.
        public function getRequests()
        {
            return parent::getRowObjects();
        }

        // Syntactic sugar.
        public function getRequestsWhere( $columnName, $value )
        {
            return parent::getRowObjectsWithValue( $columnName, $value );
        }

        // TODO: refactor with RH actions.
        // Syntactic sugar.
        public function getRequest( $id )
        {
            return parent::getRowObject( $id );
        }

        // Syntactic sugar.
        public function deleteRequest( $id )
        {
            return parent::deleteRow( $id );
        }

        // Syntactic sugar. Note that if the properties of the $params object do not correspond to 
        // database columns, the update will fail.
        // $params: An object with properties that are key/value pairs corresponding to columns in the database.
        public function updateRequest( $params ) 
        {
            return parent::updateRow( $params );
        }

        // Create a new Request using the provided key/value pairs. Modify the $params object so that 
        // the object properties correspond to the column names in the database.
        // Note that if the properties of the $params object do not correspond to 
        // database columns, the Request will not be added.
        public function createRequest( $params )
        {
            // This params object will likely be from the form processor, so make sure you add in values for the columns
            // that don't have input fields and unset value that don't correspond to a column, 
            // or call fixParams( $params, RH::$actionCreate ) first.

            return parent::addRow( $params );
        }

        // Validate the value for the given column.
        public function isValid( $columnName, $value )
        {
            if( $columnName == "firstname" || $columnName == "lastname" ) {
                return FormProcessor::isValidName( $value );
            }
            if( $columnName == "phone" ) {
                return FormProcessor::isValidPhone( $value );
            }
            if( $columnName == "email" ) {
                return FormProcessor::isValidEmail( $value );
            }
            if( $columnName == "postalcode" ) {
                // Check that the Request name is unique.
                return strlen( $value ) > 0 && sizeof( $this->getRequestsWhere( $columnName, $value ) ) == 0;
            }
            return true;
        }

        // Validate all of the key/value pairs in the $params object.
        // If everything is valid, return null. Otherwise, return the name of the 
        // invalid input.
        public function validateInput( $params )
        {
            foreach( $params as $key=>$value ) {
                if( !$this->isValid( $key, $params->$key ) ) {
                    return $key;
                }
            }
            return null;
        }
    }
}
?>