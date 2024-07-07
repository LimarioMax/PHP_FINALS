<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

// Check if rental ID and days late are provided
if (isset($_GET['id']) && isset($_GET['days_late'])) {
    $rentalId = htmlspecialchars($_GET['id']);
    $daysLate = intval(htmlspecialchars($_GET['days_late']));
    $rental = getRentalById($rentalId);

    if ($rental) {
        $videoId = $rental['video_id'];
        $video = getVideoById($videoId);

        // Determine the late fee based on the format
        $lateFee = 0;
        if ($rental['format'] == 'blu_ray') {
            $lateFee = $video['blu_ray_late_fee'] * $daysLate;
        } elseif ($rental['format'] == 'dvd') {
            $lateFee = $video['dvd_late_fee'] * $daysLate;
        }

        // Check if form is submitted for payment
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate and process the payment
            $paymentMethod = htmlspecialchars($_POST['payment_method']);
            $paymentValid = false;

            if ($paymentMethod === 'paypal') {
                // Validate PayPal email
                if (isset($_POST['paypal_email']) && filter_var($_POST['paypal_email'], FILTER_VALIDATE_EMAIL)) {
                    // Process PayPal payment (details skipped for brevity)
                    $paymentValid = true;
                }
            } elseif ($paymentMethod === 'cash') {
                // Validate cash amount
                if (isset($_POST['cash_amount']) && is_numeric($_POST['cash_amount']) && $_POST['cash_amount'] >= $lateFee) {
                    // Process cash payment (details skipped for brevity)
                    $paymentValid = true;
                }
            } elseif ($paymentMethod === 'credit_card') {
                // Validate credit card details (for demonstration purposes, basic validation)
                if (isset($_POST['card_number']) && isset($_POST['expiry_date']) && isset($_POST['cvv'])) {
                    // Process credit card payment (details skipped for brevity)
                    $paymentValid = true;
                }
            }

            // If payment is valid, redirect to confirm return
            if ($paymentValid) {
                header("Location: confirm_return.php?id={$rentalId}");
                exit;
            }
        }
    } else {
        echo '<p>Rental not found.</p>';
    }
} else {
    echo '<p>No rental ID or days late provided.</p>';
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Late Fee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
        .payment-summary {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pay Late Fee</h1>
        <div class="payment-summary">
            <h5>Rental Summary</h5>
            <p><strong>Title:</strong> <?= htmlspecialchars($video['title']) ?></p>
            <p><strong>Format:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $rental['format']))) ?></p>
            <p><strong>Days Late:</strong> <?= $daysLate ?></p>
            <p><strong>Total Late Fee:</strong> $<?= number_format($lateFee, 2) ?></p>
        </div>

        <form id="paymentForm" method="POST">
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method:</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="paypal">PayPal</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                </select>
            </div>
            <div id="paypalEmailGroup" class="mb-3" style="display: none;">
                <label for="paypal_email" class="form-label">PayPal Email:</label>
                <input type="email" class="form-control" id="paypal_email" name="paypal_email" required>
            </div>
            <div id="cashAmountGroup" class="mb-3" style="display: none;">
                <label for="cash_amount" class="form-label">Cash Amount:</label>
                <input type="number" class="form-control" id="cash_amount" name="cash_amount" step="0.01" min="0" required>
            </div>
            <div id="cardDetails" style="display: none;">
                <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number:</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" maxlength="19" pattern="\d{4} \d{4} \d{4} \d{4}" required>
                </div>
                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Expiry Date (MM/YY):</label>
                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" maxlength="5" pattern="\d{2}/\d{2}" required>
                </div>
                <div class="mb-3">
                    <label for="cvv" class="form-label">CVV:</label>
                    <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3" required>
                </div>
            </div>
            <button type="button" id="confirmRentBtn" class="btn btn-primary">Pay Now</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#payment_method').change(function() {
                var paymentMethod = $(this).val();

                if (paymentMethod === 'paypal') {
                    $('#paypalEmailGroup').css('display', 'block');
                    $('#cashAmountGroup').css('display', 'none');
                    $('#cardDetails').css('display', 'none');
                } else if (paymentMethod === 'cash') {
                    $('#paypalEmailGroup').css('display', 'none');
                    $('#cashAmountGroup').css('display', 'block');
                    $('#cardDetails').css('display', 'none');
                } else if (paymentMethod === 'credit_card') {
                    $('#paypalEmailGroup').css('display', 'none');
                    $('#cashAmountGroup').css('display', 'none');
                    $('#cardDetails').css('display', 'block');
                } else {
                    $('#paypalEmailGroup').css('display', 'none');
                    $('#cashAmountGroup').css('display', 'none');
                    $('#cardDetails').css('display', 'none');
                }
            });

            $('#confirmRentBtn').click(function() {
                var paymentMethod = $('#payment_method').val();

                if (paymentMethod === 'paypal') {
                    var paypalEmail = $('#paypal_email').val();
                    if (!paypalEmail || !validateEmail(paypalEmail)) {
                        alert('Please enter a valid PayPal email.');
                        return;
                    }
                } else if (paymentMethod === 'cash') {
                    var cashAmount = $('#cash_amount').val();
                    if (!cashAmount || isNaN(cashAmount) || parseFloat(cashAmount) < <?= $lateFee ?>) {
                        alert('Please enter a valid cash amount, minimum $<?= number_format($lateFee, 2) ?>.');
                        return;
                    }
                } else if (paymentMethod === 'credit_card') {
                    var cardNumber = $('#card_number').val();
                    var expiryDate = $('#expiry_date').val();
                    var cvv = $('#cvv').val();

                    if (!cardNumber || !validateCardNumber(cardNumber)) {
                        alert('Please enter a valid card number.');
                        return;
                    }
                    if (!expiryDate || !validateExpiryDate(expiryDate)) {
                        alert('Please enter a valid expiry date (MM/YY).');
                        return;
                    }
                    if (!cvv || isNaN(cvv) || cvv.length !== 3) {
                        alert('Please enter a valid CVV.');
                        return;
                    }
                } else {
                    alert('Please select a payment method.');
                    return;
                }

                // Set the form action dynamically to confirm_return.php?id={$rentalId}
                $('#paymentForm').attr('action', 'confirm_return.php?id=<?= $rentalId ?>');

                // Submit the form
                $('#paymentForm').submit();
            });

            function validateEmail(email) {
                var re = /\S+@\S+\.\S+/;
                return re.test(email);
            }

            function validateCardNumber(cardNumber) {
                var re = /\d{4} \d{4} \d{4} \d{4}/;
                return re.test(cardNumber);
            }

            function validateExpiryDate(expiryDate) {
                var re = /\d{2}\/\d{2}/;
                return re.test(expiryDate);
            }
        });
    </script>
</body>
</html>
