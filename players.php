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

if(array_key_exists("order", $_POST)){
    $thisorder = $_POST["order"];
}
else{
    $thisorder = "lname, fname";
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $playersQuery = 'SELECT P.playerid, P.fname, P.mname, P.lname, PO.abbr, P.age FROM players P INNER JOIN playsposition PP ON PP.playerid=P.playerid INNER JOIN positions PO ON PO.posid=PP.posid ORDER BY ' . $thisorder . ', playerid';
    $playersResults = $conn->query($playersQuery);
    $playersResults->setFetchMode(PDO::FETCH_ASSOC);
    $positionsQuery = 'SELECT * FROM positions';
    $positionsResults = $conn->query($positionsQuery);
    $positionsResults->setFetchMode(PDO::FETCH_ASSOC);
    $teamsQuery = 'SELECT * FROM teams ORDER BY city';
    $teamsResults = $conn->query($teamsQuery);
    $teamsResults->setFetchMode(PDO::FETCH_ASSOC);
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
        <a id="backButton" href="./home.php">Home</a>
        <h1 id="mainTitle">Players</h1>
        <div class="row">
            <div class="col50" style="overflow-y:auto;">
                <h4 class="subTitle">Current Players</h4>
                <div class="dataTable">
                    <div class="headerRow">
                        <span class="dataItem" style="flex:5%;"><form method="post"><input type="hidden" value="playerid" name="order"><input class="sortHeader" type="submit" value="ID"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="fname" name="order"><input class="sortHeader" type="submit" value="First"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="mname" name="order"><input class="sortHeader" type="submit" value="Middle"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="lname" name="order"><input class="sortHeader" type="submit" value="Last"></form></span>
                        <span class="dataItem" style="flex:15%;"><form method="post"><input type="hidden" value="abbr" name="order"><input class="sortHeader" type="submit" value="Position"></form></span>
                        <span class="dataItem" style="flex:10%;"><form method="post"><input type="hidden" value="age" name="order"><input class="sortHeader" type="submit" value="Age"></form></span>
                        <span class="dataItem" style="flex:10%;">Delete</span>
                    </div>
                    <?php while ($player = $playersResults->fetch()): ?>
                    <form id="playerForm<?php echo $player['playerid'] ?>" action="/playerPage.php" method="post">
                        <input type="hidden" name="playerid" value="<?php echo $player['playerid'] ?>">
                    </form>
                    <div class="dataRow" style="cursor:pointer;" onclick="document.getElementById('playerForm<?php echo htmlspecialchars($player['playerid']) ?>').submit();">
                        <span class="dataItem" style="flex:5%;"><?php echo htmlspecialchars($player['playerid']) ?></span>
                        <span class="dataItem" style="flex:20%;"><?php echo htmlspecialchars($player['fname']) ?></span>
                        <span class="dataItem" style="flex:20%;"><?php echo htmlspecialchars($player['mname']) ?></span>
                        <span class="dataItem" style="flex:20%;"><?php echo htmlspecialchars($player['lname']) ?></span>
                        <span class="dataItem" style="flex:15%;"><?php echo htmlspecialchars($player['abbr']) ?></span>
                        <span class="dataItem" style="flex:10%;"><?php echo htmlspecialchars($player['age']) ?></span>
                        <span class="dataItem" style="flex:10%;"><?php echo '<form action="/deletePlayer.php" method="post"><input class="deleteButton" type="submit" value="Delete"><input type="hidden" name="playerid" value="' . htmlspecialchars($player['playerid']) . '"></form>'?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col50">
                <h4 class="subTitle">Create a Player</h4>
                <form action="/createPlayer.php" method="post" class="dataForm">
                    <p class="formLabel">First Name</p>
                    <input class="dataInput" type="text" id="fname" minlength="1" name="fname" value="" required>
                    <p class="formLabel">Middle Name</p>
                    <input class="dataInput" type="text" id="mname" name="mname" value="" required>
                    <p class="formLabel">Last Name</p>
                    <input class="dataInput" type="text" id="lname" minlength="1" name="lname" value="" required>
                    <p class="formLabel">Age</p>
                    <input class="dataInput" type="number" min="18" max="60" step="1" id="age" name="age" value="20" required>
                    <p class="formLabel">Position</p>
                    <select class="dataInput" name="position" id="position" required>
                        <?php while($position = $positionsResults->fetch()) : ?>
                            <option value="<?php echo htmlspecialchars($position['posid'])?>"><?php echo htmlspecialchars($position['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                    <p class="formLabel">Team</p>
                    <select class="dataInput" name="team" id="team" required>
                        <?php while($team = $teamsResults->fetch()) : ?>
                            <option value="<?php echo htmlspecialchars($team['teamid'])?>"><?php echo htmlspecialchars($team['city']) . " " . htmlspecialchars($team['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                    <br>
                    <input class="formButton" type="submit" value="Create">
                </form>
            </div>
        </div>
    </body>
    </div>
</div>
</html>