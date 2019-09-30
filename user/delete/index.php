<?php
    class Delete {
        public $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
        }
        
        public function validate($token) {
            //to-do, check that user is deleting their own account, and not deleting someone else's.  
        }
        public function logout() {
            //to-do.  remove any tokens user has after deletion successful
        }
    }
    if($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        echo json_encode(array(
            "result" => false,
            "message" => "invalid request type"
        ));
    } else {
        $del = new Delete();
        $c = trim(file_get_contents('php://input'));
        $D = json_decode($c, true);
        $id;
        $user = $del->db->sanitize($D['username']);
        echo $user;
        $selSQL = "SELECT id FROM user_login WHERE username = '$user'";
        $selRes = $del->db->conn->query($selSQL);
        if($selRes->num_rows > 0) {
            $id = $selRes->fetch_row()[0];
            $queries = array(
                "DELETE FROM user_login WHERE id = '$id'"
                //TO-DO: add SQL entries to this array as needed (IE: if I store additional data in a separate table)
            );
            $allGood = true;
            foreach($queries as $query) {
                if(!$del->db->conn->query($query)) {
                    $allGood = false;   
                }
            }
            echo json_encode(array(
                "result" => $allGood
            ));
        } else {
            echo json_encode(array(
                "result" => false,
                "message" => "nouser"       
            ));
        }
    }
?>