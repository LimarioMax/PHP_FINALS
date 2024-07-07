<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="rent.css">
    
</head>
<body>
    <header>
        <!-- Your header content here -->
    </header>
    <div class="container">
        <?php
        if (isset($_GET['id'])) {
            $videoId = htmlspecialchars($_GET['id']);
            $video = getVideoById($videoId);

            if ($video) {
                ?>
                <h1 class="title">RENT VIDEO</h1>
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text">Title: <?= htmlspecialchars($video['title']) ?></p>
                        <p class="card-text">Production: <?= htmlspecialchars($video['production']) ?></p>
                        <p class="card-text">Release Year: <?= htmlspecialchars($video['release_year']) ?></p>
                        <p class="card-text">Genre: <?= htmlspecialchars($video['genre']) ?></p>
                        <img src="uploads/<?= htmlspecialchars($video['image']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" style="height: 282px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <form id="rentForm" method="post">
                            <input type="hidden" name="video_id" value="<?= $videoId ?>">
                            <div class="form-group">
                                <label>Format:</label><br>
                                <input type="radio" name="format" value="blu_ray" data-price="<?= $video['blu_ray_price'] ?>" data-stock="<?= $video['blu_ray_copies'] ?>" <?= $video['blu_ray_copies'] == 0 ? 'disabled' : '' ?> required> Blu-ray (<?= $video['blu_ray_copies'] ?> available<?= $video['blu_ray_copies'] == 0 ? ' - <span class="out-of-stock">(OUT OF STOCK)</span>' : '' ?>)<br>
                                <input type="radio" name="format" value="dvd" data-price="<?= $video['dvd_price'] ?>" data-stock="<?= $video['dvd_copies'] ?>" <?= $video['dvd_copies'] == 0 ? 'disabled' : '' ?> required> DVD (<?= $video['dvd_copies'] ?> available<?= $video['dvd_copies'] == 0 ? ' - <span class="out-of-stock">(OUT OF STOCK)</span>' : '' ?>)<br>
                                <input type="radio" name="format" value="digital" data-price="<?= $video['digital_price'] ?>" required> Digital (Link)
                            </div>

                            <div class="form-group" id="quantity-group" style="display: none;">
                                <label>Quantity:</label>
                                <input type="number" name="quantity" id="quantity" min="1" oninput="this.value = Math.abs(this.value)">
                            </div>

                            <div class="form-group">
                                <label>Return Due Date:</label>
                                <input type="text" id="due_date" readonly>
                            </div>

                            <div class="form-group">
                                <label>Total Price:</label>
                                <input type="text" id="total_price" readonly>
                            </div>

                            <button type="button" id="payNowBtn" class="btn btn-primary" disabled>Pay Now</button>
                        </form>
                    </div>
                </div>

                <!-- Payment Modal -->
                <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Payment Summary -->
                                <div class="payment-summary">
                                    <h5>Rental Summary</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="uploads/<?= htmlspecialchars($video['image']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" style="max-width: 100%;">
                                        </div>
                                        <div class="col-md-8">
                                            <p><strong>Title:</strong> <?= htmlspecialchars($video['title']) ?></p>
                                            <p><strong>Format:</strong> <span id="summary_format"></span></p>
                                            <p><strong>Total Price:</strong> $<span id="summary_total_price"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Form -->
                                <form id="paymentForm" method="POST">
                                    <input type="hidden" name="video_id" value="<?= $videoId ?>">
                                    <input type="hidden" name="total_price" id="payment_total_price">
                                    <input type="hidden" name="format" id="payment_format">
                                    <input type="hidden" name="quantity" id="payment_quantity">
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
                                    <button type="button" id="confirmRentBtn" class="btn btn-primary">Confirm Rent</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Calculate the due date (one week from today)
                        var dueDate = new Date();
                        dueDate.setDate(dueDate.getDate() + 7);
                        var dd = dueDate.getDate();
                        var mm = dueDate.getMonth() + 1; // January is 0!
                        var yyyy = dueDate.getFullYear();
                        if (dd < 10) {
                            dd = '0' + dd;
                        }
                        if (mm < 10) {
                            mm = '0' + mm;
                        }
                        dueDate = yyyy + '-' + mm + '-' + dd;
                        $('#due_date').val(dueDate);

                        // Enable the quantity input and Pay Now button when a format is selected
                        $('input[name="format"]').change(function() {
                            var selectedFormat = $('input[name="format"]:checked');
                            var price = parseFloat(selectedFormat.data('price'));
                            var stock = parseInt(selectedFormat.data('stock'));

                            if (selectedFormat.val() !== 'digital') {
                                $('#quantity-group').show();
                                $('#quantity').val(1).attr('max', stock);
                                $('#total_price').val(price);
                                $('#payNowBtn').prop('disabled', false);
                            } else {
                                $('#quantity-group').hide();
                                $('#quantity').val(1);
                                $('#total_price').val(price);
                                $('#payNowBtn').prop('disabled', false);
                            }
                        });

                        // Calculate the total price based on quantity
                        $('#quantity').on('input', function() {
                            var quantity = parseInt($(this).val());
                            var selectedFormat = $('input[name="format"]:checked');
                            var price = parseFloat(selectedFormat.data('price'));
                            var totalPrice = price * quantity;
                            $('#total_price').val(totalPrice.toFixed(2));
                        });

  // Validate quantity before proceeding to payment
$('#payNowBtn').click(function() {
    var selectedFormat = $('input[name="format"]:checked').val();
    var quantity = parseInt($('#quantity').val());
    var maxQuantity = parseInt($('input[name="format"]:checked').data('stock'));

    if (selectedFormat !== 'digital' && (quantity <= 0 || quantity > maxQuantity)) {
        alert('Invalid quantity selected.');
        return;
    }

    // Update payment summary
    $('#summary_format').text(selectedFormat);
    $('#summary_total_price').text($('#total_price').val());

    // Update hidden form fields
    $('#payment_format').val(selectedFormat);
    $('#payment_quantity').val(quantity);
    $('#payment_total_price').val($('#total_price').val());

    $('#paymentModal').modal('show');
});

                        // Show/hide payment method fields
                        $('#payment_method').change(function() {
                            var paymentMethod = $(this).val();
                            $('#paypalEmailGroup').hide();
                            $('#cashAmountGroup').hide();
                            $('#cardDetails').hide();

                            if (paymentMethod === 'paypal') {
                                $('#paypalEmailGroup').show();
                            } else if (paymentMethod === 'cash') {
                                $('#cashAmountGroup').show();
                            } else if (paymentMethod === 'credit_card') {
                                $('#cardDetails').show();
                            }
                        });

     

                        // Handle the Confirm Rent button click
                       // Handle the Confirm Rent button click
$('#confirmRentBtn').click(function() {
    var selectedFormat = $('input[name="format"]:checked').val();
    var quantity = parseInt($('#quantity').val());
    var maxQuantity = parseInt($('input[name="format"]:checked').data('stock'));

    if (selectedFormat !== 'digital' && (quantity <= 0 || quantity > maxQuantity)) {
        alert('Invalid quantity selected.');
        return;
    }

    var paymentMethod = $('#payment_method').val();
    if (paymentMethod === '') {
        alert('Please select a payment method.');
        return;
    }

    if (paymentMethod === 'paypal' && !$('#paypal_email').val()) {
        alert('Please enter your PayPal email.');
        return;
    }

if (paymentMethod === 'cash') {
    var cashAmount = $('#cash_amount').val();
    if (!cashAmount || parseFloat(cashAmount) < parseFloat($('#total_price').val())) {
        alert('Cash amount must be greater than or equal to total price.');
        return;
    }
}

    // Confirmation prompt
    var confirmRent = confirm('Are you sure you want to rent the video?');
    if (!confirmRent) {
        return;
    } else {
        alert('Video Successfully Rented');
    }

    // Set the form action dynamically
    $('#paymentForm').attr('action', 'process_rent.php');
    // Submit the form
    $('#paymentForm').submit();
});
                    });
                </script>
                <?php
            } else {
                echo '<p>Video not found.</p>';
            }
        } else {
            echo '<p>No video ID provided.</p>';
        }
        ?>
    </div>
    <footer>
        <!-- Your footer content here -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
