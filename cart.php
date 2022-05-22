<?php

	use MongoDB\BSON\ObjectId;

	session_start();
	include_once('config/configuration.inc.php');
	header('Content-Type: application/json; charset=utf-8');

	if (!isset($_SESSION['cart'])) {
		$_SESSION['cart'] = array();
	}
	
	$p_id = isset($_GET['p_id']) ? $_GET['p_id'] : null; 
	$act = $_GET['act'];
	$qty = isset($_GET['qty']) ? $_GET['qty'] : null; 
	if ($act == 'view') {
		echo json_encode(['status' => 'success', 'cart' => array_values($_SESSION['cart'])]);
	} elseif ($act=='add' && !empty($p_id))
	{
		if(isset($_SESSION['cart'][$p_id]))
		{
			$_SESSION['cart'][$p_id]['qty']++;
			// var_dump($_SESSION['cart'][$p_id]);
			echo json_encode(['status' => 'success', 'product' => $_SESSION['cart'][$p_id]]);
		}
		else
		{
			// $collection = $db->product;
    		// $record = $collection->find(['_id' => $p_id]);
			$result = $db->product->findOne(['_id' => new ObjectId($p_id)]);

			if (!$result) {
				http_response_code(404);
				die(json_encode(['error' => true, 'status' => 'Error product not found']));
			}

			$_SESSION['cart'][$p_id] = json_decode(json_encode($result->jsonSerialize()), true);
			$_SESSION['cart'][$p_id]['qty'] = 1;

			echo json_encode(['status' => 'success', 'product' => $_SESSION['cart'][$p_id]]);
		}
	} elseif ($act=='clear')  //ยกเลิกการสั่งซื้อ
	{
		unset($_SESSION['cart']);
		echo json_encode(['status' => 'success']);
	} elseif ($act=='update' && !empty($p_id) &&  !empty($qty) )
	{
		if ($qty < 1) {
			unset($_SESSION['cart'][$p_id]);
			die(json_encode(['status' => 'update success (remove)']));
		}
		$_SESSION['cart'][$p_id]['qty'] = $qty;
		echo json_encode(['status' => 'success', 'product' => $_SESSION['cart'][$p_id]]);
	} elseif ($act=='remove' && !empty($p_id))
	{
		unset($_SESSION['cart'][$p_id]);
		echo json_encode(['status' => 'remove success']);
	}
?>
