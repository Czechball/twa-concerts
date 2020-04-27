<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "concerts.class.php";
require_once "credentials.php";
global $concerts;
$concerts = new concert($host, $port, $dbname, $user, $pass);
$id="";
$date="";
$time="";
$where="";
$name="";
$note="";
?>
<!DOCTYPE html>
<html lang="en-US">

<head>

	<title>Concert list</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="favicon.png"/>
	<link rel="stylesheet" href="style.css">
	<link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
	<body>
		<h1>Concerts</h1>
		<?php
		if (isset($_POST["add"]))
		{
			$id    = $_POST["id"];
			$date  = $_POST["date"];
			$time  = $_POST["time"];
			$where = $_POST["where"];
			$name  = $_POST["name"];
			$note  = $_POST["note"];

			if(empty($date) || empty($time) || empty($where))
			{			
				print '<div class="box style1 error">Please fill out all text fields.</div>';	
			} 
			else
			{
				$concerts->addConcert($id,$date,$time,$where,$name,$note);
				print '<div class="box style1 success">Done.</div>';
			};
		}

		if (isset($_POST["delete"]))
		{
			$id = $_POST["id"];
			$concerts->deleteConcert($id);
			echo '<div class="box style1 success">Concert deleted.</div>';
		}

		if (isset($_POST["edit"]))
		{
			$id = $_POST["id"];
			$concert=$concerts->getConcert($id);
			$date=$concert->datum;
			$time=$concert->cas;
			$where=$concert->kde;
			$name=$concert->co;
			$note=$concert->poznamka;
		}

		if (isset($_POST["login"]))
		{
			$user = $_POST["user"];
			$password = $_POST["password"];
    	if ($concerts->verifyAdmin($user, $password))
    	{
        $_SESSION['username'] = $user;
        print '<div class="box style1 success">You were logged in succesfully.</div>';
    	}
    	else
    	{
        unset($_SESSION['username']);
        print '<div class="box style1 error">Incorrect credentials.</div>';
    	}
		}

		if (isset($_POST["logout"]))
		{
        unset($_SESSION['username']);
        print '<div class="box style1 success">You were logged out succesfully.</div>';
    	}
		?>
				<div class="box style2">
		<h2>Moderation</h2>
		<?php
		if (!isset($_SESSION['username']))
		{

		?>
		<p><i>Not logged in</i></p>
		<form method="POST">
			<table>
				<tr>
					<td align=right valign="top">Username</td>
					<td align=left><input required type="text" name="user"></td>
				</tr>
				<tr>
					<td align=right valign="top">Password</td>
					<td align=left><input required type="password" name="password"></td>
				</tr>
				<tr>
					<td align=right valign="top"></td>
					<td align=left><input type="submit" name="login" value="Login"></td>
				</tr>
			</table>
		</form>
		<?php
		}
		else
		{
			?>
			<p><i>Logged in</i></p>
			<form method="POST">
			<table>
				<tr>
					<td align=right valign="top"></td>
					<td align=left><input type="submit" name="logout" value="Logout"></td>
				</tr>
			</table>
		</form>
					<div class="box style2">
		<?php 
			if ($id == "") {
		?>
		<h2>Add Event</h2>
		<?php
			}
			else
			{
		?>
		<h2>Edit Event</h2>
		<?php
			};
		?>
		<form method="POST">
			<table>
				<tr>
					<td align=right valign="top">Date</td>
					<td align=left><input value="<?php echo $date; ?>" required type="Date" name="date"></td>
				</tr>
				<tr>
					<td align=right valign="top">Time</td>
					<td align=left><input value="<?php echo $time; ?>" required type="Time" name="time"></td>
				</tr>
				<tr>
					<td align=right valign="top">Where</td>
					<td align=left><input value="<?php echo htmlspecialchars($where); ?>" required type="text" name="where"></td>
				</tr>
				<tr>
					<td align=right valign="top">Name</td>
					<td align=left><input value="<?php echo htmlspecialchars($name); ?>" required type="text" name="name"></td>
				</tr>				
				<tr>
					<td align=right valign="top">Note</td>
					<td align=left><textarea required name="note" cols="50" rows="5"><?php echo htmlspecialchars($note); ?></textarea></td>
				</tr>
				<tr>
					<td align=right valign="top"></td>
					<td align=left>
						<input type="submit" name="add" value="Submit">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
						<input type="submit" name="" value="Reset">
					</td>
				</tr>
			</table>
		</form>
		</div>
		<?php
		}
		?>
		</div>

		<div class="box style2">
			<?php
				if (isset($_POST["history"]))
				{
					if (isset($_POST["year"])) {
						$year=$_POST["year"];
					}
					else
					{
						$year=date("Y");
					}
				?>
				<h2>Historical Concerts</h2>
				<form method="POST">
					<button name="actual">Current</button>
				</form>
				<?php

				if ($concerts->getNumberConcerts($year-1)!=0) {
					
				?>
				<form method="POST">
					<button name="history"><?php echo $year-1; ?></button>
					<input type="hidden" name="year" value="<?php echo $year-1; ?>">
				</form>
				<?php
					};
				?>
				<?php echo $year; ?>
				<?php
				if ($concerts->getNumberConcerts($year+1)!=0) {
					
				?>
				<form method="POST">
					<button name="history"><?php echo $year+1; ?></button>
					<input type="hidden" name="year" value="<?php echo $year+1; ?>">
				</form>
				<?php
					};
				?>
				<?php
				$temp=($concerts->getHistoricalConcerts($year));
				}
				else
				{

			?>
  <h2>Actual Concerts</h2>
  <form method="POST">
  	<button name="history">History</button>
  </form>
  <?php
  	$temp=$concerts->getActualConcerts();
	};
    foreach ($temp as $row)
    {
  ?>
    <div class="box message">
      <?php if (isset($_SESSION['username'])) { ?>
      <form method="POST">
        <input type="hidden" name="id" value="<?php echo $row->ID; ?>">
        <button <?php echo " onclick=\"return confirm('Confirm deletion?')\""; ?> class="delete-button" name="delete"></button>
      </form>
      <form method="POST">
        <input type="hidden" name="id" value="<?php echo $row->ID; ?>">
        <button class="button" name="edit" value="Edit">Edit</button>
      </form>
      <?php } ?>
      <h3><?php print($row->co) ?></h3>
      <p>
	      <?php print($row->datum) ?><br />
	      <?php print($row->cas) ?><br />
	      <?php print($row->kde) ?>
      </p>
      <p>
      	<?php print($row->poznamka) ?>
      </p>
    </div>
<?php } ?>
	</body>
