<?php

use quiz_4_ntrpi\Models\{FormProcessor, Request, Product};

require_once "./Models/Product.php";
require_once "./Models/Request.php";
require_once "./Models/FormProcessor.php";

// Create a helper object.
$requestDbHelper = new Request;
$productDbHelper = new Product;

// See if this is a GET or POST request.
$isPost = FormProcessor::isPost( $requestDbHelper->getSubmitName() );

// Set up the error messages array for use later in the html.
$errorMessages = array();
foreach( Request::$inputNames as $input ) {
  $errorMessages[$input] = "";
}

// $params are used in the html below.
$params = $requestDbHelper->getParams( Request::$inputNames );

$selected = "";

if( $isPost ) {

  // Use the FormProcessor to retrieve the values from the form.
  $params = FormProcessor::getValuesObject( Request::$inputNames );

  // Validate the input. This will reflect what the js validate does,
  // but we can do a bit more because we have access to the database.
  $result = $requestDbHelper->validateInput( $params );
  if( $result != null ) {

    // Setting the error message here will cause it to show up in the html.
    // See the divs with class="errorDiv" below.
    $errorMessages[$result] = Request::$errorMessages[$result];

  } else {

    $requestDbHelper->createRequest( $params );

    // Check that the values in the database match what we updated.
    $request = $requestDbHelper->getRequestsWhere( "email", $params->email )[0];
    $isSuccess = true;
    foreach( $params as $key=>$value ) {
        if( $params->$key != $request->$key ) {
        $isSuccess = false;
        wl( $params->$key ."   ". $request->$key );
        break;
        }
    }

    if( $isSuccess ) {
      header( "Location: success.php?id=" . $request->id );
    } else {
        // Failed.
        // TODO: go to error message.
        echo "Unable to create request.";
        exit();
    }

  }
}

?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Quiz 4</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
  </head>
  <body>
    <h1>Request for Information Form</h1>  
    <p>To receive information on our products and services by email, please complete the form below.</p>
    <div>
      <form name="accountForm" action="" method="POST">
        <div id="firstnameError" class="errorDiv" style="color:red"><?= $errorMessages["firstname"]; ?></div>
        <div class="inputDiv">
          <label for="firstname">First name</label>
          <input type="text" name="firstname" id="firstname" value="<?= $params->firstname; ?>" />
        </div>
        <div id="lastnameError" class="errorDiv" style="color:red"><?= $errorMessages["lastname"]; ?></div>
        <div class="inputDiv">
          <label for="lastname">Last name</label>
          <input type="text" name="lastname" id="lastname" value="<?= $params->lastname; ?>" />
        </div>
        <div id="postalcodeError" class="errorDiv" style="color:red"><?= $errorMessages["postalcode"]; ?></div>
        <div class="inputDiv">
          <label for="postalcode">Postal Code</label>
          <input type="text" name="postalcode" id="postalcode" value="<?= $params->postalcode; ?>"/>
        </div>
        <div id="phoneError" class="errorDiv" style="color:red"><?= $errorMessages["phone"]; ?></div>
        <div class="inputDiv">
          <label for="phone">Phone number</label>
          <input type="tel" name="phone" id="phone" value="<?= $params->phone; ?>" />
        </div>
        <div id="emailError" class="errorDiv" style="color:red"><?= $errorMessages["email"]; ?></div>
        <div class="inputDiv">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="<?= $params->email; ?>" />
        </div>
        <div id="insuranceError" class="errorDiv" style="color:red"><?= $errorMessages["insurance"]; ?></div>
        <div class="inputDiv">
          <span>Please send me information on the following product:</span>
          <?php $productDbHelper->getRadioButtons( $selected ) ?>
        </div>
        <!-- Note that I am setting the name of the submit input to be the same as what the
            FormProcessor is checking for. -->
          <input type="submit"  name="<?= $requestDbHelper->getSubmitName(); ?>" value="Sign Up">
        </div>  
      </form>
    </div>
  </body>
</html>