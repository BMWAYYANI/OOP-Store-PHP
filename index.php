<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'class/class.Database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'class/class.Cart.php';
require_once 'class/class.Product.php';

$cart = new Cart([
    // Maximum item can added to cart, 0 = Unlimited
    'cartMaxItem' => 0,

    // Maximum quantity of a item can be added to cart, 0 = Unlimited
    'itemMaxQuantity' => 5,

    // Do not use cookie, cart items will gone after browser closed
    'useCookie' => true,
]);
print_r($cart->read());
$Products = new Product([
    // Database Connction 
    'conn' => $db,

    // Items type, 0 = Unavailable , -1 For All
    'available' => 1,

]);
$products = $Products->GetItems();

print_r($_SESSION);
/* if(isset()) */
// Empty the cart
if (isset($_POST['empty'])) {
    $cart->clear();
}

// Add item
if (isset($_POST['AddToCart'])) {
    foreach ($products as $product) {
        if ($_POST['AddToCart'] == $product['id']) {
            break;
        }
    }

    $cart->add($product['id'], $_POST['qty'], [
        'price' => $product['price'],
        'color' => (isset($_POST['color'])) ? $_POST['color'] : '',
    ]);
    $_SESSION['success'] = "Item Added";
    $_SESSION['success_icon'] = "success";
    $_SESSION['success_title'] = "Success";   
    
}

// Update item
if (isset($_POST['update'])) {
    foreach ($products as $product) {
        if ($_POST['id'] == $product['id']) {
            break;
        }
    }

    $cart->update($product['id'], $_POST['qty'], [
        'price' => $product['price'],
        'color' => (isset($_POST['color'])) ? $_POST['color'] : '',
    ]);
    $_SESSION['success'] = "Item Updated";
    $_SESSION['success_icon'] = "success";
    $_SESSION['success_title'] = "Success";  
}

// Remove item
if (isset($_POST['remove'])) {
    foreach ($products as $product) {
        if ($_POST['id'] == $product['id']) {
            break;
        }
    }

    $cart->remove($product['id'], [
        'price' => $product['price'],
        'color' => (isset($_POST['color'])) ? $_POST['color'] : '',
    ]);
    $_SESSION['success'] = "Item Deleted";
    $_SESSION['success_icon'] = "success";
    $_SESSION['success_title'] = "Success";  
}


