<?php
	include_once('header.php');
?>

	<section class="main-content">
		<h2>Traffic Report System</h2>
		<br>
		<p>
<?php
	if (!empty($_SESSION['officer'])) {
?>		
		
		You are logged in as a Police Officer.
<?php
	}
	else if (!empty($_SESSION['admin'])) {
?>	
		You are logged in as an Administrator.
<?php	
	}
	else {
?>
		Please log in as a Police Officer or an Administrator.
<?php	
	}
?>
		<br><br>
		Use the menu at the top to navigate the system.
		</p>
	</section>
	
<?php
	include_once('footer.php');
?>