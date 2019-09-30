<?php
    class Register {
        public $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
        }
             
        public function checkPasswords($password1, $password2) {
            if($password1 === $password2) {
                return true;
            } else {
                return false;
            }
        }
        public function insertIntoDB($username, $password, $email) {
            $sql = "INSERT INTO user_login (username, password, email) VALUES ('$username', '$password', '$email')";
            if($this->db->conn->query($sql)) {
                return true;
            } else {
                return false;
            }
        }
        
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(array(
            "result"  => false,
            "message" => "invalid request type"
        ));
    } else {
        $reg = new Register();
        $c = trim(file_get_contents('php://input'));
        $P = json_decode($c, true);
        $u = $reg->db->sanitize($P['username']);
        $p = $reg->db->sanitize($P['password']);
        $p2 = $reg->db->sanitize($P['password2']);
        $e = $reg->db->sanitize($P['email']);
        if($reg->db->checkUnique($u, 'username') && $reg->db->checkUnique($e, 'email')) {
            if($reg->checkPasswords($p, $p2)) {
                $sp = $reg->db->hashPW($p);
                $reg->insertIntoDB($u, $sp, $e);
                //TO-DO: send login token along with success response
                echo json_encode(array(
                    "result"  => true,
                    "message" => "success"
                ));
            } else {
                echo json_encode(array(
                    "result"  => false,
                    "message" => "passwords do not match"
                ));
            }
        } else {
            echo json_encode(array(
                "result"  => false,
                "message" => "username or email is not unique"
            ));
        }
    }
?>