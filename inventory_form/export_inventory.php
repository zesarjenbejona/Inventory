<?php
include 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventory_records.xls");

$result = $conn->query("SELECT * FROM inventory");

echo "ID\tProduct Name\tQuantity\tCategory\tPrice\tDate Added\n";

while ($row = $result->fetch_assoc()) {
    echo $row['id']."\t".$row['product_name']."\t".$row['quantity']."\t".$row['category']."\t".$row['price']."\t".$row['date_added']."\n";
}
?>
