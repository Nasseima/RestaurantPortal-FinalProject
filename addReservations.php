<h3>Add Reservation</h3>
<form method="POST" action="index.php?action=reservations&tab=addReservation">
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