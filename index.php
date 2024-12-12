<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'RestaurantDatabase.php';

class RestaurantPortal {
    private $db;

    public function __construct() {
        $this->db = new RestaurantDatabase();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'home';

        $this->outputHeader();

        switch ($action) {
            case 'reservations':
                $this->reservationsPage();
                break;
            case 'addReservation':
                $this->addReservationPage();
                break;
            case 'customers':
                if (isset($_GET['delete'])) {
                    $this->deleteCustomer($_GET['delete']);
                }
                $this->customersPage();
                break;
            case 'addCustomer':
                $this->addCustomerPage();
                break;
            case 'addSpecialRequest':
                $this->addSpecialRequestPage();
                break;
            case 'customerPreferences':
                $this->customerPreferences();
                break;
            case 'viewCustomerReservations':
                $this->viewCustomerReservations();
                break;
            default:
                $this->home();
        }

        $this->outputFooter();
    }

    private function outputHeader() {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Restaurant Portal</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                h1, h2, h3 {
                    color: #2c3e50;
                }
                nav {
                    margin-bottom: 20px;
                    border-bottom: 2px solid #3498db;
                    padding-bottom: 10px;
                }
                nav a {
                    color: #3498db;
                    text-decoration: none;
                    margin-right: 15px;
                }
                nav a:hover {
                    text-decoration: underline;
                }
                form {
                    margin-bottom: 20px;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 5px;
                }
                label {
                    display: block;
                    margin-bottom: 5px;
                    color: #2c3e50;
                }
                input[type="text"], input[type="number"], input[type="datetime-local"], textarea {
                    width: 100%;
                    padding: 8px;
                    margin-bottom: 10px;
                    border: 1px solid #bdc3c7;
                    border-radius: 3px;
                    box-sizing: border-box;
                }
                input[type="submit"], button {
                    background-color: #3498db;
                    color: white;
                    padding: 10px 15px;
                    border: none;
                    border-radius: 3px;
                    cursor: pointer;
                }
                input[type="submit"]:hover, button:hover {
                    background-color: #2980b9;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    background-color: #fff;
                    border-radius: 5px;
                    overflow: hidden;
                }
                th, td {
                    padding: 12px;
                    border: 1px solid #ecf0f1;
                    text-align: left;
                }
                th {
                    background-color: #3498db;
                    color: white;
                }
                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <h1>Restaurant Portal</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="index.php?action=reservations">Reservations</a>
                <a href="index.php?action=customers">Customers</a>
            </nav>
        HTML;
    }

    private function outputFooter() {
        echo <<<HTML
        </body>
        </html>
        HTML;
    }

    private function home() {
        echo <<<HTML
        <h2>Welcome to the Restaurant Portal</h2>
        <p>Please use the navigation menu to manage reservations and customer information.</p>
        HTML;
    }

    private function reservationsPage() {
        echo "<h2>Reservations</h2>";
        echo "<a href='index.php?action=addReservation'>Add New Reservation</a>";
        echo "<h3>Reservation List</h3>";
        $this->viewReservations();
    }

