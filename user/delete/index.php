<?php
class Delete {
    public $db;
    private $id;
    private $queries = array(
        "DELETE FROM user_login WHERE id = ",
        "DELETE FROM user_preferences WHERE id = "
        //TO-DO: add SQL entries to this array as needed (IE: if I store additional data in a separate table)
        //...(this will be turned into one query once table structure is finalized)
    );
    public function __construct() {
        include '../../config/db.php';
        $this->db = new Data();
        if($this->db->verifyToken()) {
            echo json_encode(array(
                "result" => "false",
                "message" => "invalid user token"
            ));
        } else {
        $c = trim(file_get_contents('php://input'));
        $D = json_decode($c, true);
        $user = $this->db->sanitize($D['username']);
        $selSQL = "SELECT id FROM user_login WHERE username = '$user'";
        $selRes = $this->db->conn->query($selSQL);
        if($selRes->num_rows > 0) {
            $this->id = $selRes->fetch_row()[0];
            $allGood = true;
            foreach($this->queries as $query) {
                if(!$this->db->conn->query($query)) {
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
    }

}
if($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(array(
        "result" => false,
        "message" => "invalid request type"
    ));
} else {
    $d = new Delete();
}
?>