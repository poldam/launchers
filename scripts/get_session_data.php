<?php
	session_name('MISSILESv01');
	session_start();

	$data = [
		'google_id' => $_SESSION['google_id'] ?? null,
		'temp_id' => bin2hex(random_bytes(16))
	];

	echo json_encode($data);
