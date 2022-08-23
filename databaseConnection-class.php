<?php 

class DatabaseConnection {
    private $host ="localhost";
    private $user = "root";
    private $password = "";
    private $database = "sql_uzduotis";

    protected $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
            // echo "Prisijungta prie duomenu bazes sekmingai";
        } catch(PDOException $e) {
            // echo "Prisijungti nepavyko: ".$e->getMessage();
        }
    }

    public function selectWithJoin($join, $cols, $where) {
        $cols = implode(",", $cols);
        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT DISTINCT $cols FROM users
            $join users_roles_permissions
            ON users.id = users_roles_permissions.user_id
            $join roles
            ON roles.id = users_roles_permissions.role_id
            $join permissions
            ON permissions.id = users_roles_permissions.permission_id
            $where";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            var_dump($result);
            return $result;

        } catch(PDOException $e) {
            echo "Nepavyko vykdyti uzklausos: ".$e->getMessage();
        }
    }

    public function merge3($table1, $table2, $table3) {
        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT * FROM $table1, $table2, $table3";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            var_dump($result);
            return $result;

        } catch(PDOException $e) {
            echo "Nepavyko vykdyti uzklausos: ".$e->getMessage();
        }
    }

    public function selfJoin() {
        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT a.name AS _1lygis, b.name AS _2lygis, c.name AS _3lygis
            FROM `categories` a
            LEFT JOIN `categories` b ON b.parent_id = a.id
            LEFT JOIN `categories` c ON c.parent_id = b.id
            WHERE a.parent_id = 0";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            var_dump($result);
            return $result;

        } catch(PDOException $e) {
            echo "Nepavyko vykdyti uzklausos: ".$e->getMessage();
        }
    }

    public function __destruct() {
        $this->conn=null;
        // echo "Atsijungta";
    }

}

$table = new DatabaseConnection;

$table->selectWithJoin( "INNER JOIN", ["users.name, users.surname, roles.name AS role_name"], "");
$table->selectWithJoin( "INNER JOIN", ["permissions.name AS permission_name , roles.name AS role_name"], "");
$table->selectWithJoin( "INNER JOIN", ["users.name, users.surname, permissions.name AS permission_name"], "");
$table->selectWithJoin( "INNER JOIN", ["users.name, users.surname, permissions.name AS permission_name , roles.name AS role_name"], "");
$table->merge3("users", "roles","permissions");
$table->selectWithJoin( "INNER JOIN", ["users.name, users.surname, permissions.name AS permission_name, roles.name AS role_name"], "WHERE roles.id=1");
$table->selectWithJoin( "LEFT JOIN", ["users.name, users.surname, permissions.name AS permission_name"], "");
$table->selfJoin();



?>