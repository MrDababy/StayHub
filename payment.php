<?php
session_start();
require_once 'db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$payment_methods = [
    ['value' => 'card', 'label' => 'Credit/Debit Card', 'image' => 'images/mastercard.jpg', 'description' => 'Pay with Visa, MasterCard or other cards.'],
    ['value' => 'mpesa', 'label' => 'M-Pesa', 'image' => 'images/mpesa.jpg', 'description' => 'Use your M-Pesa mobile wallet.'],
    ['value' => 'airtel', 'label' => 'Airtel Money', 'image' => 'images/airtelmoney.jpg', 'description' => 'Pay with Airtel Money on your phone.'],
    ['value' => 'halopesa', 'label' => 'HaloPesa', 'image' => 'images/halopesa.jpg', 'description' => 'Use HaloPesa mobile payment service.'],
];

$selected_method = $_POST['payment_method'] ?? 'card';
if (!in_array($selected_method, array_column($payment_methods, 'value'), true)) {
    $selected_method = 'card';
}

$bed_id = isset($_GET['bed_id']) ? intval($_GET['bed_id']) : (isset($_POST['bed_id']) ? intval($_POST['bed_id']) : 0);
$bed = null;
if ($bed_id > 0) {
    $bq = $conn->prepare(
        'SELECT beds.bed_id, rooms.room_number, beds.bed_position, rooms.floor, blocks.block_name, rooms.price 
         FROM beds 
         JOIN rooms ON beds.room_id = rooms.room_id 
         JOIN blocks ON rooms.block_id = blocks.block_id 
         WHERE beds.bed_id = ? LIMIT 1'
    );
    $bq->bind_param('i', $bed_id);
    $bq->execute();
    $bq->bind_result($b_bed_id, $b_room_number, $b_bed_position, $b_floor, $b_block_name, $b_price);
    if ($bq->fetch()) {
        $bed = [
            'bed_id' => $b_bed_id,
            'room_number' => $b_room_number,
            'bed_position' => $b_bed_position,
            'floor' => $b_floor,
            'block_name' => $b_block_name,
            'price' => $b_price,
        ];
    }
    $bq->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $bed_id = intval($_POST['bed_id'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');
    $card_number = trim($_POST['card_number'] ?? '');
    $card_holder = trim($_POST['card_holder'] ?? '');
    $expiry_date = trim($_POST['expiry_date'] ?? '');
    $cvc = trim($_POST['cvc'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');

    if ($bed_id <= 0) {
        $error = 'Invalid bed selected.';
    } elseif (!in_array($payment_method, array_column($payment_methods, 'value'), true)) {
        $error = 'Please select a valid payment method.';
    } elseif ($payment_method === 'card' && ($card_number === '' || $card_holder === '' || $expiry_date === '' || $cvc === '')) {
        $error = 'Please complete all card payment fields.';
    } elseif (in_array($payment_method, ['mpesa', 'airtel', 'halopesa'], true) && $mobile_number === '') {
        $error = 'Please enter your mobile number for the selected payment method.';
    } else {
        $date = date('Y-m-d');
        $status = 'Confirmed';
        $ins = $conn->prepare('INSERT INTO bookings (user_id, bed_id, booking_date, status) VALUES (?, ?, ?, ?)');
        $ins->bind_param('iiss', $user_id, $bed_id, $date, $status);
        if ($ins->execute()) {
            $booking_id = $ins->insert_id;
            $upd = $conn->prepare('UPDATE beds SET status = ? WHERE bed_id = ?');
            $occupied = 'Occupied';
            $upd->bind_param('si', $occupied, $bed_id);
            $upd->execute();
            $upd->close();

            if ($bed && isset($bed['price'])) {
                $payment_ins = $conn->prepare('INSERT INTO payments (booking_id, amount, payment_method, payment_status, payment_date) VALUES (?, ?, ?, ?, ?)');
                $paid = 'Paid';
                $payment_date = date('Y-m-d');
                $amount = floatval($bed['price']);
                $payment_ins->bind_param('idsss', $booking_id, $amount, $payment_method, $paid, $payment_date);
                $payment_ins->execute();
                $payment_ins->close();
            }

            $ins->close();
            header('Location: userdb.php');
            exit;
        } else {
            $error = 'Unable to record booking.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>Payment</title>
  <link rel="stylesheet" href="paymentstylesheet.css">
</head>

<body>
  <main class="payment-main">
    <h2>Select Payment Method</h2>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!$bed): ?>
      <p>No bed selected or bed not found. Please choose a room first.</p>
      <p><a class="button" href="shhomepage.php">Browse rooms</a></p>
    <?php else: ?>
      <div class="card">
        <h3>Booking</h3>
        <p><strong>Block:</strong> <?php echo htmlspecialchars($bed['block_name']); ?></p>
        <p><strong>Room:</strong> <?php echo htmlspecialchars($bed['room_number']); ?></p>
        <p><strong>Bed:</strong> <?php echo htmlspecialchars($bed['bed_position']); ?></p>
        <p><strong>Floor:</strong> <?php echo htmlspecialchars($bed['floor']); ?></p>
        <p><strong>Amount:</strong> <?php echo htmlspecialchars(number_format($bed['price'],2)); ?></p>
      </div>

      <form method="POST" class="payment-form">
        <input type="hidden" name="bed_id" value="<?php echo htmlspecialchars($bed['bed_id']); ?>">

        <div class="payment-options">
          <?php foreach ($payment_methods as $method): ?>
            <label class="card payment-card<?php echo $selected_method === $method['value'] ? ' selected' : ''; ?>">
              <input type="radio" name="payment_method" value="<?php echo htmlspecialchars($method['value']); ?>" <?php echo $selected_method === $method['value'] ? 'checked' : ''; ?>>
              <img src="<?php echo htmlspecialchars($method['image']); ?>" alt="<?php echo htmlspecialchars($method['label']); ?>">
              <span><?php echo htmlspecialchars($method['label']); ?></span>
              <small><?php echo htmlspecialchars($method['description']); ?></small>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="payment-fields" id="paymentFields">
          <div class="field card-only" style="display: <?php echo $selected_method === 'card' ? 'block' : 'none'; ?>;">
            <label for="card_number">Card number</label>
            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" value="<?php echo htmlspecialchars($card_number ?? ''); ?>">
          </div>
          <div class="field card-only" style="display: <?php echo $selected_method === 'card' ? 'block' : 'none'; ?>;">
            <label for="card_holder">Card holder</label>
            <input type="text" id="card_holder" name="card_holder" placeholder="Name on card" value="<?php echo htmlspecialchars($card_holder ?? ''); ?>">
          </div>
          <div class="row">
            <div class="field card-only" style="flex:1; display: <?php echo $selected_method === 'card' ? 'block' : 'none'; ?>;">
              <label for="expiry_date">Expiry date</label>
              <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" value="<?php echo htmlspecialchars($expiry_date ?? ''); ?>">
            </div>
            <div class="field card-only" style="flex:1; display: <?php echo $selected_method === 'card' ? 'block' : 'none'; ?>;">
              <label for="cvc">CVC</label>
              <input type="text" id="cvc" name="cvc" placeholder="123" value="<?php echo htmlspecialchars($cvc ?? ''); ?>">
            </div>
          </div>

          <div class="field mobile-only" style="display: <?php echo in_array($selected_method, ['mpesa', 'airtel', 'halopesa'], true) ? 'block' : 'none'; ?>;">
            <label for="mobile_number">Mobile number</label>
            <input type="text" id="mobile_number" name="mobile_number" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($mobile_number ?? ''); ?>">
          </div>
        </div>

        <button class="button" type="submit" name="confirm_payment">Confirm Payment</button>
      </form>
    <?php endif; ?>
  </main>
  <script>
    const paymentCards = document.querySelectorAll('.payment-card');
    const cardFields = document.querySelectorAll('.card-only');
    const mobileFields = document.querySelectorAll('.mobile-only');

    paymentCards.forEach((card) => {
      card.addEventListener('click', () => {
        const radio = card.querySelector('input[type="radio"]');
        radio.checked = true;
        paymentCards.forEach((c) => c.classList.remove('selected'));
        card.classList.add('selected');
        const selected = radio.value;

        if (selected === 'card') {
          cardFields.forEach((field) => field.style.display = 'block');
          mobileFields.forEach((field) => field.style.display = 'none');
        } else {
          cardFields.forEach((field) => field.style.display = 'none');
          mobileFields.forEach((field) => field.style.display = 'block');
        }
      });
    });
  </script>
</body>
</html>