<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tecroot";

$conn = mysqli_connect("localhost", "root", "", "tecroot");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $sql = "UPDATE employees SET name='$name', position='$position', salary='$salary' WHERE id=$id";
    } else {
        $sql = "INSERT INTO employees (name, position, salary) VALUES ('$name', '$position', '$salary')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: emp.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM employees WHERE id=$id";
    $conn->query($sql);
    header("Location: emp.php");
    exit();
}

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql = "SELECT * FROM employees WHERE name LIKE '%$search%' OR position LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM employees";
}
$result = $conn->query($sql);

$edit_employee = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM employees WHERE id=$id";
    $edit_employee_result = $conn->query($sql);
    $edit_employee = $edit_employee_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="icon" href="2.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0a0a0a;
            color: #0f0;
            font-family: 'Orbitron', sans-serif;
            text-align: center;
        }
        .container {
            margin-top: 50px;
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px #0f0;
        }
        .table {
            background-color: #000;
            color: #0f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th, .table td {
            border: 1px solid #0f0;
        }
        .btn-custom {
            background-color: #0f0;
            color: #121212;
            font-weight: bold;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px #0f0;
        }
        .btn-custom:hover {
            background-color: #00ff00;
            box-shadow: 0 0 10px #0f0;
        }
        .btn-secondary {
            background-color: #444;
            border: 1px solid #0f0;
            color: #0f0;
        }
        input {
            background-color: #222;
            color: #0f0;
            border: 1px solid #0f0;
        }
        .navbar {
            background-color: #198754;
        }
        .navbar-brand {
            font-weight: bold;
            color: white;
        }
        .navbar-nav .nav-link {
            color: white !important;
        }
        .footer {
            background-color: #145a32;
            color: white;
            text-align: center;
            padding: 15px 0;
        }
        .footer a {
            color: white;
            margin: 0 10px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="1.png" alt="Tecroot Logo" height="30"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="adminIndex.html"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-shopping-bag"></i> Inventory</a></li>
                <li class="nav-item"><a class="nav-link" href="account.php"><i class="fas fa-info-circle"></i> Add control</a></li>
                <li class="nav-item"><a class="nav-link" href="customerProfile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-3">
        <?php echo isset($edit_employee) ? 'Edit Employee' : 'Add New Employee'; ?>
    </h2>

    <form method="POST" class="mb-3">
        <div class="input-group mb-2">
            <input type="text" name="name" class="form-control" placeholder="Employee Name" required 
                   value="<?php echo $edit_employee['name'] ?? ''; ?>">
            <input type="text" name="position" class="form-control" placeholder="Position" required 
                   value="<?php echo $edit_employee['position'] ?? ''; ?>">
            <input type="number" name="salary" class="form-control" placeholder="Salary (LKR)" required 
                   value="<?php echo $edit_employee['salary'] ?? ''; ?>">
            <input type="hidden" name="edit_id" value="<?php echo $edit_employee['id'] ?? ''; ?>">
            <button type="submit" name="add_employee" class="btn btn-custom">
                <?php echo isset($edit_employee) ? 'Update' : 'Add'; ?> Employee
            </button>
            <?php if (isset($edit_employee)) : ?>
                <a href="emp.php" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Search Bar -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or position" 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-custom">Search</button>
            <?php if (isset($_GET['search']) && $_GET['search'] !== '') : ?>
                <a href="emp.php" class="btn btn-secondary ms-2">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Salary (LKR)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['name']."</td>
                            <td>".$row['position']."</td>
                            <td>LKR ".number_format($row['salary'], 2)."</td>
                            <td>
                                <a href='?edit=".$row['id']."' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='?delete=".$row['id']."' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure?')\">Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No employees found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="generate_employee_report.php" class="btn btn-custom mt-3">Generate Salary Report</a>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
        <p>© 2025 Tecroot. All rights reserved.</p>
    </div>
</footer>

</body>
</html>

<?php
$conn->close();
?>
