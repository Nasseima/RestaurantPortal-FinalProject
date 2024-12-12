<h3>Add Customer</h3>
<form method="POST" action="index.php?action=customers&tab=addCustomer">
    <label for="customer_name">Customer Name:</label>
    <input type="text" id="customer_name" name="customer_name" required>

    <label for="contact_info">Contact Info:</label>
    <input type="text" id="contact_info" name="contact_info" required>

    <input type="submit" value="Add Customer">
</form>