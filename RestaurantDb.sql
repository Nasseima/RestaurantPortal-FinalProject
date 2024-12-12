-- Create the database
CREATE DATABASE IF NOT EXISTS restaurant_reservations;

-- Use the database
USE restaurant_reservations;

-- Create Customers table
CREATE TABLE Customers (
    customerId INT NOT NULL UNIQUE AUTO_INCREMENT,
    customerName VARCHAR(45) NOT NULL,
    contactInfo VARCHAR(200),
    PRIMARY KEY (customerId)
);

-- Create Reservations table
CREATE TABLE Reservations (
    reservationId INT NOT NULL UNIQUE AUTO_INCREMENT,
    customerId INT NOT NULL,
    reservationTime DATETIME NOT NULL,
    numberOfGuests INT NOT NULL,
    specialRequests VARCHAR(200),
    PRIMARY KEY (reservationId),
    FOREIGN KEY (customerId) REFERENCES Customers(customerId)
);

-- Create DiningPreferences table
CREATE TABLE DiningPreferences (
    preferenceId INT NOT NULL UNIQUE AUTO_INCREMENT,
    customerId INT NOT NULL,
    favoriteTable VARCHAR(45),
    dietaryRestrictions VARCHAR(200),
    PRIMARY KEY (preferenceId),
    FOREIGN KEY (customerId) REFERENCES Customers(customerId)
);

-- Stored Procedure: findReservations
DELIMITER //
CREATE PROCEDURE findReservations(IN customer_id INT)
BEGIN
    SELECT * FROM Reservations WHERE customerId = customer_id;
END //
DELIMITER ;

-- Stored Procedure: addSpecialRequest
DELIMITER //
CREATE PROCEDURE addSpecialRequest(IN reservation_id INT, IN requests VARCHAR(200))
BEGIN
    UPDATE Reservations SET specialRequests = requests WHERE reservationId = reservation_id;
END //
DELIMITER ;

-- Stored Procedure: addReservation
DELIMITER //
CREATE PROCEDURE addReservation(
    IN p_customerName VARCHAR(45),
    IN p_contactInfo VARCHAR(200),
    IN p_reservationTime DATETIME,
    IN p_numberOfGuests INT,
    IN p_specialRequests VARCHAR(200)
)
BEGIN
    DECLARE v_customerId INT;
    
    -- Check if customer exists, if not, create a new customer
    SELECT customerId INTO v_customerId FROM Customers WHERE customerName = p_customerName AND contactInfo = p_contactInfo;
    
    IF v_customerId IS NULL THEN
        INSERT INTO Customers (customerName, contactInfo) VALUES (p_customerName, p_contactInfo);
        SET v_customerId = LAST_INSERT_ID();
    END IF;
    
    -- Add the reservation
    INSERT INTO Reservations (customerId, reservationTime, numberOfGuests, specialRequests)
    VALUES (v_customerId, p_reservationTime, p_numberOfGuests, p_specialRequests);
    
    SELECT LAST_INSERT_ID() AS reservationId;
END //
DELIMITER ;

-- Populate tables with sample data
INSERT INTO Customers (customerName, contactInfo) VALUES
('John Doe', 'john@example.com'),
('Jane Smith', '555-1234'),
('Bob Johnson', 'bob@example.com');

INSERT INTO Reservations (customerId, reservationTime, numberOfGuests, specialRequests) VALUES
(1, '2023-06-01 18:00:00', 2, 'Window seat preferred'),
(2, '2023-06-02 19:30:00', 4, 'Birthday celebration'),
(3, '2023-06-03 20:00:00', 3, 'Gluten-free options needed');

INSERT INTO DiningPreferences (customerId, favoriteTable, dietaryRestrictions) VALUES
(1, 'Table 5', 'None'),
(2, 'Booth 3', 'Vegetarian'),
(3, 'Table 10', 'Gluten-free');

DROP PROCEDURE IF EXISTS addReservation;
DROP PROCEDURE IF EXISTS findReservations;
DROP PROCEDURE IF EXISTS addSpecialRequest;