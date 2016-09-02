<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
    	<meta name="description" content="Enter Site Description">

		<!-- external CSS link -->
		<link rel="stylesheet" href="css/style.css">
		<script src="js/main.js"></script>
		<style type="text/css">
			p{
				width:150px;
				display:inline-block;
				padding:5px 10px;
				text-align: center;
			}
		</style>
	</head>
	<body>
		<section class="container">
			<?php
			$csv = array_map('str_getcsv', file('csv/DKSalaries.csv'));
			$remainingSalary = 50000;
			$players = array();
			$QBs = array();
			$RBs = array();
			$WRs = array();
			$TEs = array();
			$DSTs = array();
			class Player{
				public $id;
				public $position;
				public $name;
				public $salary;
				public $gameInfo;
				public $ppg;
				public $team;
			}
			class Lineup{
				public $totalValue;
				public $qb;
				public $rb1;
				public $rb2;
				public $wr1;
				public $wr2;
				public $te;
				public $flex;
				public $dst;
			}
			foreach ($csv as $key=>$value) {
				if($key!=0){
					$tempPlayer = new Player();
					$tempPlayer->id = $key;
					$tempPlayer->position = $value[0];
					$tempPlayer->name = $value[1];
					$tempPlayer->salary = $value[2];
					$tempPlayer->gameInfo = $value[3];
					$tempPlayer->ppg = $value[4];
					$tempPlayer->team = $value[5];
					switch($value[0]){
						case "DST":
							$DSTs[$key] = $tempPlayer;
							break;
						case "QB":
							$QBs[$key] = $tempPlayer;
							array_push($QBkeys, $key);
							break;
						case "RB":
							$RBs[$key] = $tempPlayer;
							break;
						case "WR":
							$WRs[$key] = $tempPlayer;
							break;
						case "TE";
							$TEs[$key] = $tempPlayer;
							break;
						default:
							break;
					}
				}
			}
			$players["QB"] = $QBs;
			$players["RB"] = $RBs;
			$players["WR"] = $WRs;
			$players["TE"] = $TEs;
			$players["DST"] = $DSTs;

			$combinations = array();
			$combinations["QB"] = array();
			$combinations["RB"] = array();
			$combinations["WR"] = array();
			$combinations["TE"] = array();
			$combinations["DST"] = array();
			function getCombos($playerGroup, $elemCount, $cPos, &$combos){
				if($elemCount==1){
					foreach ($playerGroup as $player) {
						$combo = array();
						array_push($combo, $player);
						array_push($combos[$cPos], $combo);
						//echo $player->name."<br>";
					}
				}
				if($elemCount==2){
					$currentPlayer = array_shift($playerGroup);
					foreach ($playerGroup as $player) {
						$combo = array();
						array_push($combo, $currentPlayer);
						array_push($combo, $player);
						array_push($combos[$cPos], $combo);
						//echo $currentPlayer->name." - ".$player->name."<br>";
					}
					if(count($playerGroup)>1){
						getCombos($playerGroup, 2, $cPos, $combos);
					}	
				}
				if($elemCount==3){
					$firstPlayer = array_shift($playerGroup);
					$nextGroup = $playerGroup;
					foreach ($playerGroup as $secondPlayer) {
						$secondPlayer = array_shift($playerGroup);
						foreach ($playerGroup as $thirdPlayer) {
							$combo = array();
							array_push($combo, $firstPlayer);
							array_push($combo, $secondPlayer);
							array_push($combo, $thirdPlayer);
							array_push($combos[$cPos], $combo);
							//echo $firstPlayer->name." - ".$secondPlayer->name." - ".$thirdPlayer->name."<br>";
						}
					}
					if(count($nextGroup)>2){
						getCombos($nextGroup, 3, $cPos, $combos);
					}	
				}
				if($elemCount==4){
					$combinations = array();
					$firstPlayer = array_shift($playerGroup);
					$nextGroup = $playerGroup;
					foreach ($playerGroup as $secondPlayer) {
						$secondPlayer = array_shift($playerGroup);
						foreach ($playerGroup as $thirdPlayer) {
							$thirdPlayer = array_shift($playerGroup);
							foreach ($playerGroup as $fourthPlayer) {
								$combo = array();
								array_push($combo, $firstPlayer);
								array_push($combo, $secondPlayer);
								array_push($combo, $thirdPlayer);
								array_push($combo, $fourthPlayer);
								//echo var_dump($combo)."<br><br>";
								echo $firstPlayer->name." - ".$secondPlayer->name." - ".$thirdPlayer->name." - ".$fourthPlayer->name."<br>";
							}
						}
					}
					if(count($nextGroup)>3){
						getCombos($nextGroup, 4);
					}	
				}
			}

			/*$testNumbers = array(1,2,3,4,5,6,7,8,9);
			function combosOfFour($set){
				for ($i=0; $i < count($set) ; $i++) { 
					$workingSet = $set;
					$firstNumber = array_shift($workingSet);
					$secondNumber = array_shift($workingSet);
					$thirdNumber = array_shift()
				}
			}*/

			
			//echo "<br><br>QB COMBOS:<br><br>";
			getCombos($QBs, 1, "QB", $combinations);
			//var_dump($combinations["QB"]);
			//echo "<br><br>RB COMBOS:<br><br>";
			getCombos($RBs, 3, "RB", $combinations);
			//var_dump($combinations["RB"]);
			//echo "<br><br>WR COMBOS:<br><br>";
			getCombos($WRs, 3, "WR", $combinations);
			//echo "<br><br>".count($combinations["WR"])."<br><br>";
			//var_dump($combinations["WR"]);
			//echo "<br><br>TE COMBOS:<br><br>";
			getCombos($TEs, 1, "TE", $combinations);
			//var_dump($combinations["TE"]);
			//echo "<br><br>DST COMBOS:<br><br>";
			getCombos($DSTs, 1, "DST", $combinations);
			//var_dump($combinations["DST"]);

			$totalCombinations = 0;
			$output="";
			foreach ($combinations["QB"] as $QBCombo) {
				foreach ($combinations["RB"] as $RBCombo) {
					foreach ($combinations["WR"] as $WRCombo) {
						foreach ($combinations["TE"] as $TECombo) {
							foreach ($combinations["DST"] as $DSTCombo) {
								$totalCost = 0;
								$potentialPoints = 0;
								$QBnames="";
								foreach ($QBCombo as $QB) {
									$totalCost+=$QB->salary;
									$potentialPoints += $QB->ppg;
									$QBnames.=$QB->name.", ";
								}
								$RBnames="";
								foreach ($RBCombo as $RB) {
									$totalCost+=$RB->salary;
									$potentialPoints += $RB->ppg;
									$RBnames.=$RB->name.", ";
								}
								$WRnames="";
								foreach ($WRCombo as $WR) {
									$totalCost+=$WR->salary;
									$potentialPoints += $WR->ppg;
									$WRnames.=$WR->name.", ";
								}
								$TEnames="";
								foreach ($TECombo as $TE) {
									$totalCost+=$TE->salary;
									$potentialPoints += $TE->ppg;
									$TEnames.=$TE->name.", ";
								}
								$DSTnames="";
								foreach ($DSTCombo as $DST) {
									$totalCost+=$DST->salary;
									$potentialPoints += $DST->ppg;
									$DSTnames.=$DST->name.", ";
								}
								if($totalCost<=50000){
									$totalCombinations+=1;
									$output.=$totalCost.",".$potentialPoints.",".$QBnames.",".$RBnames.",".$WRnames.",".$TEnames.",".$DSTnames."\n";
								}
							}
						}
					}
				}
			}
			function writeFile($string){
				$my_file = 'combinations.csv';
				$handle = fopen($my_file, 'w');
				$data = $string;
				fwrite($handle, $data);
			}
			writeFile($output);
			echo $output;
			echo "<br><br>Total combinations: "+$totalCombinations;
			?>
		</section>
	</body>
</html>