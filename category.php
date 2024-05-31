<?php
include 'config.php';

session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$cat = $db->escapeString($_GET['cat']);

$db->select('sub_categories','sub_cat_title',null,"sub_cat_id = '{$cat}'",null,null);
$result = $db->getResult();
if(!empty($result)){ 
    $title = $result[0]['sub_cat_title'].' : Buy '.$result[0]['sub_cat_title'].' at Best Price'; 

}else{ 
    $title = "Result Not Found";
}
$page_head = $result[0]['sub_cat_title'];

// Include header
include 'header.php'; 
?>

<div class="product-section content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="section-head"><?php echo $page_head; ?></h2>
            </div>
        </div>
        <?php if(!empty($result)){ ?>
        <div class="row">
            <div class="col-md-3 left-sidebar">
                <h3>Related Brands</h3>
                <?php
                $db->select('sub_categories','cat_parent',null,"sub_cat_id = '{$cat}'",null,null);
                $cat_name = $db->getResult();

                $db->select('brands','*',null,"brand_cat = '{$cat_name[0]["cat_parent"]}'",null,null);
                $result2 = $db->getResult();
                if(count($result2) > 0){ ?>
                    <ul>
                        <?php foreach($result2 as $row2){ ?>
                            <li><a href="brands.php?brand=<?php echo $row2['brand_id']; ?>"><?php echo $row2['brand_title']; ?></a></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
            <div class="col-md-9">
                <?php
                $limit = 8;
                $db->select('products','*',null,"product_sub_cat = '{$cat}' AND product_status = 1 AND qty > 0",null,null);
                $result3 = $db->getResult();
                if(count($result3) > 0){
                    foreach($result3 as $index => $row3){ ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="product-grid">
                                <div class="product-image">
                                    <a class="image" href="single_product.php?pid=<?php echo $row3['product_id']; ?>">
                                        <img class="pic-1" src="product-images/<?php echo $row3['featured_image']; ?>">
                                    </a>
                                    <div class="product-button-group">
                                        <!-- <a href="single_product.php?pid=<?php //echo $row3['product_id']; ?>" ><i class="fa fa-eye"></i></a> -->
                                        <a href="#" class="add-to-cart" data-id="<?php echo $row3['product_id']; ?>" data-index="<?php echo $index + 1; ?>"><i class="fa fa-shopping-cart"></i></a>
                                        <!-- <a href="#" class="add-to-wishlist" data-id="<?php //echo $row3['product_id']; ?>"><i class="fa fa-heart"></i></a> -->
                                    </div>
                                </div>
                                <div class="product-content">
                                    <h3 class="title">
                                        <a href="single_product.php?pid=<?php echo $row3['product_id']; ?>" data-title="<?php echo $row3['product_title']; ?>" data-price="<?php echo $row3['product_price']; ?>" data-productid="<?php echo $row3['product_id']; ?>">
                                            <?php echo substr($row3['product_title'],0,30).'...'; ?>
                                        </a>
                                    </h3>
                                    <div class="price"><?php echo $cur_format; ?> <?php echo $row3['product_price']; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php    }
                }else{ ?>
                    <div class="empty-result">Result Empty</div>
            <?php } ?>
            <div class="col-md-12 pagination-outer">
                    <?php
                        echo $db->pagination('products',null,"product_sub_cat = '{$cat}' AND product_status = 1 AND qty > 0",$limit);
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Voice navigation script -->
<script>
// Function to add product to cart
function addToCart(productId) {
    $.ajax({
        url: 'actions.php',
        method: 'POST',
        data: { addCart: productId },
        success: function (data) {
            if (data === 'true') {
                speak('Product added to cart successfully.');
            } else {
                speak('Failed to add product to cart.');
            }
        },
        error: function () {
            speak('Error occurred while adding product to cart.');
        }
    });
}

// Check if speech synthesis is supported
if ('speechSynthesis' in window) {
    // Wait for the document to be fully loaded
    document.addEventListener('DOMContentLoaded', function () {
        // Function to speak the given text
        function speak(text) {
            const synth = window.speechSynthesis;
            const utterance = new SpeechSynthesisUtterance(text);
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
            synth.speak(utterance);
        }

        // Speak the category name when the page loads
        speak("<?php echo $page_head; ?>. Press Shift to listen to the products. and press Enter to go to cart.");

        // Event listener for Shift key press to read products
        window.addEventListener('keydown', function (event) {
            if (event.key === "Shift") {
                const products = document.querySelectorAll('.product-content .title a');
                products.forEach(function (product, index) {
                    const title = product.getAttribute('data-title');
                    const price = product.getAttribute('data-price');
                    speak(`Press ${index + 1} to add ${title} to cart. Price: <?php echo $cur_format; ?> ${price}.`);
                });
            } else if (!isNaN(event.key) && event.key > 0) {
                const index = event.key - 1;
                const products = document.querySelectorAll('.product-content .title a');
                if (index < products.length) {
                    const productLink = products[index];
                    const productId = productLink.getAttribute('data-productid'); // Fetch productId
                    const productName = productLink.getAttribute('data-title');
                    const productPrice = productLink.getAttribute('data-price');
                    speak(`Adding ${productName} to cart. Price: <?php echo $cur_format; ?> ${productPrice}.`);
                    addToCart(productId);
                } else {
                    speak('Invalid product number.');
                }
            }
        });
    });
} else {
    // Speech synthesis not supported
    console.error('Speech synthesis not supported');
}



</script>

<?php include 'footer.php'; ?>
<script src="voice/voice-navigation.js"></script>
<script src="js/actions.js"></script>
