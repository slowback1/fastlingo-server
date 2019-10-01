<?php
    class Set {
        private $db;
        private $id;

        public function __construct() {
            include '../../../config/db.php';
            $this->db = new Data();
            if($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                echo json_encode(array(
                    "result" => false,
                    "message" => "invalid request type"
                ));
                return false;
            }
            if($this->db->verifyToken()) {
                $this->id = $this->db->getUserId();
                if(!$this->id) {
                    echo json_encode(array(
                        "result" => false,
                        "message" => "no user found"
                    ));
                } else {
                    $body = trim(file_get_contents('php://input'));
                    $postdata = json_decode($body, true);
                    $sql = $this->db->getUpdateQuery('user_preferences', $postdata, $this->id);
                    if($this->db->conn->query($sql)) {
                        echo json_encode(array(
                            "result" => true,
                            "message" => "success"
                        ));
                    } else {
                        echo json_encode(array(
                            "result" => false,
                            "message" => "something went wrong"
                        ));
                    }
                }
            } else {
                echo json_encode(array(
                    "result" => false,
                    "message" => "access denied"
                ));
            }
        }
        
    }
    $a = new Set();
?>