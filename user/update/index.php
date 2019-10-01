<?php
    class Update {
        private $db;
        private $id;

        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
            if($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                echo json_encode(array(
                    "result" => false,
                    "message" => "invalid request type"
                ));
                return;
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
                    $data = array();
                    foreach($postdata as $key => $value) {
                        if($key === 'password') {
                            $postdata[$key] = $this->db->hashPW($value);
                        } else if($key === 'username' || $key === 'email') {
                            if(!$this->db->checkUnique($value, $key)) {
                                unset($postdata[$key]);
                            }
                        }
                    }

                    $sql = $this->db->getUpdateQuery('user_login', $postdata, $this->id);
                    if($this->db->conn->query($sql)) {
                        $usql = "SELECT username, password FROM user_login WHERE id = $this->id LIMIT 1";
                        $r = $this->db->conn->query($usql);
                        $pass = "";
                        $user = "";
                        if($r->num_rows > 0) {
                            while($row = $r->fetch_assoc()) {
                                $pass = $row['password'];
                                $user = $row['username'];
                            }
                        }
                        $token = $this->db->setToken($user, $pass);
                        echo json_encode(array(
                            "result" => true,
                            "message" => "success",
                            "jwt" => $token
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
    $a = new Update();
?>