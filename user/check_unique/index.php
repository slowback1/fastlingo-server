<?php
    //This will return true if given email/username is UNIQUE (IE: it is NOT in the database), and false if it is not
    //it will accept GET or POST requests, with the same parameters, they will both return the same thing
    include '../../config/db.php';
    
    function cu() {
        $db = new Data();
        $i;
        $t;
         if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $c = trim(file_get_contents('php://input'));
            $d = json_decode($c, true);
            $i = $db->sanitize($d['input']);
            $t = $db->sanitize($d['type']);
        } else if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $i = $db->sanitize($_GET['input']);
            $t = $db->sanitize($_GET['type']);
        } else {
            return false;
        }
        return $db->checkUnique($i, $t);
    }
    echo json_encode(array(
        "isUnique" => cu()
    ));
?>