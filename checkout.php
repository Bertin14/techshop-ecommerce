<?php
require 'includes/db.php';
require 'includes/header.php';

if (empty($_SESSION['cart'])) {
  header('Location: /cart.php');
  exit;
}

// HANDLE ORDER SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
  $full_name = htmlspecialchars(trim($_POST['full_name']));
  $email     = htmlspecialchars(trim($_POST['email']));
  $phone     = htmlspecialchars(trim($_POST['phone']));
  $address   = htmlspecialchars(trim($_POST['address']));
  $payment   = htmlspecialchars(trim($_POST['payment_method']));

  // Save customer
  $stmt = $pdo->prepare("INSERT INTO customers (full_name, email, phone, address) VALUES (?, ?, ?, ?)");
  $stmt->execute([$full_name, $email, $phone, $address]);
  $customer_id = $pdo->lastInsertId();

  // Calculate total
  $total = 0;
  foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
  }

  // Save order
  $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'pending')");
  $stmt->execute([$customer_id, $total]);
  $order_id = $pdo->lastInsertId();

  // Save order items
  foreach ($_SESSION['cart'] as $product_id => $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);
    $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $stmt->execute([$item['quantity'], $product_id]);
  }

  $_SESSION['cart'] = [];
  $_SESSION['last_payment'] = $payment;
  header("Location: /order-confirmation.php?order_id=$order_id");
  exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
  $total += $item['price'] * $item['quantity'];
}
?>

<div class="container" style="max-width:800px;">
  <h2 style="margin-bottom:30px;">📦 Checkout</h2>

  <div style="display:flex; gap:30px; flex-wrap:wrap;">

    <!-- CUSTOMER FORM -->
    <div style="flex:2; min-width:280px;">
      <form method="POST" id="checkoutForm">

        <!-- CUSTOMER DETAILS -->
        <div style="background:white; border-radius:12px; padding:25px; margin-bottom:20px; box-shadow:0 2px 10px rgba(0,0,0,0.08);">
          <h3 style="margin-bottom:20px; color:#1a1a2e;">👤 Your Details</h3>
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="John Doe" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="john@example.com" required>
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" id="phoneInput" placeholder="+250 700 000 000" required>
          </div>
          <div class="form-group">
            <label>Delivery Address</label>
            <textarea name="address" rows="3" placeholder="Kigali, Rwanda..." required
              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:15px;"></textarea>
          </div>
        </div>

        <!-- PAYMENT METHOD -->
        <div style="background:white; border-radius:12px; padding:25px; margin-bottom:20px; box-shadow:0 2px 10px rgba(0,0,0,0.08);">
          <h3 style="margin-bottom:20px; color:#1a1a2e;">💳 Payment Method</h3>

          <div style="display:flex; flex-direction:column; gap:12px;">

            <!-- MTN Mobile Money -->
            <label style="display:flex; align-items:center; gap:15px; padding:15px; border:2px solid #ddd; border-radius:10px; cursor:pointer; transition:all 0.2s;" id="mtn-label">
              <input type="radio" name="payment_method" value="MTN Mobile Money" onchange="showPayment('mtn')" required style="width:18px; height:18px;">
              <div style="background:#FFC300; width:45px; height:45px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:12px; color:#000;">MTN</div>
              <div>
                <p style="font-weight:bold; margin:0;">MTN Mobile Money</p>
                <p style="color:#888; font-size:13px; margin:0;">Pay with MTN MoMo</p>
              </div>
            </label>

            <!-- Airtel Money -->
            <label style="display:flex; align-items:center; gap:15px; padding:15px; border:2px solid #ddd; border-radius:10px; cursor:pointer; transition:all 0.2s;" id="airtel-label">
              <input type="radio" name="payment_method" value="Airtel Money" onchange="showPayment('airtel')" required style="width:18px; height:18px;">
              <div style="background:#FF0000; width:45px; height:45px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:11px; color:#fff;">AIR</div>
              <div>
                <p style="font-weight:bold; margin:0;">Airtel Money</p>
                <p style="color:#888; font-size:13px; margin:0;">Pay with Airtel Money</p>
              </div>
            </label>

            <!-- Cash on Delivery -->
            <label style="display:flex; align-items:center; gap:15px; padding:15px; border:2px solid #ddd; border-radius:10px; cursor:pointer; transition:all 0.2s;" id="cash-label">
              <input type="radio" name="payment_method" value="Cash on Delivery" onchange="showPayment('cash')" required style="width:18px; height:18px;">
              <div style="background:#1D9E75; width:45px; height:45px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:22px;">💵</div>
              <div>
                <p style="font-weight:bold; margin:0;">Cash on Delivery</p>
                <p style="color:#888; font-size:13px; margin:0;">Pay when you receive</p>
              </div>
            </label>

          </div>

          <!-- MTN PAYMENT DETAILS -->
          <div id="mtn-details" style="display:none; margin-top:20px; background:#FFF9E6; border:1px solid #FFC300; border-radius:10px; padding:20px;">
            <h4 style="color:#333; margin-bottom:15px;">📱 MTN Mobile Money Payment</h4>
            <div style="background:#FFC300; color:#000; padding:12px; border-radius:8px; text-align:center; margin-bottom:15px;">
              <p style="font-size:13px; margin:0;">Send payment to:</p>
              <p style="font-size:22px; font-weight:bold; margin:5px 0;">*182*8*1*0788000000#</p>
              <p style="font-size:13px; margin:0;">Amount: <strong>RWF <?= number_format($total) ?></strong></p>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="color:#333;">Enter MTN MoMo Transaction ID</label>
              <input type="text" name="mtn_transaction_id" id="mtn_txn"
                     placeholder="e.g. TXN1234567890"
                     style="border-color:#FFC300;">
            </div>
          </div>

          <!-- AIRTEL PAYMENT DETAILS -->
          <div id="airtel-details" style="display:none; margin-top:20px; background:#FFF0F0; border:1px solid #FF0000; border-radius:10px; padding:20px;">
            <h4 style="color:#333; margin-bottom:15px;">📱 Airtel Money Payment</h4>
            <div style="background:#FF0000; color:#fff; padding:12px; border-radius:8px; text-align:center; margin-bottom:15px;">
              <p style="font-size:13px; margin:0;">Send payment to:</p>
              <p style="font-size:22px; font-weight:bold; margin:5px 0;">*185*1*1*0733000000#</p>
              <p style="font-size:13px; margin:0;">Amount: <strong>RWF <?= number_format($total) ?></strong></p>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="color:#333;">Enter Airtel Money Transaction ID</label>
              <input type="text" name="airtel_transaction_id" id="airtel_txn"
                     placeholder="e.g. AIR1234567890"
                     style="border-color:#FF0000;">
            </div>
          </div>

          <!-- CASH DETAILS -->
          <div id="cash-details" style="display:none; margin-top:20px; background:#F0FFF8; border:1px solid #1D9E75; border-radius:10px; padding:20px;">
            <h4 style="color:#333; margin-bottom:10px;">💵 Cash on Delivery</h4>
            <p style="color:#555; font-size:14px;">Our delivery agent will collect <strong>RWF <?= number_format($total) ?></strong> when your order arrives.</p>
            <p style="color:#1D9E75; font-size:14px; margin-top:8px;">✅ No payment needed now. Pay on delivery!</p>
          </div>

        </div>

        <input type="hidden" name="place_order" value="1">
        <button type="button" onclick="processPayment()" class="btn" style="width:100%; padding:16px; font-size:18px;">
          ✅ Place Order
        </button>
      </form>
    </div>

    <!-- ORDER SUMMARY -->
    <div style="flex:1; min-width:250px;">
      <div style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.08); position:sticky; top:100px;">
        <h3 style="margin-bottom:20px; color:#1a1a2e;">🧾 Order Summary</h3>
        <?php
          foreach ($_SESSION['cart'] as $item):
            $subtotal = $item['price'] * $item['quantity'];
        ?>
        <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:14px;">
          <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
          <span>RWF <?= number_format($subtotal) ?></span>
        </div>
        <?php endforeach; ?>
        <hr style="margin:15px 0;">
        <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:18px;">
          <span>Total</span>
          <span style="color:#e94560;">RWF <?= number_format($total) ?></span>
        </div>
      </div>
      <a href="/cart.php" style="display:block; text-align:center; margin-top:15px; color:#888;">← Back to Cart</a>
    </div>

  </div>
