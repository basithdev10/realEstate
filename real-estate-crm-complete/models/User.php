<?php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Remove password from result
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get all sales employees
     */
    public function getSalesEmployees() {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE role = 'sales_employee' ORDER BY name");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all users
     */
    public function getAllUsers($limit = null, $offset = 0) {
        $query = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }
        
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create new user
     */
    public function createUser($name, $email, $password, $role = 'sales_employee') {
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $stmt->insert_id];
        }
        return ['success' => false, 'message' => 'Error creating user'];
    }

    /**
     * Update user
     */
    public function updateUser($id, $name, $email, $role) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
        return $stmt->execute();
    }

    /**
     * Change password
     */
    public function changePassword($id, $oldPassword, $newPassword) {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!password_verify($oldPassword, $result['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        return ['success' => false, 'message' => 'Error updating password'];
    }

    /**
     * Count total users
     */
    public function countUsers() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM users");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
