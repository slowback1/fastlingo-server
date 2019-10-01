<?php
    class PrefGet {
        private $db;


        public function __construct() {
            include '../../../config/db.php';
            $this->db = new Data();
            if($this->db->verifyToken()) {
                $id = $this->db->getUserId();
                if(!$id) {
                    echo json_encode(array(
                        "result" => false,
                        "message" => "no user found"
                    ));
                } else {
                    echo $this->getPreferences($id);
                }
            }
        }


        private function getPreferences($id) {
            $sql = "SELECT color_mode, goal_duration, goal_type FROM user_preferences WHERE id = '$id'";
            $res = $this->db->conn->query($sql);
            if($res->num_rows > 0) {
                $row = $res->fetch_array();
                return json_encode(array(
                    "result" => true,
                    "color_mode" => $row['color_mode'],
                    "goal_duration" => $row['goal_duration'],
                    "goal_type" => $row['goal_type']
                ));
            } else {
                $isql = "INSERT INTO user_preferences (id) VALUES ($id)";
                $this->db->conn->query($isql);
                return json_encode(array(
                    "result" => true,
                    "color_mode" => "light",
                    "goal_duration" => 16,
                    "goal_type" => "hour"
                ));
            }
        }
    }
    $a = new PrefGet();
?>
