<?php
// File created by Sandra Kupfer 2021/03.

namespace Codesses\php\Models
{
    class FormProcessor 
    {

        // Static class.
        private function __construct()
        {        
        }

        // Function sanitizeInput adapted from 
        // https://www.w3schools.com/php/php_form_validation.asp 2021/02/08.
        public static function sanitizeInput( $input ) 
        { 
            return ( htmlspecialchars( stripslashes( trim( $input ) ) ) );
        }
        
        // Confirm that a form was submitted with POST and that the submit input had the given name.
        public static function isPost( $submitName )
        {
            return $_SERVER["REQUEST_METHOD"] == "POST" && isset( $_POST[ $submitName ] );
        }

        // Return an object with the input names as keys and the input field values as values.
        public static function getValuesObject( $inputNames ) // Expecting an array of input element names.
        {
            $values = new \stdClass();
            foreach( $inputNames as $name ) {   
                $inputValue = isset( $_POST[ $name ] ) ? $_POST[ $name ] : "";
                $inputValue = self::sanitizeInput( $inputValue );
                $values->$name = $inputValue;
            }
            return $values;
        }

        // Syntactic sugar.
        public static function getPostValue( $value )
        {
            return $_POST[ $value ];
        }

        // Names can only have letters, apostrophes, and hyphens.
        public static function isValidName( $name, $length = 2 )
        {
            $nameRegex = "/^[a-zA-Z-' ]*$/";
            return is_string( $name ) && preg_match( $nameRegex, $name ) && strlen( $name ) >= $length;
        }

        // Validating format only.
        public static function isValidEmail( $email )
        {
            return is_string( $email ) && filter_var( $email, FILTER_VALIDATE_EMAIL );
        }

        // Validating against 10 digits only.
        public static function isValidPhone( $phone )
        {
            return preg_match( "/\d{10}/", $phone );
        }

        // Use the paramaters to make the conditions more strict.
        public static function isValidPassword( $password, $length = 8, $mustContainUpper = false, $mustContainNumber = false, $mustContainSpecial = false )
        {
            return strlen( $password ) >= $length
            && ( $mustContainUpper && preg_match( "/.*[A-Z].*/", $password ) )
            && ( $mustContainNumber && preg_match( "/.*[0-9].*/", $password ) )
            && ( $mustContainSpecial && preg_match( "/.*[^a-zA-Z0-9].*/", $password ) );
        }
    }
}
?>