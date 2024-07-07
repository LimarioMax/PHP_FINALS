<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Retrieve rental details from session
if (isset($_SESSION['rental_details'])) {
    $rentalDetails = $_SESSION['rental_details'];
    $videoId = $rentalDetails['video_id'];
    $format = $rentalDetails['format'];
    $quantity = $rentalDetails['quantity'];
    $totalPrice = $rentalDetails['total_price'];

    // Display payment form and summary
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Payment Form</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h1>Payment Method</h1>
            <div id="paymentMethod">
                <form action="process_payment.php" method="post">
                    <input type="hidden" name="video_id" value="<?= $videoId ?>">
                    <input type="hidden" name="format" value="<?= $format ?>">
                    <input type="hidden" name="quantity" value="<?= $quantity ?>">
                    <input type="hidden" name="total_price" value="<?= $totalPrice ?>">
                    
                    <h3>Select Payment Method:</h3>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="creditCardRadio" value="credit_card" required>
                        <label class="form-check-label" for="creditCardRadio">
                            Credit Card
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="paypalRadio" value="paypal" required>
                        <label class="form-check-label" for="paypalRadio">
                            PayPal
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="cashRadio" value="cash" required>
                        <label class="form-check-label" for="cashRadio">
                            Cash
                        </label>
                    </div>

                    <div id="creditCardFields" style="display: none;">
                        <div class="form-group">
                            <label for="ccNumber">Credit Card Number:</label>
                            <input type="text" class="form-control" id="ccNumber" name="cc_number" required>
                        </div>
                        <div class="form-group">
                            <label for="ccExpiry">Expiry Date:</label>
                            <input type="text" class="form-control" id="ccExpiry" name="cc_expiry" required>
                        </div>
                        <div class="form-group">
                            <label for="ccCvv">CVV:</label>
                            <input type="text" class="form-control" id="ccCvv" name="cc_cvv" required>
                        </div>
                    </div>

                    <div id="paypalFields" style="display: none;">
                        <div class="form-group">
                            <label for="paypalEmail">PayPal Email:</label>
                            <input type="email" class="form-control" id="paypalEmail" name="paypal_email" required>
                        </div>
                    </div>

                    <div id="cashFields" style="display: none;">
                        <div class="form-group">
                            <label for="cashAmount">Cash Amount (Minimum $<?= $totalPrice ?>):</label>
                            <input type="number" class="form-control" id="cashAmount" name="cash_amount" min="<?= $totalPrice ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <h3>Payment Summary</h3>
                        <p><strong>Title:</strong> <?= htmlspecialchars($video['title']) ?></p>
                        <p><strong>Quantity:</strong> <?= $quantity ?></p>
                        <p><strong>Total Price:</strong> $<?= $totalPrice ?></p>
                        <p><strong>Format:</strong> <?= ucfirst($format) ?></p>
                    </div>

                    <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                </form>
            </div>
        </div>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $('input[type="radio"]').change(function() {
                    var selectedMethod = $(this).val();
                    $('#creditCardFields').hide();
                    $('#paypalFields').hide();
                    $('#cashFields').hide();

                    if (selectedMethod === 'credit_card') {
                        $('#creditCardFields').show();
                    } else if (selectedMethod === 'paypal') {
                        $('#paypalFields').show();
                    } else if (selectedMethod === 'cash') {
                        $('#cashFields').show();
                    }
                });
            });
        </script>
    </body>
    </html>
    <?php
} else {
    echo "Rental details not found.";
}
?>
