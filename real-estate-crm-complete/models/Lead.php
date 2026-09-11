<?php
class Lead {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all leads with filtering and pagination
     */
    public function getLeads($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT l.*, u.name as assigned_to_name FROM leads l 
                  LEFT JOIN users u ON l.assigned_to = u.id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        // Apply filters
        if (!empty($filters['stage'])) {
            $query .= " AND l.stage = ?";
            $params[] = $filters['stage'];
            $types .= "s";
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND l.assigned_to = ?";
            $params[] = $filters['assigned_to'];
            $types .= "i";
        }

        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $query .= " AND (l.first_name LIKE ? OR l.last_name LIKE ? OR l.email LIKE ? OR l.phone LIKE ?)";
            $params = array_merge($params, [$search, $search, $search, $search]);
            $types .= "ssss";
        }

        if (!empty($filters['status'])) {
            $query .= " AND l.stage = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        $query .= " ORDER BY l.updated_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->db->prepare($query);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count leads with filters
     */
    public function countLeads($filters = []) {
        $query = "SELECT COUNT(*) as total FROM leads WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['stage'])) {
            $query .= " AND stage = ?";
            $params[] = $filters['stage'];
            $types .= "s";
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND assigned_to = ?";
            $params[] = $filters['assigned_to'];
            $types .= "i";
        }

        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $params = array_merge($params, [$search, $search, $search, $search]);
            $types .= "ssss";
        }

        $stmt = $this->db->prepare($query);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    /**
     * Get lead by ID with notes
     */
    public function getLeadById($id) {
        $stmt = $this->db->prepare("SELECT l.*, u.name as assigned_to_name FROM leads l 
                                   LEFT JOIN users u ON l.assigned_to = u.id 
                                   WHERE l.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $lead = $stmt->get_result()->fetch_assoc();

        if ($lead) {
            // Get notes
            $stmt = $this->db->prepare("SELECT ln.*, u.name as created_by_name FROM lead_notes ln 
                                       LEFT JOIN users u ON ln.created_by = u.id 
                                       WHERE ln.lead_id = ? 
                                       ORDER BY ln.created_at DESC");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $lead['notes'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $lead;
    }

    /**
     * Create new lead
     */
    public function createLead($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO leads (first_name, last_name, email, phone, stage, assigned_to, source, budget, preferred_property_type) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $assigned_to = $data['assigned_to'] ?? null;
        $assigned_to = $assigned_to ? intval($assigned_to) : null;

        $stmt->bind_param(
            "sssssisds",
            $data['first_name'],
            $data['last_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['stage'] ?? 'New',
            $assigned_to,
            $data['source'] ?? null,
            $data['budget'] ?? null,
            $data['preferred_property_type'] ?? null
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $stmt->insert_id];
        }
        return ['success' => false, 'message' => 'Error creating lead'];
    }

    /**
     * Update lead
     */
    public function updateLead($id, $data) {
        $assigned_to = $data['assigned_to'] ?? null;
        $assigned_to = $assigned_to ? intval($assigned_to) : null;

        $stmt = $this->db->prepare(
            "UPDATE leads 
             SET first_name = ?, last_name = ?, email = ?, phone = ?, stage = ?, assigned_to = ?, source = ?, budget = ?, preferred_property_type = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssssisddsi",
            $data['first_name'],
            $data['last_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['stage'] ?? 'New',
            $assigned_to,
            $data['source'] ?? null,
            $data['budget'] ?? null,
            $data['preferred_property_type'] ?? null,
            $id
        );

        return $stmt->execute();
    }

    /**
     * Add note to lead
     */
    public function addNote($lead_id, $note, $follow_up_date = null, $created_by) {
        $stmt = $this->db->prepare(
            "INSERT INTO lead_notes (lead_id, note, follow_up_date, created_by) 
             VALUES (?, ?, ?, ?)"
        );

        $follow_up_date = $follow_up_date ?: null;
        $stmt->bind_param("issi", $lead_id, $note, $follow_up_date, $created_by);

        return $stmt->execute();
    }

    /**
     * Update lead stage
     */
    public function updateStage($id, $stage) {
        $stmt = $this->db->prepare("UPDATE leads SET stage = ? WHERE id = ?");
        $stmt->bind_param("si", $stage, $id);
        return $stmt->execute();
    }

    /**
     * Get leads by stage
     */
    public function getLeadsByStage($stage) {
        $stmt = $this->db->prepare("SELECT * FROM leads WHERE stage = ? ORDER BY updated_at DESC");
        $stmt->bind_param("s", $stage);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get leads count by stage
     */
    public function getLeadsCountByStage() {
        $result = $this->db->query("SELECT stage, COUNT(*) as count FROM leads GROUP BY stage");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get pending follow-ups
     */
    public function getPendingFollowUps($days = 7) {
        $query = "SELECT ln.*, l.first_name, l.last_name, u.name as created_by_name 
                  FROM lead_notes ln 
                  JOIN leads l ON ln.lead_id = l.id 
                  LEFT JOIN users u ON ln.created_by = u.id 
                  WHERE ln.follow_up_date IS NOT NULL 
                  AND ln.follow_up_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                  AND ln.follow_up_date >= CURDATE()
                  ORDER BY ln.follow_up_date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Delete lead
     */
    public function deleteLead($id) {
        $stmt = $this->db->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
