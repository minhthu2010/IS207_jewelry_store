<?php
class Customer {
    private $conn;
    private $table_name = "customer";

    public $cus_id;
    public $fullname;
    public $phone;
    public $email;
    public $address;
    public $password;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kiểm tra email
    public function emailExists() {
        $query = "SELECT cus_id, fullname, password 
                  FROM " . $this->table_name . " 
                  WHERE email = ? 
                  LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->cus_id = $row['cus_id'];
            $this->fullname = $row['fullname'];
            $this->password = $row['password'];
            return true;
        }
        return false;
    }

    // Tạo tài khoản mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (fullname, email, password) VALUES (:fullname, :email, :password)";
        $stmt = $this->conn->prepare($query);

        $this->fullname = htmlspecialchars(strip_tags($this->fullname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":fullname", $this->fullname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        return $stmt->execute();
    }

    // Đăng nhập
    public function login($email, $password) {
        $query = "SELECT cus_id, fullname, email, password 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['customer'] = [
                    'cus_id' => $row['cus_id'],
                    'fullname' => $row['fullname'],
                    'email' => $row['email']
                ];
                return true;
            }
        }
        return false;
    }

    // Lưu token vào DB
    public function saveToken($cus_id, $token) {
        $query = "UPDATE " . $this->table_name . " 
                  SET token = :token 
                  WHERE cus_id = :cus_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":cus_id", $cus_id);
        return $stmt->execute();
    }

    // Lấy user bằng token
    public function getCustomerByToken($token) {
        $query = "SELECT cus_id, fullname, email 
                  FROM " . $this->table_name . " 
                  WHERE token = :token 
                  LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function updateInfo() {
    $query = "UPDATE " . $this->table_name . "
              SET fullname = :fullname,
                  phone = :phone,
                  email = :email,
                  address = :address,
                  updated_at = NOW()
              WHERE cus_id = :cus_id";
    
    $stmt = $this->conn->prepare($query);

    // Lọc dữ liệu
    $this->fullname = htmlspecialchars(strip_tags($this->fullname));
    $this->phone = htmlspecialchars(strip_tags($this->phone));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->address = htmlspecialchars(strip_tags($this->address));

    // Gán giá trị
    $stmt->bindParam(':fullname', $this->fullname);
    $stmt->bindParam(':phone', $this->phone);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':address', $this->address);
    $stmt->bindParam(':cus_id', $this->cus_id);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

}

// Cập nhật thông tin khách hàng

?>
