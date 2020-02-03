<?php
//$year = date("Y");
$year = $_GET["season"];

if (!is_numeric($year))
{
	$year = date("Y");
}

?>
<!DOCTYPE html>
<html lang="en-US">

	<head>
		<title>Shows history</title>
	</head>
	<body>
		<h1>Show me da history</h1>
		<h2>
			<?php
				echo '<a href="?season=';
				echo $year -1;
				echo '">';
				echo $year - 1;
				echo '</a>';
				echo " ";
				echo $year;
				echo " ";
				echo $year - 1;
			?>
		</h2>

	</body>
</html>