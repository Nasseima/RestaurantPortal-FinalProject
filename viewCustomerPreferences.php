private function customerPreferences() {
    $customerId = $_GET['customer_id'] ?? null;
    if ($customerId) {
        $customer = $this->db->getCustomerById($customerId);
        $preferences = $this->db->getCustomerPreferences($customerId);
        
        echo "<h2>Customer Preferences for " . htmlspecialchars($customer['customerName']) . "</h2>";
        include 'viewCustomerPreferences.php';
    } else {
        echo "<p>Error: Customer ID not provided.</p>";
    }
}