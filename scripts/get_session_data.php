<?php
	session_name('MISSILESv01');
	session_start();

	$data = [
		'google_id' => $_SESSION['google_id'] ?? null,
		'session_id' => session_id()
	];

	echo json_encode($data);
