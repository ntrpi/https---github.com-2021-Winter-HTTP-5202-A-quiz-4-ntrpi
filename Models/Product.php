<?php
// File created by Sandra Kupfer 2021/03.

namespace quiz_4_ntrpi\Models
{
    use quiz_4_ntrpi\Models\{Model};
    require_once "Model.php";

    class Product extends Model
    {
        // There should be one item for every column in the database.
        public static array $columnNames = array( "id", "product" );

        public function __construct()
        {
            parent::__construct( "insproducts", "id", self::$columnNames );
        }

        // Syntactic sugar.
        public function getNumProducts()
        {
            return parent::getNumRows();
        }

        // Syntactic sugar.
        public function getProducts()
        {
            return parent::getRowObjects();
        }

        // Syntactic sugar.
        public function getProductsWhere( $columnName, $value )
        {
            return parent::getRowObjectsWithValue( $columnName, $value );
        }

        // TODO: refactor with RH actions.
        // Syntactic sugar.
        public function getProduct( $id )
        {
            return parent::getRowObject( $id );
        }

        // Syntactic sugar.
        public function deleteProduct( $id )
        {
            return parent::deleteRow( $id );
        }

        // Syntactic sugar. Note that if the properties of the $params object do not correspond to 
        // database columns, the update will fail.
        // $params: An object with properties that are key/value pairs corresponding to columns in the database.
        public function updateProduct( $params ) 
        {
            return parent::updateRow( $params );
        }

        // Create a new Product using the provided key/value pairs. Modify the $params object so that 
        // the object properties correspond to the column names in the database.
        // Note that if the properties of the $params object do not correspond to 
        // database columns, the Product will not be added.
        public function createProduct( $params )
        {
            // This params object will likely be from the form processor, so make sure you add in values for the columns
            // that don't have input fields and unset value that don't correspond to a column, 
            // or call fixParams( $params, RH::$actionCreate ) first.

            return parent::addRow( $params );
        }

        // Validate the value for the given column.
        public function isValid( $columnName, $value )
        {
            if( $columnName == "product" ) {
                return FormProcessor::isValidName( $value );
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

        public function getRadioButton( $productId, $productName, $isSelected = false)
        {
            $checked = $isSelected ? "checked=\"checked\"" : "";
            echo "<div class=\"radioDiv\">";
            echo "   <input type=\"radio\" id=\"product{$productId}\" name=\"insurance\" value=\"{$productName}\" {$checked} />";
            echo "    <label for=\"product{$productId}\">{$productName}</label>";
            echo "</div>";
        }

        public function getRadioButtons( $selected = 0 )
        {
            $products = $this->getProducts();
            foreach( $products as $product ) {
                $isSelected = $selected == $product->product;
                $this->getRadioButton( $product->id, $product->product, $isSelected );
            }
        }
    }
}
?>