</div>

<!-- PAYMENT PROCESSING MODAL -->
<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
  <div style="background:white; border-radius:16px; padding:40px; text-align:center; max-width:380px; width:90%;">
    <div id="modal-processing">
      <div style="font-size:50px; margin-bottom:15px;">⏳</div>
      <h3 style="color:#1a1a2e; margin-bottom:10px;">Processing Payment...</h3>
      <p style="color:#888;">Please wait while we verify your payment</p>
      <div style="margin-top:20px; background:#f5f5f5; border-radius:8px; height:8px; overflow:hidden;">
        <div id="progressBar" style="background:#e94560; height:100%; width:0%; transition:width 0.1s;"></div>
      </div>
    </div>
    <div id="modal-success" style="display:none;">
      <div style="font-size:60px; margin-bottom:15px;">✅</div>
      <h3 style="color:#1D9E75; margin-bottom:10px;">Payment Confirmed!</h3>
      <p style="color:#888;">Your order has been placed successfully</p>
    </div>
  </div>
</div>

<script>
function showPayment(type) {
  document.getElementById('mtn-details').style.display = 'none';
  document.getElementById('airtel-details').style.display = 'none';
  document.getElementById('cash-details').style.display = 'none';
  document.getElementById(type + '-details').style.display = 'block';

  // Highlight selected
  ['mtn', 'airtel', 'cash'].forEach(t => {
    const label = document.getElementById(t + '-label');
    label.style.borderColor = t === type ? '#e94560' : '#ddd';
    label.style.background = t === type ? '#fff5f7' : 'white';
  });
}

function processPayment() {
  const form = document.getElementById('checkoutForm');
  const payment = document.querySelector('input[name="payment_method"]:checked');

  if (!form.full_name.value || !form.email.value || !form.phone.value || !form.address.value) {
    alert('Please fill in all your details first!');
    return;
  }

  if (!payment) {
    alert('Please select a payment method!');
    return;
  }

  // Show processing modal
  const modal = document.getElementById('paymentModal');
  modal.style.display = 'flex';

  // Animate progress bar
  let progress = 0;
  const bar = document.getElementById('progressBar');
  const interval = setInterval(() => {
    progress += 2;
    bar.style.width = progress + '%';
    if (progress >= 100) {
      clearInterval(interval);
      // Show success
      document.getElementById('modal-processing').style.display = 'none';
      document.getElementById('modal-success').style.display = 'block';
      // Submit form after 1.5 seconds
      setTimeout(() => { form.submit(); }, 1500);
    }
  }, 60);
}
</script>

<?php require 'includes/footer.php'; ?>