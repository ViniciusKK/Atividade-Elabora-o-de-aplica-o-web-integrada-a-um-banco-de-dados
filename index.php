<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Video Game Database</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the GAMES table exists. */
  VerifyGamesTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the GAMES table. */
  $game_title = htmlentities($_POST['TITLE']);
  $release_year = htmlentities($_POST['RELEASE_YEAR']);
  $platform = htmlentities($_POST['PLATFORM']);
  $rating = htmlentities($_POST['RATING']);

  if (strlen($game_title) || strlen($release_year) || strlen($platform) || strlen($rating)) {
    AddGame($connection, $game_title, $release_year, $platform, $rating);
  }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>TITLE</td>
      <td>RELEASE YEAR</td>
      <td>PLATFORM</td>
      <td>RATING</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="TITLE" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="RELEASE_YEAR" maxlength="4" size="5" />
      </td>
      <td>
        <input type="text" name="PLATFORM" maxlength="30" size="20" />
      </td>
      <td>
        <input type="text" name="RATING" maxlength="5" size="5" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>TITLE</td>
    <td>RELEASE YEAR</td>
    <td>PLATFORM</td>
    <td>RATING</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM GAMES");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>", $query_data[0], "</td>",
       "<td>", $query_data[1], "</td>",
       "<td>", $query_data[2], "</td>",
       "<td>", $query_data[3], "</td>",
       "<td>", $query_data[4], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add a game to the table. */
function AddGame($connection, $title, $releaseYear, $platform, $rating) {
   $t = mysqli_real_escape_string($connection, $title);
   $ry = mysqli_real_escape_string($connection, $releaseYear);
   $p = mysqli_real_escape_string($connection, $platform);
   $r = mysqli_real_escape_string($connection, $rating);

   $query = "INSERT INTO GAMES (TITLE, RELEASE_YEAR, PLATFORM, RATING) VALUES ('$t', '$ry', '$p', '$r');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding game data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyGamesTable($connection, $dbName) {
  if(!TableExists("GAMES", $connection, $dbName))
  {
     $query = "CREATE TABLE GAMES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         TITLE VARCHAR(45),
         RELEASE_YEAR INT,
         PLATFORM VARCHAR(30),
         RATING VARCHAR(5)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