    private function addReservationPage() {
        echo "<h2>Add New Reservation</h2>";
        echo <<<HTML
        <form method="POST" action="index.php?action=addReservation">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="contact_info">Contact Info:</label>
            <input type="text" id="contact_info" name="contact_info" required>

            <label for="reservation_time">Reservation Time:</label>
            <input type="datetime-local" id="reservation_time" name="reservation_time" required>

            <label for="number_of_guests">Number of Guests:</label>
            <input type="number" id="number_of_guests" name="number_of_guests" required>

            <label for="special_requests">Special Requests:</label>
            <textarea id="special_requests" name="special_requests"></textarea>

            <input type="submit" value="Add Reservation">
        </form>
        <a href="index.php?action=reservations">Back to Reservations</a>
        HTML;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addReservation();
        }
    }

    private function addReservation() {
        $customerName = $_POST['customer_name'];
        $contactInfo = $_POST['contact_info'];
        $reservationTime = $_POST['reservation_time'];
        $numberOfGuests = $_POST['number_of_guests'];
        $specialRequests = $_POST['special_requests'];

        $reservationId = $this->db->addReservation($customerName, $contactInfo, $reservationTime, $numberOfGuests, $specialRequests);
        if ($reservationId) {
            echo "<p>Reservation Added Successfully (ID: {$reservationId})</p>";
        } else {
            echo "<p>Error: Failed to add reservation.</p>";
        }
    }

    private function viewReservations() {
        $reservations = $this->db->getAllReservationsWithCustomers();
        if (empty($reservations)) {
            echo "<p>No reservations found.</p>";
        } else {
            echo "<table>";
            echo "<tr><th>Reservation ID</th><th>Customer Name</th><th>Contact Info</th><th>Reservation Time</th><th>Number of Guests</th><th>Special Requests</th><th>Actions</th></tr>";
            foreach ($reservations as $reservation) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($reservation['reservationId']) . "</td>";
                echo "<td>" . htmlspecialchars($reservation['customerName']) . "</td>";
                echo "<td>" . htmlspecialchars($reservation['contactInfo']) . "</td>";
                echo "<td>" . htmlspecialchars($reservation['reservationTime']) . "</td>";
                echo "<td>" . htmlspecialchars($reservation['numberOfGuests']) . "</td>";
                echo "<td>" . htmlspecialchars($reservation['specialRequests']) . "</td>";
                echo "<td>
                    <a href='index.php?action=reservations&delete=" . $reservation['reservationId'] . "' onclick='return confirm(\"Are you sure you want to delete this reservation?\");'>Delete</a>
                </td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    private function customersPage() {
        echo "<h2>Customers</h2>";
        echo "<a href='index.php?action=addCustomer'>Add New Customer</a>";
        echo "<h3>Customer List</h3>";
        $this->viewCustomers();
    }

    private function addCustomerPage() {
        echo "<h2>Add New Customer</h2>";
        echo <<<HTML
        <form method="POST" action="index.php?action=addCustomer">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="contact_info">Contact Info:</label>
            <input type="text" id="contact_info" name="contact_info" required>

            <input type="submit" value="Add Customer">
        </form>
        <a href="index.php?action=customers">Back to Customers</a>
        HTML;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addCustomer();
        }
    }

    private function addCustomer() {
        $customerName = $_POST['customer_name'];
        $contactInfo = $_POST['contact_info'];

        $customerId = $this->db->addCustomer($customerName, $contactInfo);
        if ($customerId) {
            echo "<p>Customer Added Successfully (ID: {$customerId})</p>";
        } else {
            echo "<p>Error: Failed to add customer.</p>";
        }
    }

    private function viewCustomers() {
        $customers = $this->db->getAllCustomers();
        if (empty($customers)) {
            echo "<p>No customers found.</p>";
        } else {
            echo "<table>";
            echo "<tr><th>Customer ID</th><th>Customer Name</th><th>Contact Info</th><th>Actions</th></tr>";
            foreach ($customers as $customer) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($customer['customerId']) . "</td>";
                echo "<td>" . htmlspecialchars($customer['customerName']) . "</td>";
                echo "<td>" . htmlspecialchars($customer['contactInfo']) . "</td>";
                echo "<td>
                    <a href='index.php?action=viewCustomerReservations&customer_id=" . $customer['customerId'] . "'>View Reservations</a> |
                    <a href='index.php?action=customers&delete=" . $customer['customerId'] . "' onclick='return confirm(\"Are you sure you want to delete this customer? This will also delete all their reservations.\");'>Delete</a>
                </td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    private function addSpecialRequestPage() {
        echo "<h2>Add Special Request</h2>";
        echo <<<HTML
        <form method="POST" action="index.php?action=addSpecialRequest">
            <label for="reservation_id">Reservation ID:</label>
            <input type="number" id="reservation_id" name="reservation_id" required>

            <label for="special_requests">Special Requests:</label>
            <textarea id="special_requests" name="special_requests" required></textarea>

            <input type="submit" value="Add Special Request">
        </form>
        <a href="index.php?action=reservations">Back to Reservations</a>
        HTML;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addSpecialRequest();
        }
    }

    private function addSpecialRequest() {
        $reservationId = $_POST['reservation_id'];
        $specialRequests = $_POST['special_requests'];
        $success = $this->db->addSpecialRequest($reservationId, $specialRequests);
        if ($success) {
            echo "<p>Special Request Added to Reservation (ID: {$reservationId})</p>";
        } else {
            echo "<p>Failed to add Special Request. Reservation not found.</p>";
        }
    }

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
        echo "<a href='index.php?action=customers'>Back to Customers</a>";
    }

    private function deleteCustomer($customerId) {
        $success = $this->db->deleteCustomer($customerId);
        if ($success) {
            echo "<p>Customer (ID: {$customerId}) has been deleted successfully along with all their reservations.</p>";
        } else {
            echo "<p>Failed to delete customer (ID: {$customerId}). It may not exist or there was an error.</p>";
        }
    }

    private function viewCustomerReservations() {
        $customerId = $_GET['customer_id'] ?? null;
        if ($customerId) {
            $customer = $this->db->getCustomerById($customerId);
            $reservations = $this->db->findReservations($customerId);
            
            echo "<h2>Reservations for " . htmlspecialchars($customer['customerName']) . "</h2>";
            if (!empty($reservations)) {
                echo "<table>";
                echo "<tr><th>Reservation ID</th><th>Reservation Time</th><th>Number of Guests</th><th>Special Requests</th></tr>";
                foreach ($reservations as $reservation) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($reservation['reservationId']) . "</td>";
                    echo "<td>" . htmlspecialchars($reservation['reservationTime']) . "</td>";
                    echo "<td>" . htmlspecialchars($reservation['numberOfGuests']) . "</td>";
                    echo "<td>" . htmlspecialchars($reservation['specialRequests']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No reservations found for this customer.</p>";
            }
        } else {
            echo "<p>Error: Customer ID not provided.</p>";
        }
        echo "<a href='index.php?action=customers'>Back to Customers</a>";
    }
}

$portal = new RestaurantPortal();
$portal->handleRequest();
?>