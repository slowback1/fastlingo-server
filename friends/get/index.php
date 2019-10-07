<?php
    class GetFriends {
        public $db;
        private $id;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
            if($this->db->verifyToken()) {
                $this->id = $this->db->getUserId();
            } else {
                echo json_encode(array(
                    "result" => false,
                    "message" => "no user token found"
                ));
                return;
            }
            $fListStr;
            $sql = "SELECT friends_list FROM user_friends WHERE id = $this->id";
            $req = $this->db->conn->query($sql);
            if($req->num_rows > 0) {
                $fListStr = $req->fetch_row()[0];
            } else {
                echo json_encode(array(
                    "result" => false,
                    "message" => "no user found"
                ));
                return;
            }
            $fListArr = explode(",", $fListStr);
            echo json_encode(array(
                "result" => true,
                "friends_list" => $fListArr
            ));
        }
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(array(
            "result" => false,
            "message" => "invalid request method"
        ));
    } else {
        $f = new GetFriends();
    }
?>