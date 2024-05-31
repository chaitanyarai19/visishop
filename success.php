<?php
include 'config.php';
session_start();

$title = 'Payment Unsuccessful';
$response = '<div class="panel-body">
                <i class="fa fa-times-circle text-danger"></i>
                <h3>Payment Unsuccessful</h3>
                <a href="'.$hostname.'" class="btn btn-md btn-primary">Continue Shopping</a>
             </div>';

// Debugging: Log the session and GET parameters
//error_log('Session TID: ' . $_SESSION['TID']);
error_log('GET payment_request_id: ' . ($_GET['payment_request_id'] ?? 'Not set'));
error_log('GET payment_status: ' . ($_GET['payment_status'] ?? 'Not set'));

// Check if the required GET parameters are set
if (isset($_GET['payment_request_id']) && isset($_GET['payment_status'])) {
    $payment_request_id = $_GET['payment_request_id'];
    $payment_status = $_GET['payment_status'];

    // Debugging: Check if the payment request ID matches the session TID
    //error_log('Comparing payment_request_id with session TID: ' . ($payment_request_id == $_SESSION['TID']));
    
    if ($payment_request_id && $payment_status == 'Credit') {
        $title = 'Payment Successful';
        $response = '<div class="panel-body">
                        <i class="fa fa-check-circle text-success"></i>
                        <h3>Order Confirmed</h3><br>
						<h4>Your Order Id is '.$payment_request_id.'</h4><br>
                        <p>Your Product Will be Delivered within 4 to 7 days.</p>
                        <a href="'.$hostname.'" class="btn btn-md btn-primary">Continue Shopping</a>
                     </div>';

        // Reduce purchased quantity from products
        $db = new Database();
        $db->select('order_products', 'product_id, product_qty', null, "pay_req_id = '{$payment_request_id}'", null, null);
        $result = $db->getResult();

        if ($result) {
            $products = array_filter(explode(',', $result[0]['product_id']));
            $qty = array_filter(explode(',', $result[0]['product_qty']));
            for ($i = 0; $i < count($products); $i++) {
                $db->sql("UPDATE products SET qty = qty - '{$qty[$i]}' WHERE product_id = '{$products[$i]}'");
            }
            $res = $db->getResult();
        }

        // Remove cart items
        if (isset($_COOKIE['user_cart'])) {
            setcookie('cart_count', '', time() - 180, '/', '', '', true);
            setcookie('user_cart', '', time() - 180, '/', '', '', true);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include('header.php'); ?>

    <div class="payment-response">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="panel panel-default">
                        <?php echo $response; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('footer.php'); ?>
<!-- Add audio element for clap sound -->
<audio id="clapSound" src="voice/clap.mp3"></audio>

<script>
    const synth = window.speechSynthesis;

    function setFemaleVoice(utterance) {
        const voices = synth.getVoices();
        const femaleVoice = voices.find(voice =>
            voice.name.includes("Google US English") ||
            voice.name.includes("Microsoft Zira") ||
            voice.name.includes("Microsoft Catherine") ||
            voice.name.includes("Google UK English Female")
        );

        if (femaleVoice) {
            utterance.voice = femaleVoice;
        }
    }

    const clapSound = document.getElementById('clapSound');
    const utterance = new SpeechSynthesisUtterance("Congratulations! Your order is confirmed with us. Press Enter to go to the home page.");

    clapSound.play();

    clapSound.onended = () => {
        if (synth.getVoices().length !== 0) {
            setFemaleVoice(utterance);
            synth.speak(utterance);
        } else {
            synth.onvoiceschanged = () => {
                setFemaleVoice(utterance);
                synth.speak(utterance);
            };
        }
    };

    window.addEventListener('keydown', (event) => {
        if (event.key === "Enter") {
            window.location.href = 'http://localhost/supermart/';
        }
    });
</script>

</body>
</html>
