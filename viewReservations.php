<h3>View Reservations</h3>
<?php if (empty($reservations)): ?>
    <p>No reservations found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>Customer Name</th>
            <th>Contact Info</th>
            <th>Reservation Time</th>
            <th>Number of Guests</th>
            <th>Special Requests</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($reservations as $reservation): ?>
        <tr>
            <td><?= htmlspecialchars($reservation['reservationId']) ?></td>
            <td><?= htmlspecialchars($reservation['customerName']) ?></td>
            <td><?= htmlspecialchars($reservation['contactInfo']) ?></td>
            <td><?= htmlspecialchars($reservation['reservationTime']) ?></td>
            <td><?= htmlspecialchars($reservation['numberOfGuests']) ?></td>
            <td><?= htmlspecialchars($reservation['specialRequests']) ?></td>
            <td>
                <a href="index.php?action=reservations&tab=viewReservations&delete=<?= $reservation['reservationId'] ?>" onclick="return confirm('Are you sure you want to delete this reservation?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>