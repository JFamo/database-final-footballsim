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

if(array_key_exists("teamid", $_POST)){
    $thisteam = $_POST["teamid"];
}
else{
    $thisteam = 1;
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $gamesQuery = "SELECT * from games";
	$gamesStatement = $conn->prepare($gamesQuery);
    $gamesStatement->execute(array($thisteam));
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
        <h1 id="mainTitle">Games</h1>
        <div class="row">
            <div class="col50" style="overflow-y:auto;">
                <form action="games.php" method="post">
                    <select onchange="this.form.submit()" class="dataInput" name="teamid" id="teamid" required>
                        <?php while($team = $teamsResults->fetch()) : ?>
                            <option value="<?php 
                            echo htmlspecialchars($team['teamid']) . '"';
                            if($thisteam == $team["teamid"]){
                                echo "selected";
                            }
                            ?>><?php echo htmlspecialchars($team['city']) . " " . htmlspecialchars($team['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                </form>
                <form action="games.php" method="post">
                    <select onchange="this.form.submit()" class="dataInput" name="year" id="year" required>
                        <?php while($team = $teamsResults->fetch()) : ?>
                            <option value="<?php 
                            echo htmlspecialchars($team['teamid']) . '"';
                            if($thisteam == $team["teamid"]){
                                echo "selected";
                            }
                            ?>><?php echo htmlspecialchars($team['city']) . " " . htmlspecialchars($team['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                </form>
                <h4 class="subTitle">Active Roster</h4>
                <div class="dataTable">
                    <div class="headerRow">
                        <span class="dataItem" style="flex:5%;">PlayerID</span>
                        <span class="dataItem" style="flex:20%;">First</span>
                        <span class="dataItem" style="flex:20%;">Middle</span>
                        <span class="dataItem" style="flex:20%;">Last</span>
                        <span class="dataItem" style="flex:15%;">Position</span>
                        <span class="dataItem" style="flex:10%;">Age</span>
                        <span class="dataItem" style="flex:10%;">Release</span>
                    </div>
                    <?php while ($player = $rosterStatement->fetch()): ?>
                    <div class="dataRow">
                        <span class="dataItem" style="flex:5%;"><?php echo htmlspecialchars($player['playerid']) ?></span>
                        <span class="dataItem" style="flex:20%;"><?php echo htmlspecialchars($player['fname']) ?></span>
                        <span class="dataItem" style="flex:20%;"><?php echo htmlspecialchars($player['mname']) ?></span>
                        <span class="dataItem" style="flex:20%;"><?php echo htmlspecialchars($player['lname']) ?></span>
                        <span class="dataItem" style="flex:15%;"><?php echo htmlspecialchars($player['abbr']) ?></span>
                        <span class="dataItem" style="flex:10%;"><?php echo htmlspecialchars($player['age']) ?></span>
                        <span class="dataItem" style="flex:10%;"><?php echo '<form action="/releasePlayer.php" method="post"><input class="deleteButton" type="submit" value="Release"><input type="hidden" name="teamid" value="' . $thisteam . '"><input type="hidden" name="playerid" value="' . htmlspecialchars($player['playerid']) . '"></form>'?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col50">
                <h4 class="subTitle">Generate Schedule</h4>
                <form action="generateSchedule.php" method="post" class="dataForm">
                    <p class="formLabel">Year</p>
                    <input class="dataInput" type="number" id="year" minlength="4" name="year" value="" required>
                    <br>
                    <input class="formButton" type="submit" value="Generate">
                </form>
            </div>
        </div>
    </body>
    </div>
</div>
</html>