<?php

session_start();

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
    $_SESSION["teamsteamid"] = $_POST["teamid"];
}
if(array_key_exists("teamsteamid", $_SESSION)){
    $thisteam = $_SESSION["teamsteamid"];
}
else{
    $thisteam = 1;
}
if(array_key_exists("order", $_POST)){
    $rosterorder = $_POST["order"];
}
else{
    $rosterorder = "PO.posid";
}
if(array_key_exists("order2", $_POST)){
    $faorder = $_POST["order2"];
}
else{
    $faorder = "PO.posid";
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $rosterQuery = "SELECT P.playerid, P.fname, P.mname, P.lname, PO.abbr, P.age, T.teamid FROM players P INNER JOIN playsposition PP ON PP.playerid=P.playerid INNER JOIN positions PO ON PO.posid=PP.posid INNER JOIN activeroster AR ON AR.playerid=P.playerid INNER JOIN teams T ON T.teamid=AR.teamid WHERE T.teamid=? ORDER BY " . $rosterorder . ", P.playerid";
	$rosterStatement = $conn->prepare($rosterQuery);
    $rosterStatement->execute(array($thisteam));
    $faQuery = 'SELECT P.playerid, P.fname, P.mname, P.lname, P.age, PO.abbr FROM players P INNER JOIN playsposition PP ON PP.playerid=P.playerid INNER JOIN positions PO ON PO.posid=PP.posid WHERE P.playerid NOT IN (SELECT AR.playerid FROM activeroster AR) ORDER BY ' . $faorder . ', P.playerid';
    $faResults = $conn->query($faQuery);
    $faResults->setFetchMode(PDO::FETCH_ASSOC);
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
        <h1 id="mainTitle">Teams</h1>
        <div class="row">
            <div class="col50" style="overflow-y:auto;">
                <form action="teams.php" method="post">
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
                <h4 class="subTitle">Rebrand</h4>
                <form action="updateTeam.php" method="post" class="dataForm">
                    <input type="hidden" name="teamid" value="<?php echo $thisteam ?>">
                    <div class="row">
                        <div class="col33">
                            <p class="formLabel">New City</p>
                            <input class="dataInput" type="text" id="city" name="city" value="" required>
                        </div>
                        <div class="col33">
                            <p class="formLabel">New Name</p>
                            <input class="dataInput" type="text" id="name" minlength="1" name="name" value="" required>
                        </div>
                        <div class="col33">
                            <input class="formButton" type="submit" value="Rebrand">
                        </div>
                    </div>
                </form>
                <h4 class="subTitle">Active Roster</h4>
                <div class="dataTable">
                    <div class="headerRow">
                        <span class="dataItem" style="flex:5%;"><form method="post"><input type="hidden" value="playerid" name="order"><input class="sortHeader" type="submit" value="ID"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="fname" name="order"><input class="sortHeader" type="submit" value="First"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="mname" name="order"><input class="sortHeader" type="submit" value="Middle"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="lname" name="order"><input class="sortHeader" type="submit" value="Last"></form></span>
                        <span class="dataItem" style="flex:15%;"><form method="post"><input type="hidden" value="PO.posid" name="order"><input class="sortHeader" type="submit" value="Position"></form></span>
                        <span class="dataItem" style="flex:10%;"><form method="post"><input type="hidden" value="age" name="order"><input class="sortHeader" type="submit" value="Age"></form></span>
                        <span class="dataItem" style="flex:10%;">Release</span>
                    </div>
                    <?php while ($player = $rosterStatement->fetch()): ?>
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
                        <span class="dataItem" style="flex:10%;"><?php echo '<form action="/releasePlayer.php" method="post"><input class="deleteButton" type="submit" value="Release"><input type="hidden" name="teamid" value="' . $thisteam . '"><input type="hidden" name="playerid" value="' . htmlspecialchars($player['playerid']) . '"></form>'?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col50">
                <h4 class="subTitle">Free Agents</h4>
                <div class="dataTable">
                    <div class="headerRow">
                        <span class="dataItem" style="flex:5%;"><form method="post"><input type="hidden" value="playerid" name="order2"><input class="sortHeader" type="submit" value="ID"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="fname" name="order2"><input class="sortHeader" type="submit" value="First"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="mname" name="order2"><input class="sortHeader" type="submit" value="Middle"></form></span>
                        <span class="dataItem" style="flex:20%;"><form method="post"><input type="hidden" value="lname" name="order2"><input class="sortHeader" type="submit" value="Last"></form></span>
                        <span class="dataItem" style="flex:15%;"><form method="post"><input type="hidden" value="PO.posid" name="order2"><input class="sortHeader" type="submit" value="Position"></form></span>
                        <span class="dataItem" style="flex:10%;"><form method="post"><input type="hidden" value="age" name="order2"><input class="sortHeader" type="submit" value="Age"></form></span>
                        <span class="dataItem" style="flex:10%;">Sign</span>
                    </div>
                    <?php while ($player = $faResults->fetch()): ?>
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
                        <span class="dataItem" style="flex:10%;"><?php echo '<form action="/signPlayer.php" method="post"><input class="createButton" type="submit" value="Sign"><input type="hidden" name="teamid" value="' . $thisteam . '"><input type="hidden" name="playerid" value="' . htmlspecialchars($player['playerid']) . '"></form>'?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </body>
    </div>
</div>
</html>