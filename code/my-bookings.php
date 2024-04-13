<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        .no-bookings {
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>My Bookings</h1>

    <?php
    session_start();

    if(isset($_SESSION['isLoggedIn'])) {
        include 'dbCon.php';
        $conn = connect();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $user_id = $_SESSION['id'];
        $sql = "SELECT * FROM booking_details WHERE c_id = $user_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Booking ID</th><th>Customer Name</th><th>Phone</th><th>Booking Date</th><th>Booking Time</th><th>Status</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["booking_id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["phone"] . "</td>";
                echo "<td>" . $row["booking_date"] . "</td>";
                echo "<td>" . $row["booking_time"] . "</td>";
                echo "<td>" . ($row["status"] == 1 ? "Confirmed" : "Rejected") . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-bookings'>No bookings found for this user.</p>";
        }

        $conn->close();
    } else {
        header("Location: login.php");
        exit();
    }
    ?>

</div>

</body>
</html>
