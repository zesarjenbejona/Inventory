CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255),
    quantity INT,
    category VARCHAR(255),
    price DECIMAL(10,2),
    date_added DATE
);
