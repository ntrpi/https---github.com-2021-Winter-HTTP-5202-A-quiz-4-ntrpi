<?php
// File created by Sandra Kupfer 2021/03.

function wl( $someString )
{
    echo $someString . "</br>";
}

// Code copied from https://www.php.net/manual/en/function.session-status.php
// 2021/03/29
function isSessionStarted()
{
    if( php_sapi_name() !== 'cli' ) {
        if( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

function startSession( 
    int $lifetime_or_options = 1800, // Default is 30 minutes. 
    string|null $path = null , 
    string|null $domain = null , 
    bool|null $secure = null , 
    bool|null $httponly = null,
    string|null $samesite = null )
{
    $cookieParams = array(
        'lifetime' => $lifetime_or_options
    );

    if( $path != null && $path != "" ) {
        $cookieParams[ 'path' ] = $path;
    }
    
    if( $domain != null && $domain != "" ) {
        $cookieParams[ 'domain' ] = $domain;
    }
    
    if( $secure != null && $secure != "" ) {
        $cookieParams[ 'secure' ] = $secure;
    }
    
    if( $httponly != null && $httponly != "" ) {
        $cookieParams[ 'httponly' ] = $httponly;
    }
    
    if( $samesite != null && $samesite != "" ) {
        $cookieParams[ 'samesite' ] = $samesite;
    }

    session_set_cookie_params( $cookieParams );

    if( !isSessionStarted() ) {
        session_start();
    }
}

function isSessionSet( $name )
{
    return isset( $_SESSION[ $name ] );
}

function getSessionVar( $name ) 
{
    if( !isSessionSet( $name ) ) {
        return null;
    }
    return $_SESSION[ $name ];
}

$loggedInVar = "is_user_logged_in";
function setUserLoggedIn()
{
    global $loggedInVar;
    startSession( 1800 );
    $_SESSION[ $loggedInVar ] = true;
}

function isUserLoggedIn()
{
    global $loggedInVar;
    return isSessionSet( $loggedInVar ) && $_SESSION[ $loggedInVar ] == true;
}

function setUserLoggedOut()
{
    $_SESSION = array();
    session_destroy()();
}

function checkSecure()
{
    // make sure the page uses a secure connection
    $https = filter_input(INPUT_SERVER, 'HTTPS');
    if (!$https) {
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $url = 'https://' . $host . $uri;
        header("Location: " . $url);
        exit();
    }
}

?>