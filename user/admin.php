<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $_SESSION['session_expiry']) {
    session_unset();
    session_destroy();
    $session_expired = true;
}

include '../config/db_connection.php';

// Fetch data from database
$yearlyData = [];
$sql = "SELECT YEAR(dateIssued) AS year, SUM(amount) AS total FROM funds GROUP BY YEAR(dateIssued) ORDER BY year";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $yearlyData[$row['year']] = $row['total'];
}

// Convert PHP arrays to JSON
$years = json_encode(array_keys($yearlyData));  // Labels (Years)
$totals = json_encode(array_values($yearlyData)); // Data (Total Funds)

// Fetch weekly data (last 7 days)
$weeklyLabels = [];
$weeklyTotals = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date("Y-m-d", strtotime("-$i days"));
    $weeklyLabels[] = date("D", strtotime($date)); // Format as Mon, Tue, etc.

    $sql = "SELECT SUM(amount) AS total FROM funds WHERE DATE(dateIssued) = '$date'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $weeklyTotals[] = $row['total'] ?? 0; // Default to 0 if no data
}

// Convert PHP arrays to JSON for JavaScript
$weeklyLabelsJSON = json_encode($weeklyLabels);
$weeklyTotalsJSON = json_encode($weeklyTotals);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JERN - Admin Panel</title>
    <!-- Favicon -->
    <link rel="icon" href="../Images/jern-logo.png" type="image/png">
    <!-- Tabler Core CSS -->
    <link href="../public/dist/css/tabler.min.css" rel="stylesheet">
    <link href="../public/dist/css/tabler-vendors.min.css" rel="stylesheet">
    <link href="../public/dist/css/demo.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/global.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="pt-6">
    <div class="page">
        <!-- Header -->
        <header class="navbar navbar-expand-md navbar-light bg-light fixed-top custom-shadow">
            <div class="container-fluid">
                <h2 id="lms-text-logo">JERN</h2>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Page Title -->
                <h1 class="page-title pt-3">Funds Management</h1>

                <!-- Cards for Total Amounts -->
                <div class="row row-cards">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Funds</div>
                                </div>
                                <div class="h1 mb-3">
                                    <?php
                                    $sql = "SELECT SUM(amount) AS total FROM funds";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo "PHP" . number_format($row['total'], 2);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Funds Issued This Month</div>
                                </div>
                                <div class="h1 mb-3">
                                    <?php
                                    $sql = "SELECT SUM(amount) AS total FROM funds WHERE MONTH(dateIssued) = MONTH(CURRENT_DATE())";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo "PHP" . number_format($row['total'], 2);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Funds Issued This Year</div>
                                </div>
                                <div class="h1 mb-3">
                                    <?php
                                    $sql = "SELECT SUM(amount) AS total FROM funds WHERE YEAR(dateIssued) = YEAR(CURRENT_DATE())";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo "PHP" . number_format($row['total'], 2);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row row-cards">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Funds Distribution</h3>
                                <canvas id="fundsChart" style="height: 442px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 h-100">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Monthly Funds Issued</h3>
                                <canvas id="monthlyChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Weekly Funds Issued</h3>
                                <canvas id="weeklyChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Yearly Funds Issued</h3>
                                <canvas id="yearlyChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Funds Table -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body" id="fundsTable">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title">Funds List</h3>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFundModal">
                                <i class="fas fa-plus"></i> Add Fund
                            </button>
                        </div>
                        
                        <!-- Make the table responsive on smaller screens -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Date Issued</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM funds";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$row['id']}</td>
                                                    <td>{$row['firstName']}</td>
                                                    <td>{$row['lastName']}</td>
                                                    <td>{$row['dateIssued']}</td>
                                                    <td>PHP" . number_format($row['amount'], 2) . "</td>
                                                    <td>
                                                        <button class='btn btn-sm btn-warning edit-fund' data-id='{$row['id']}'><i class='fas fa-edit'></i></button>
                                                        <button class='btn btn-sm btn-danger delete-fund' data-id='{$row['id']}'><i class='fas fa-trash'></i></button>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No funds found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Add Fund Modal -->
    <div class="modal fade" id="addFundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Fund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addFundForm">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="dateIssued" class="form-label">Date Issued</label>
                            <input type="date" class="form-control" id="dateIssued" name="dateIssued" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Fund</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Fund Modal -->
    <div class="modal fade" id="editFundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Fund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editFundForm">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDateIssued" class="form-label">Date Issued</label>
                            <input type="date" class="form-control" id="editDateIssued" name="dateIssued" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAmount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="editAmount" name="amount" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Fund</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler Core JS -->
    <script src="../public/dist/js/tabler.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="../js/admin.js"></script>

    <!-- Custom JS -->
    <script>
        $(document).ready(function () {

            // Charts

            // Weekly Chart
            const weeklyChart = new Chart(document.getElementById('weeklyChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo $weeklyLabelsJSON; ?>, // Days of the week
                    datasets: [{
                        label: 'Funds Issued',
                        data: <?php echo $weeklyTotalsJSON; ?>, // Funds data
                        backgroundColor: '#ffcc00'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const fundsChart = new Chart(document.getElementById('fundsChart'), {
                type: 'pie',
                data: {
                    labels: ['Total Funds', 'This Month', 'This Year'],
                    datasets: [{
                        data: [
                            <?php
                            $sql = "SELECT SUM(amount) AS total FROM funds";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            echo $row['total'] . ",";
                            ?>
                            <?php
                            $sql = "SELECT SUM(amount) AS total FROM funds WHERE MONTH(dateIssued) = MONTH(CURRENT_DATE())";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            echo $row['total'] . ",";
                            ?>
                            <?php
                            $sql = "SELECT SUM(amount) AS total FROM funds WHERE YEAR(dateIssued) = YEAR(CURRENT_DATE())";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        ],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });

            const monthlyChart = new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Funds Issued',
                        data: [
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $sql = "SELECT SUM(amount) AS total FROM funds WHERE MONTH(dateIssued) = $i";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['total'] . ",";
                            }
                            ?>
                        ],
                        backgroundColor: '#4e73df'
                    }]
                }
            });

            const yearlyChart = new Chart(document.getElementById('yearlyChart'), {
                type: 'line',
                data: {
                    labels: <?php echo $years; ?>, 
                    datasets: [{
                        label: 'Total Funds Issued',
                        data: <?php echo $totals; ?>, 
                        borderColor: '#4e73df',
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>