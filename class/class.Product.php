<?php

/*
  This is Class Of Product.

  Copyright (c) 2023 AbdulbariSH
 */

class Product 
{
    /**
     * Array of Products.
	 *
	 * @var array $ProductsArray
     */
    private $ProductsArray = [];
    private $conn;

    public function __construct($options = []) {
        $this->conn = $options['conn'];
		if(isset($options['available']) && $options['available'] == -1){
			$Products = $this->conn->query("SELECT * FROM web_items");
		}elseif (isset($options['available']) && $options['available'] == 0) {
			$Products = $this->conn->query("SELECT * FROM web_items WHERE available = 0");
		}else{
			$Products = $this->conn->query("SELECT * FROM web_items WHERE available = 1");
		}
		foreach ($Products as $row) {
			$id = intval($row['id']);
			$label = htmlentities($row['label']);
			$image = htmlentities($row['image']);
			$colors = json_decode($row['colors']);
			$price = intval($row['price']);
			$available = intval($row['available']);
			$this->ProductsArray[] = [
				'id'         => $id,
				'label'         => $label,
				'image'         => $image,
				'colors'         => $colors,
				'price'         => $price,
				'available'         => $available,
			];
		}
    }


	public function getItems(){
		return $this->ProductsArray;
	}

	/**
	 * Check if the cart is empty.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty(array_filter($this->ProductsArray));
	}

		/**
	 * Get the total of item in cart.
	 *
	 * @return int
	 */
	public function getTotalItem()
	{
		$total = 0;

		foreach ($this->ProductsArray as $Products) {
			foreach ($Products as $Product) {
				++$total;
			}
		}

		return $total;
	}


}

?>