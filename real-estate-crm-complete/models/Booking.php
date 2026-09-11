<?php
class Booking {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all bookings with filters and pagination
     */
    public function getBookings($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT b.*, l.first_name, l.last_name, l.email, u.unit_number, p.name as project_name, 
                         u2.name as created_by_name 
                  FROM bookings b 
                  JOIN leads l ON b.lead_id = l.id 
                  JOIN units u ON b.unit_id = u.id 
                  JOIN projects p ON u.project_id = p.id 
                  LEFT JOIN users u2 ON b.created_by = u2.id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['lead_id'])) {
            $query .= " AND b.lead_id = ?";
            $params[] = $filters['lead_id'];
            $types .= "i";
        }

        if (!empty($filters['unit_id'])) {
            $query .= " AND b.unit_id = ?";
            $params[] = $filters['unit_id'];
            $types .= "i";
        }

        if (!empty($filters['booking_status'])) {
            $query .= " AND b.booking_status = ?";
            $params[] = $filters['booking_status'];
            $types .= "s";
        }

        if (!empty($filters['project_id'])) {
            $query .= " AND u.project_id = ?";
            $params[] = $filters['project_id'];
            $types .= "i";
        }

        $query .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
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
     * Get booking by ID
     */
    public function getBookingById($id) {
        $stmt = $this->db->prepare("SELECT b.*, l.first_name, l.last_name, l.email, u.unit_number, p.name as project_name
                                   FROM bookings b 
                                   JOIN leads l ON b.lead_id = l.id 
                                   JOIN units u ON b.unit_id = u.id 
                                   JOIN projects p ON u.project_id = p.id 
                                   WHERE b.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Check if unit is already booked (confirmed booking)
     * CRITICAL: Prevent duplicate bookings
     */
    public function isUnitBooked($unit_id) {
        $stmt = $this->db->prepare(
            "SELECT id FROM bookings WHERE unit_id = ? AND booking_status = 'Confirmed' LIMIT 1"
        );
        $stmt->bind_param("i", $unit_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Check if lead already has a confirmed booking
     */
    public function getLeadConfirmedBooking($lead_id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM bookings WHERE lead_id = ? AND booking_status = 'Confirmed'"
        );
        $stmt->bind_param("i", $lead_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create booking with duplicate prevention
     * CRITICAL BUSINESS LOGIC
     */
    public function createBooking($lead_id, $unit_id, $payment_amount, $created_by) {
        // Start transaction
        $this->db->begin_transaction();

        try {
            // Check if unit is already booked
            if ($this->isUnitBooked($unit_id)) {
                throw new Exception("This unit is already booked");
            }

            // Check lead doesn't have another confirmed booking
            $existingBooking = $this->getLeadConfirmedBooking($lead_id);
            if ($existingBooking) {
                throw new Exception("Lead already has a confirmed booking for another unit");
            }

            // Create the booking
            $bookingStatus = 'Pending';
            $stmt = $this->db->prepare(
                "INSERT INTO bookings (lead_id, unit_id, booking_status, payment_amount, created_by) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iisdi", $lead_id, $unit_id, $bookingStatus, $payment_amount, $created_by);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating booking");
            }

            $bookingId = $stmt->insert_id;

            // Commit transaction
            $this->db->commit();

            return ['success' => true, 'id' => $bookingId, 'message' => 'Booking created successfully'];

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Confirm booking - Mark unit as booked
     * CRITICAL: Update unit status when booking is confirmed
     */
    public function confirmBooking($id, $payment_date = null) {
        $this->db->begin_transaction();

        try {
            // Get booking
            $booking = $this->getBookingById($id);
            if (!$booking) {
                throw new Exception("Booking not found");
            }

            // Double-check unit is not already booked by someone else
            if ($this->isUnitBooked($booking['unit_id'])) {
                throw new Exception("Unit was just booked by another customer");
            }

            // Update booking status
            $status = 'Confirmed';
            $stmt = $this->db->prepare("UPDATE bookings SET booking_status = ?, payment_date = ? WHERE id = ?");
            $stmt->bind_param("ssi", $status, $payment_date, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating booking");
            }

            // Update unit status to Booked
            $unitStatus = 'Booked';
            $stmt = $this->db->prepare("UPDATE units SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $unitStatus, $booking['unit_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating unit status");
            }

            // Update lead stage to Booked
            $leadStage = 'Booked';
            $stmt = $this->db->prepare("UPDATE leads SET stage = ? WHERE id = ?");
            $stmt->bind_param("si", $leadStage, $booking['lead_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating lead stage");
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Booking confirmed successfully'];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cancel booking - Release unit
     */
    public function cancelBooking($id, $reason = null) {
        $this->db->begin_transaction();

        try {
            $booking = $this->getBookingById($id);
            if (!$booking) {
                throw new Exception("Booking not found");
            }

            // Update booking status
            $status = 'Cancelled';
            $stmt = $this->db->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error cancelling booking");
            }

            // If was confirmed, release the unit
            if ($booking['booking_status'] === 'Confirmed') {
                $unitStatus = 'Available';
                $stmt = $this->db->prepare("UPDATE units SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $unitStatus, $booking['unit_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error updating unit status");
                }
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Booking cancelled successfully'];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get bookings by lead
     */
    public function getLeadBookings($lead_id) {
        $stmt = $this->db->prepare(
            "SELECT b.*, u.unit_number, p.name as project_name FROM bookings b 
             JOIN units u ON b.unit_id = u.id 
             JOIN projects p ON u.project_id = p.id 
             WHERE b.lead_id = ? 
             ORDER BY b.created_at DESC"
        );
        $stmt->bind_param("i", $lead_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get bookings by unit
     */
    public function getUnitBookings($unit_id) {
        $stmt = $this->db->prepare(
            "SELECT b.*, l.first_name, l.last_name FROM bookings b 
             JOIN leads l ON b.lead_id = l.id 
             WHERE b.unit_id = ? 
             ORDER BY b.created_at DESC"
        );
        $stmt->bind_param("i", $unit_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total bookings count
     */
    public function countBookings($filters = []) {
        $query = "SELECT COUNT(*) as total FROM bookings WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['booking_status'])) {
            $query .= " AND booking_status = ?";
            $params[] = $filters['booking_status'];
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
     * Get revenue (confirmed bookings with payment)
     */
    public function getTotalRevenue() {
        $result = $this->db->query(
            "SELECT SUM(payment_amount) as total FROM bookings WHERE booking_status = 'Confirmed' AND payment_date IS NOT NULL"
        );
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Get recent bookings
     */
    public function getRecentBookings($limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT b.*, l.first_name, l.last_name, u.unit_number, p.name as project_name 
             FROM bookings b 
             JOIN leads l ON b.lead_id = l.id 
             JOIN units u ON b.unit_id = u.id 
             JOIN projects p ON u.project_id = p.id 
             ORDER BY b.created_at DESC 
             LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
