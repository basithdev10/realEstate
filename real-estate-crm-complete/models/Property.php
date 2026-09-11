<?php
class Property {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    // ============ PROJECTS ============

    /**
     * Get all projects
     */
    public function getProjects($limit = null, $offset = 0) {
        $query = "SELECT * FROM projects ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }
        
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get project by ID with buildings and units count
     */
    public function getProjectById($id) {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $project = $stmt->get_result()->fetch_assoc();

        if ($project) {
            // Get buildings count
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM buildings WHERE project_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $project['buildings_count'] = $stmt->get_result()->fetch_assoc()['total'];

            // Get units count
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM units WHERE project_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $project['units_count'] = $stmt->get_result()->fetch_assoc()['total'];

            // Get available units count
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM units WHERE project_id = ? AND status = 'Available'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $project['available_units'] = $stmt->get_result()->fetch_assoc()['total'];
        }

        return $project;
    }

    /**
     * Create project
     */
    public function createProject($name, $location, $description) {
        $stmt = $this->db->prepare("INSERT INTO projects (name, location, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $location, $description);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $stmt->insert_id];
        }
        return ['success' => false, 'message' => 'Error creating project'];
    }

    /**
     * Update project
     */
    public function updateProject($id, $name, $location, $description, $status) {
        $stmt = $this->db->prepare("UPDATE projects SET name = ?, location = ?, description = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $location, $description, $status, $id);
        return $stmt->execute();
    }

    // ============ BUILDINGS ============

    /**
     * Get buildings by project
     */
    public function getProjectBuildings($project_id) {
        $stmt = $this->db->prepare("SELECT * FROM buildings WHERE project_id = ? ORDER BY name");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get building by ID
     */
    public function getBuildingById($id) {
        $stmt = $this->db->prepare("SELECT * FROM buildings WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create building
     */
    public function createBuilding($project_id, $name, $floor_count) {
        $stmt = $this->db->prepare("INSERT INTO buildings (project_id, name, floor_count) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $project_id, $name, $floor_count);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $stmt->insert_id];
        }
        return ['success' => false, 'message' => 'Error creating building'];
    }

    // ============ UNITS ============

    /**
     * Get all units with filtering
     */
    public function getUnits($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT u.*, p.name as project_name, b.name as building_name 
                  FROM units u 
                  JOIN projects p ON u.project_id = p.id 
                  JOIN buildings b ON u.building_id = b.id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['project_id'])) {
            $query .= " AND u.project_id = ?";
            $params[] = $filters['project_id'];
            $types .= "i";
        }

        if (!empty($filters['building_id'])) {
            $query .= " AND u.building_id = ?";
            $params[] = $filters['building_id'];
            $types .= "i";
        }

        if (!empty($filters['type'])) {
            $query .= " AND u.type = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }

        if (!empty($filters['status'])) {
            $query .= " AND u.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $query .= " AND (u.unit_number LIKE ? OR p.name LIKE ?)";
            $params = array_merge($params, [$search, $search]);
            $types .= "ss";
        }

        $query .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
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
     * Get unit by ID
     */
    public function getUnitById($id) {
        $stmt = $this->db->prepare("SELECT u.*, p.name as project_name, b.name as building_name 
                                   FROM units u 
                                   JOIN projects p ON u.project_id = p.id 
                                   JOIN buildings b ON u.building_id = b.id 
                                   WHERE u.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create unit
     */
    public function createUnit($building_id, $project_id, $unit_number, $type, $price, $built_area) {
        $stmt = $this->db->prepare(
            "INSERT INTO units (building_id, project_id, unit_number, type, price, built_area) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iisdd", $building_id, $project_id, $unit_number, $type, $price, $built_area);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $stmt->insert_id];
        }
        return ['success' => false, 'message' => 'Error creating unit'];
    }

    /**
     * Update unit
     */
    public function updateUnit($id, $unit_number, $type, $price, $built_area, $status) {
        $stmt = $this->db->prepare(
            "UPDATE units SET unit_number = ?, type = ?, price = ?, built_area = ?, status = ? WHERE id = ?"
        );
        $stmt->bind_param("sdddsi", $unit_number, $price, $built_area, $type, $status, $id);
        return $stmt->execute();
    }

    /**
     * Update unit status
     */
    public function updateUnitStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE units SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /**
     * Get available units by project
     */
    public function getAvailableUnits($project_id = null) {
        $query = "SELECT u.*, p.name as project_name, b.name as building_name 
                  FROM units u 
                  JOIN projects p ON u.project_id = p.id 
                  JOIN buildings b ON u.building_id = b.id 
                  WHERE u.status = 'Available'";
        
        if ($project_id) {
            $query .= " AND u.project_id = ?";
            $stmt = $this->db->prepare($query . " ORDER BY u.unit_number");
            $stmt->bind_param("i", $project_id);
        } else {
            $stmt = $this->db->prepare($query . " ORDER BY u.created_at DESC");
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get units by building
     */
    public function getBuildingUnits($building_id) {
        $stmt = $this->db->prepare("SELECT * FROM units WHERE building_id = ? ORDER BY unit_number");
        $stmt->bind_param("i", $building_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count units with filters
     */
    public function countUnits($filters = []) {
        $query = "SELECT COUNT(*) as total FROM units WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['project_id'])) {
            $query .= " AND project_id = ?";
            $params[] = $filters['project_id'];
            $types .= "i";
        }

        if (!empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
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
     * Get units count by type
     */
    public function getUnitsCountByType($project_id = null) {
        $query = "SELECT type, COUNT(*) as count FROM units";
        
        if ($project_id) {
            $query .= " WHERE project_id = ?";
            $stmt = $this->db->prepare($query . " GROUP BY type");
            $stmt->bind_param("i", $project_id);
        } else {
            $stmt = $this->db->prepare($query . " GROUP BY type");
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get units count by status
     */
    public function getUnitsCountByStatus($project_id = null) {
        $query = "SELECT status, COUNT(*) as count FROM units";
        
        if ($project_id) {
            $query .= " WHERE project_id = ?";
            $stmt = $this->db->prepare($query . " GROUP BY status");
            $stmt->bind_param("i", $project_id);
        } else {
            $stmt = $this->db->prepare($query . " GROUP BY status");
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
