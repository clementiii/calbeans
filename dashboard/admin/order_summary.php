<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Order | Calbeans Coffee</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="shortcut icon"
      type="image/x-icon"
      href="../../assets/img/icon/favicon.png"
    />

    <!-- STYLES -->
    <link rel="stylesheet" href="../../assets/css/calbeans-style.css" />
    <link rel="stylesheet" href="../../assets/css/nice-select.css" />

</head>
<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');

if (isset($_POST['view_order'])) {
    $order_id = $_POST['order_id'];

    // Fetch the order details including customer name
    $stmt = $mysqli->prepare("SELECT * FROM rpos_orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_object();

    if ($order) {
        // Fetch all the orders made by the customer
        $stmt = $mysqli->prepare("SELECT * FROM rpos_orders WHERE customer_name = ?");
        $stmt->bind_param("s", $order->customer_name);
        $stmt->execute();
        $orders_result = $stmt->get_result();
        ?>
        
        <body class="hero-overly">
        <style>
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 25px;
            height: 100%;
        }

        .card {
            width: 40%;
            height: 100%;
            margin: 0 auto; /* Center the card horizontally */
            padding: 10px; /* Add some padding */
            text-align: center;
            background-color: #f6f1ea;
        }

        html,
        body {
            background-image: url("../../assets/img/hero/4.png");
            background-size: cover;
            font-family: 'Chivo', sans-serif;
            font-weight: 200;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-family: "Brice", "Chivo", sans-serif;
            color: #d4ac63;
            margin-top: 0px;
            font-style: normal;
            font-weight: 500;
            text-transform: normal;
            letter-spacing: 0.1rem;
        }

        .margin {
            margin-top: 1rem;
        }

        </style>
        <!-- Main content -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1></h1>
                        <div class="card shadow">
                            <div class="card-body">
                                <?php
                                while ($order_row = $orders_result->fetch_object()) {
                                    ?>
                                    <h1 class="title">ORDER SUMMARY</h1><hr>
                                    <p><strong>Customer:</strong> <?php echo $order_row->customer_name; ?></p>
                                    <p><strong>Products:</strong> <?php echo $order_row->prod_name; ?></p>
                                    <p><strong>Unit Price:</strong> ₱<?php echo $order_row->prod_price; ?></p>
                                    <p><strong>Quantity:</strong> <?php echo $order_row->prod_qty . ' ' . $order_row->prod_name; ?></p>
                                    <p><strong>Total Price:</strong> ₱<?php echo $order_row->prod_price * $order_row->prod_qty; ?></p>
                                    <p><strong>Status:</strong> <?php echo $order_row->order_status; ?></p>
                                    <hr>
                                    <form action="update_order_status.php" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order_row->order_id; ?>">
                                        <input type="hidden" name="customer_name" value="<?php echo $order_row->customer_name; ?>">
                                        <select name="new_status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary margin">Update Status</button>
                                        <hr>
                                    </form>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- SCRIPT -->
        <script src="../../assets/js/vendor/jquery-1.12.4.min.js"></script>
        <script src="../../assets/js/jquery.nice-select.min.js"></script>

        </body>
        </html>
        <?php
    } else {
        $_SESSION['error'] = "Order not found";
        header("Location: orders_reports.php"); // Redirect back to the order list page
        exit();
    }
}
?>
