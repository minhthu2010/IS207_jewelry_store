<?php
class Admin {
    private $conn;
    private $table_name = "admin";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔑 Đăng nhập bằng email hoặc username
    public function login($emailOrUsername, $password) {
        $query = "SELECT admin_id, fullname, email, username, password 
                  FROM {$this->table_name}
                  WHERE email = :login OR username = :login
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $emailOrUsername);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin'] = [
                    'admin_id' => $row['admin_id'],
                    'fullname' => $row['fullname'],
                    'email'    => $row['email'],
                    'username' => $row['username']
                ];
                return true;
            }
        }
        return false;
    }

    // 💾 Lưu token Remember Me
    public function saveToken($admin_id, $token) {
        $query = "UPDATE {$this->table_name} 
                  SET token = :token 
                  WHERE admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":admin_id", $admin_id);
        return $stmt->execute();
    }

    // 🔍 Lấy admin bằng token (tự động đăng nhập)
    public function getAdminByToken($token) {
        $query = "SELECT admin_id, fullname, email, username 
                  FROM {$this->table_name}
                  WHERE token = :token
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
?>
