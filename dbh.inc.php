<?php

$conn = pg_connect("host=localhost port=5432 dbname=PetCare user=postgres password=3154");

if (!$conn) {
	die('Connection failed.??');
}
?>	