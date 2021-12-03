<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conf = include('config.php');

$username = $conf['username'];
$password = $conf['password'];
$host = $conf['host'];
$dbname = $conf['dbname'];
$port = $conf['port'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $playersQuery = 'SELECT city, name FROM teams';
    $playersResults = $conn->query($playersQuery);
    $playersResults->setFetchMode(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>

<style>
<?php include './styles.css'; ?>
</style>

<!DOCTYPE html>
<html style="font-family:">
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
        <title>FootballSimXYZ</title>
    </head>
    <body style=""> 
    <div style="text-align:center; margin: 1rem 1rem 1rem 1rem;">
        <h1 id="mainTitle">Players</h1>
        <div class="menu">
            <a class="menuButton" href="./players.php">Players</a>
            <a class="menuButton" href="./teams.php">Teams</a>
            <a class="menuButton" href="./games.php">Games</a>
            <a class="menuButton" href="./stats.php">Stats</a>
        </div>
        <?php while ($row = $q->fetch()): ?>
    </body>
    </div>
</div>
</html>