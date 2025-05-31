<?php
class Users {
    private $conn;
    private $table = 'users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create a new user
    public function create($name, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO {$this->table} (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    // Read all users or a single user by ID
    public function read($id = null) {
        if ($id) {
            $sql = "SELECT id, name, email, role, created_at FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } else {
            $sql = "SELECT id, name, email, role, created_at FROM {$this->table}";
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Update a user's information
    public function update($id, $name = null, $email = null, $password = null, $role = null) {
        $updates = [];
        $params = [];
        $types = '';

        if ($name) {
            $updates[] = "name = ?";
            $params[] = $name;
            $types .= 's';
        }

        if ($email) {
            $updates[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }

        if ($password) {
            $updates[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_BCRYPT);
            $types .= 's';
        }

        if ($role) {
            $updates[] = "role = ?";
            $params[] = $role;
            $types .= 's';
        }

        $params[] = $id;
        $types .= 'i';

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        return $stmt->execute();
    }

    // Delete a user by ID
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }
}
?>
