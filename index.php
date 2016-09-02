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
							$DSTs[count($DSTs)] = $tempPlayer;
							break;
						case "QB":
							$QBs[count($QBs)] = $tempPlayer;
							break;
						case "RB":
							$RBs[count($RBs)] = $tempPlayer;
							break;
						case "WR":
							$WRs[count($WRs)] = $tempPlayer;
							break;
						case "TE";
							$TEs[count($TEs)] = $tempPlayer;
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
					//echo "WR count: ".count($playerGroup)."<br><br>";
					for ($i=0; $i < count($playerGroup); $i++) { 
						$firstPlayer = $playerGroup[$i];
						//echo var_dump($firstPlayer);
						for ($j=$i+1; $j < count($playerGroup); $j++) { 
							$secondPlayer = $playerGroup[$j];
							for ($k=$j+1; $k < count($playerGroup); $k++) { 
								$thirdPlayer = $playerGroup[$k];
								for ($h=$k+1; $h < count($playerGroup); $h++) { 
									$fourthPlayer = $playerGroup[$h];
									$combo = array();
									array_push($combo, $firstPlayer);
									array_push($combo, $secondPlayer);
									array_push($combo, $thirdPlayer);
									array_push($combo, $fourthPlayer);
									array_push($combos[$cPos], $combo);
									//echo "<br>".$firstPlayer->name.",".$secondPlayer->name.",".$thirdPlayer->name.",".$fourthPlayer->name."<br>";
								}
							}
						}
					}
				}
			}			

			getCombos($QBs, 1, "QB", $combinations);
			getCombos($RBs, 2, "RB", $combinations);
			getCombos($WRs, 4, "WR", $combinations);
			getCombos($TEs, 1, "TE", $combinations);
			getCombos($DSTs, 1, "DST", $combinations);

			$totalCombinations = 0;
			$output="";
			$html = "";
			$html .= '<section>
									<div>
										<div class="header-row">
											<p>Total Salary</p>
											<p>Projected Point Total</p>
											<p>QB</p>
											<p>RBs</p>
											<p>WRs</p>
											<p>TEs</p>
											<p>DST</p>
										</div>
									';
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
									$QBnames.=$QB->name.",";
								}
								$RBnames="";
								foreach ($RBCombo as $RB) {
									$totalCost+=$RB->salary;
									$potentialPoints += $RB->ppg;
									$RBnames.=$RB->name.",";
								}
								$WRnames="";
								foreach ($WRCombo as $WR) {
									$totalCost+=$WR->salary;
									$potentialPoints += $WR->ppg;
									$WRnames.=$WR->name.",";
								}
								$TEnames="";
								foreach ($TECombo as $TE) {
									$totalCost+=$TE->salary;
									$potentialPoints += $TE->ppg;
									$TEnames.=$TE->name.",";
								}
								$DSTnames="";
								foreach ($DSTCombo as $DST) {
									$totalCost+=$DST->salary;
									$potentialPoints += $DST->ppg;
									$DSTnames.=$DST->name.",";
								}
								if($totalCost<=50000){
									$totalCombinations+=1;
									$html.="<div>
														<p>".$totalCost."</p>
														<p>".$potentialPoints."</p>
														<p>".rtrim($QBnames, ",")."</p>
														<p>".rtrim($RBnames, ",")."</p>
														<p>".rtrim($WRnames, ",")."</p>
														<p>".rtrim($TEnames, ",")."</p>
														<p>".rtrim($DSTnames, ",")."</p>
													</div>";
									$output.=$totalCost.",".$potentialPoints.",".rtrim($QBnames, ",").",".rtrim($RBnames, ",").",".rtrim($WRnames, ",").",".rtrim($TEnames, ",").",".rtrim($DSTnames, ",")."\n";
								}
							}
						}
					}
				}
			}
			$html .= "	<div>
								</section>";

			function writeFile($string){
				$my_file = 'combinations.csv';
				$handle = fopen($my_file, 'w');
				$data = $string;
				fwrite($handle, $data);
			}
			writeFile($output);
			echo $html;
			//echo "<br><br>Total combinations: "+$totalCombinations;
			?>
		</section>
	</body>
</html>