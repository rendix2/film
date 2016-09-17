<html>
<head>
    <title>Template</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700&subset=latin-ext"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="template/main.css">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"
            integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
    <script src="template/main.js"></script>
</head>
<?php
    /**
     * Created by PhpStorm.
     * User: Tomáš
     * Date: 14. 9. 2016
     * Time: 10:48
     */

    error_reporting ( E_ALL );

    require_once ( './class/Starter.php' );

    $starter = Starter::start ();
    $db      = new Database( 'innodb.endora.cz', 'film', 'Siemens1', 'film' );
    $smarty  = new MySmarty();
    $user    = new User( $db, $smarty );
    $movie   = new Movie( $db, $smarty );

    $smarty->display ( 'pageHeader', [ ] );

    echo '<div class="padding">';
    switch ( $_GET[ 'akce' ] ) {
        case '':
        case 'searchMovie':
            $movie->searchMovie ();
            break;
        case 'login':
            $user->login ();
            break;
        case 'logout':
            $user->logout ();
            break;
        case 'register':
            $user->register ();
            break;
        case 'addMovie':
            $movie->add ();
            break;
        case 'search':
            $movie->realSearch ();
            break;
        default:
            echo 'Chyba switch';
    }

    echo '</div>';
    $smarty->display ( 'pageFooter', [ ] );
?>
