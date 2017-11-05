<?php

$conn = pg_connect("host=localhost port=5432 dbname=PetCare user=postgres password=");

if (!$conn) {
	die('Connection failed.??');
}
?>	