$page = (isset($_REQUEST['page'])) ? $_GET['page'] : 'index';
if ($page == 'checkout') {
    $output = '
    <div class="container">
        <h1>Checkout</h1>

        <div class="row">
            <div class="col-md-14">
                <div class="table-responsive">
                    <pre>' . print_r($cart->getItems()) . '</pre>
                </div>
            </div>
        </div>
    </div>
    
    ';
} elseif ($page == 'cart') {
    $cartContents = '
	<div class="alert alert-warning">
		<i class="fa fa-info-circle"></i> There are no items in the cart.
	</div>';

    

    if (!$cart->isEmpty()) {
        $allItems = $cart->getItems();

        $cartContents = '
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="col-md-7">Product</th>
					<th class="col-md-3 text-center">Quantity</th>
					<th class="col-md-2 text-right">Price</th>
				</tr>
			</thead>
			<tbody>';

        foreach ($allItems as $id => $items) {
            foreach ($items as $item) {
                foreach ($products as $product) {
                    if ($id == $product['id']) {
                        break;
                    }
                }

                $cartContents .= '
				<tr>
                <form action="" method="POST">
					<td>' . $product['label'] . ((isset($item['attributes']['color'])) ? ('<p><strong>Color: </strong>' . $item['attributes']['color'] . '</p>') : '') . '</td>
					<td class="text-center">
                    <div class="form-group">
                    <input name="qty" type="number" value="' . $item['quantity'] . '" class="form-control quantity pull-left" style="width:100px">
                    <div class="pull-right">
                    <input name="id" type="hidden" value="' . $id . '">
                    <input name="color" type="hidden" value="'.$item['attributes']['color'].'">
                    <button type="submit" name="update" class="btn btn-default btn-update" data-id="' . $id . '" data-color="' . ((isset($item['attributes']['color'])) ? $item['attributes']['color'] : '') . '">
                    <i class="fa fa-refresh"></i> Update</button>
                    <button type="submit" name="remove" class="btn btn-danger btn-remove" data-id="' . $id . '" data-color="' . ((isset($item['attributes']['color'])) ? $item['attributes']['color'] : '') . '">
                    <i class="fa fa-trash"></i></button></div></div></td>
					<td class="text-right">$' . $item['attributes']['price'] . '</td>
                    </form>
				</tr>';
            }
        }

        $cartContents .= '
			</tbody>
		</table>

		<div class="text-right">
			<h3>Total:<br />$' . number_format($cart->getAttributeTotal('price'), 2, '.', ',') . '</h3>
		</div>

		<p>
			<div class="pull-left">
				<button class="btn btn-danger btn-empty-cart">Empty Cart</button>
			</div>
			<div class="pull-right text-right">
				<a href="?page=home" class="btn btn-default">Continue Shopping</a>
				<a href="?page=checkout" class="btn btn-danger">Checkout</a>
			</div>
		</p>';
    }

    $output = '
    <div class="container">
    <h1>Shopping Cart</h1>

    <div class="row">
        <div class="col-md-12">
             <div class="table-responsive">
                ' . $cartContents . '
             </div>
        </div>
    </div>
</div>
    ';
} else {

    $output = '
    <div class="container">
            <h1>Products</h1>
            <div class="row"> ';
    foreach ($products as $product) {
        $output .= '
                    
					<div class="col-md-3">
                    <form action="" method="POST">
						<h3>' . $product["label"] . '</h3>

						<div>
							<div class="pull-left">
								<img src="' . $product["image"] . '" border="0" width="200" height="250" title="' . $product["label"] . '" />
							</div>
							<div class="pull-right">
								<h4>$' . $product["price"] . '</h4>
									<input type="hidden" value="' . $product["id"] . '" class="product-id" />';

        if ($product["colors"]) {
            $output .= '
										<div class="form-group">
											<label>Color:</label>
											<select name="color" class="form-control color">';

            foreach ($product["colors"] as $key => $value) {
                $output .= '
												<option value="' . $value . '"> ' . $value . '</option>';
            }

            $output .= '
											</select>
										</div>';
        }

        $output .= '

									<div class="form-group">
										<label>Quantity:</label>
										<input type="number" value="1" name="qty" class="form-control quantity" />
									</div>
									<div class="form-group">
										<button type="submit" name="AddToCart" class="btn btn-info"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
									</div>
							</div>
							<div class="clearfix"></div>
						</div>
                        </form>
					</div>
                   
                    ';
    }
    ?>
    </div>
    </div>

<?php } ?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cart - A Simple PHP Cart</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
        padding-bottom: 20px;
        }

        .navbar {
        margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Simple Shop</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample02"
            aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample02">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="?page=home">Home </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=cart"><i class="fa fa-shopping-cart"></i> Cart (<?php echo $cart->getTotalItem(); ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=cheakout">Checkout</a>
                </li>
            </ul>
        </div>
    </nav>
    <?php echo $output ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>

        <?php
if(isset($_SESSION['Error']) && $_SESSION['Error'] !='')
{
  ?>
    <script>
        Swal.fire({
  icon: '<?php echo $_SESSION['Error_icon']; ?>',
  title: '<?php echo $_SESSION['Error_title']; ?>',
  text: '<?php echo $_SESSION['Error']; ?>',
});
  </script>
  <?php
  unset($_SESSION['Error']);
  unset($_SESSION['Error_icon']);
  unset($_SESSION['Error_title']);
}
?>
    <?php
if(isset($_SESSION['success']) && $_SESSION['success'] !='')
{
  ?>
    <script>
        Swal.fire({
  icon: '<?php echo $_SESSION['success_icon']; ?>',
  title: '<?php echo $_SESSION['success_title']; ?>',
  text: '<?php echo $_SESSION['success']; ?>',
});
  </script>
  <?php
  unset($_SESSION['success']);
  unset($_SESSION['success_icon']);
  unset($_SESSION['success_title']);
}
?>

</body>

</html>