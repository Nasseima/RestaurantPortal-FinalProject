<h3>Add Special Request</h3>
<form method="POST" action="index.php?action=reservations&tab=addSpecialRequest">
    <label for="reservation_id">Reservation ID:</label>
    <input type="number" id="reservation_id" name="reservation_id" required>

    <label for="special_requests">Special Requests:</label>
    <textarea id="special_requests" name="special_requests" required></textarea>

    <input type="submit" value="Add Special Request">
</form>