<?php
class RestaurantDatabase {
    private $host = "localhost";
    private $port = "3306";
    private $database = "restaurant_reservations";
    private $user = "root";
    private $password = "password";
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function getCustomerId($customerName, $contactInfo) {
        $stmt = $this->connection->prepare("SELECT customerId FROM Customers WHERE customerName = ? AND contactInfo = ?");
        $stmt->bind_param("ss", $customerName, $contactInfo);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['customerId'];
        }
        $stmt->close();
        return null;
    }

    public function addCustomer($customerName, $contactInfo) {
        $stmt = $this->connection->prepare("INSERT INTO Customers (customerName, contactInfo) VALUES (?, ?)");
        $stmt->bind_param("ss", $customerName, $contactInfo);
        $stmt->execute();
        $customerId = $stmt->insert_id;
        $stmt->close();
        return $customerId;
    }

    public function addReservation($customerId, $reservationTime, $numberOfGuests, $specialRequests) {
        $stmt = $this->connection->prepare("INSERT INTO Reservations (customerId, reservationTime, numberOfGuests, specialRequests) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $customerId, $reservationTime, $numberOfGuests, $specialRequests);
        $stmt->execute();
        $reservationId = $stmt->insert_id;
        $stmt->close();
        return $reservationId;
    }

    public function findReservations($customerId) {
        $stmt = $this->connection->prepare("SELECT * FROM Reservations WHERE customerId = ? ORDER BY reservationTime DESC");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reservations;
    }

    public function getAllReservationsWithCustomers() {
        $query = "SELECT r.reservationId, r.reservationTime, r.numberOfGuests, r.specialRequests, 
                         c.customerId, c.customerName, c.contactInfo 
                  FROM Reservations r 
                  JOIN Customers c ON r.customerId = c.customerId 
                  ORDER BY r.reservationTime DESC";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchReservations($searchTerm) {
        $searchTerm = "%$searchTerm%";
        $query = "SELECT r.reservationId, r.reservationTime, r.numberOfGuests, r.specialRequests, 
                         c.customerId, c.customerName, c.contactInfo 
                  FROM Reservations r 
                  JOIN Customers c ON r.customerId = c.customerId 
                  WHERE c.customerName LIKE ? OR c.customerId = ?
                  ORDER BY r.reservationTime DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reservations;
    }

    public function getAllCustomers() {
        $query = "SELECT * FROM Customers ORDER BY customerName";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteReservation($reservationId) {
        $stmt = $this->connection->prepare("DELETE FROM Reservations WHERE reservationId = ?");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows > 0;
    }

    public function addSpecialRequest($reservationId, $newRequest) {
        $stmt = $this->connection->prepare("SELECT specialRequests FROM Reservations WHERE reservationId = ?");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $currentRequests = $row['specialRequests'];
            $updatedRequests = $currentRequests ? $currentRequests . "\n" . $newRequest : $newRequest;

            $stmt = $this->connection->prepare("UPDATE Reservations SET specialRequests = ? WHERE reservationId = ?");
            $stmt->bind_param("si", $updatedRequests, $reservationId);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        return false;
    }

    

    public function getReservation($reservationId) {
        $stmt = $this->connection->prepare("SELECT * FROM Reservations WHERE reservationId = ?");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation = $result->fetch_assoc();
        $stmt->close();
        return $reservation;
    }

    public function updateSpecialRequest($reservationId, $specialRequests) {
        $stmt = $this->connection->prepare("UPDATE Reservations SET specialRequests = ? WHERE reservationId = ?");
        $stmt->bind_param("si", $specialRequests, $reservationId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getCustomerById($customerId) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE customerId = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getCustomerPreferences($customerId) {
        $stmt = $this->connection->prepare("SELECT favoriteTable, dietaryRestrictions FROM CustomerPreferences WHERE customerId = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $preferences = $result->fetch_assoc();
        $stmt->close();
        return $preferences;
    }

    
}
?>