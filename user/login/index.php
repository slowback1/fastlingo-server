<?php
    class Login {
        public $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
        }
        public function validate($username, $password) {
            $sql = "SELECT password FROM user_login WHERE username = '$username'";
            $r = $this->db->conn->query($sql);
            if($r->num_rows > 0) {
                $spw = $r->fetch_row()[0];
                if(password_verify($password, $spw)) {
                    return array(true, $spw);
                } else {
                    return array(false, "badpassword");
                }
            } else {
                return array(false, "baduser");
            }
        }
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(array(
            "result" => false,
            "message" => "invalid request type"
        ));
    } else {
        $log = new Login();
        $c = trim(file_get_contents('php://input'));
        $P = json_decode($c, true);
        $user = $log->db->sanitize($P['username']);
        $pass = $log->db->sanitize($P['password']);
        $res = $log->validate($user, $pass);
        if($res[0]) {
            $token = $log->db->setToken($user, $res[1]);
            echo json_encode(array(
                "result" => true,
                "jwt" => $token
            ));
        } else {
            echo json_encode(array(
                "result" => false,
                "message" => $res[1]
            ));
        }
    }
?>