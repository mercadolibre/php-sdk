<?php

require '../src/meli.php';

require 'config_examples.php';


// Login or logout url will be needed depending on current user state.
if ($userId) {

    $user = $meli -> getWithAccessToken('/users/me');

    $recentOrders = $meli -> getWithAccessToken('/orders/search/recent', array('seller' => $user['json']['id']));
}
?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>MeliPHP SDK - Example orders</title>
	</head>
	<body>
		<h1>MeliPHP SDK - Example orders</h1>

		<?php if ($userId):
		?>
		<p>
			Hello <?php echo $user['json']['first_name']
			?>
		</p>
		<a href="<?php echo $meli -> getLogoutUrl();?>">Logout</a>
		<h2> Recent orders </h2>

		<ul>
			<?php foreach ($recentOrders['json']['results'] as $order):
			?>
			<li>
				<p>
					Dados da Order
				</p>
				<table>
					<thead>
						<tr>
							<th>id</th>
							<th>status</th>
							<th>status_detail</th>
							<th>date_created</th>
							<th>date_closed</th>
							<th>total_amount</th>
							<th>currency_id</th>						
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?=$order['id']; ?></td>
							<td><?=$order['status']; ?></td>
							<td><?=$order['status_detail']; ?></td>
							<td><?=$order['date_created']; ?></td>
							<td><?=$order['date_closed']; ?></td>
							<td><?=$order['total_amount']; ?></td>
							<td><?=$order['currency_id']; ?></td>
						</tr>
					</tbody>
				</table>
			</li>
			<?php endforeach;?>
		</ul>
		<?php else:?>
		<div>
			<p>
				Login using OAuth 2.0 handled by the PHP SDK:
			</p>
			<a href="<?php echo $meli -> getLoginUrl(array('scope' => array('questions_write')));?>">Login with MercadoLibre</a>
		</div>
		<?php endif?>
	</body>
</html>