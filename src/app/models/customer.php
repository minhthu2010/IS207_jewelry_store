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

    // Kiểm tra email đã tồn tại chưa
    public function emailExists() {
        $query = "SELECT cus_id, fullname, password 
                  FROM " . $this->table_name . " 
                  WHERE email = ? 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
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
                SET fullname=:fullname, email=:email, password=:password";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $this->fullname = htmlspecialchars(strip_tags($this->fullname));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":fullname", $this->fullname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Tạo giỏ hàng mới cho customer
    public function createCart($customer_id) {
        $query = "INSERT INTO cart (customer_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $customer_id);
        return $stmt->execute();
    }

    // Lấy customer_id bằng email
    public function getCustomerIdByEmail($email) {
        $query = "SELECT cus_id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['cus_id'];
        }
        return false;
    }

        // Đăng nhập tài khoản khách hàng
    public function login($email, $password) {
        // Câu truy vấn kiểm tra email
        $query = "SELECT cus_id, fullname, email, password 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        // Nếu tìm thấy email
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Kiểm tra mật khẩu
            if (password_verify($password, $row['password'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['customer'] = [
                    'cus_id' => $row['cus_id'],
                    'fullname' => $row['fullname'],
                    'email' => $row['email']
                ];
                return true;
            } else {
                return false; 
            }
        } else {
            return false; 
        }
    }

    
public function updatePassword($email, $new_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $query = "UPDATE " . $this->table_name . " 
              SET password = :password, updated_at = NOW() 
              WHERE email = :email";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":email", $email);
    
    return $stmt->execute();
}


    
}
?>
