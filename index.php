<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
    	<meta name="description" content="Enter Site Description">

		<!-- external CSS link -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<link rel="stylesheet" href="css/style.css">
		<script src="js/main.js"></script>
	</head>
	<body>
		<header class="container">
			<div class="build-csv"><span>GO!</span></div>
			<div>Lineups In Use: <span class="inUseCount">0</span></div>
			<h1>Draftkings NFL Lineup Creator</h1>
		</header>
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
				public $qb;
				public $rb1;
				public $rb2;
				public $wr1;
				public $wr2;
				public $te;
				public $flex;
				public $dst;
			}
			$html.=('<section class="player-list">
								<h2>Player List:</h2>
								<div class="players">
									<div class="player header">
										<span>Position</span>
										<span>Name</span>
										<span>Team</span>
										<span>Cost</span>
										<span>Matchup</span>
										<span>PPT</span>
										<span>Available In:</span>
										<span>Used In:</span>
									</div>');
								foreach ($csv as $key=>$value) {
									if($key!=0){
										$tempPlayer = new Player();
										$tempPlayer->id = $value[2];
										$tempPlayer->position = $value[0];
										$tempPlayer->name = $value[1];
										$tempPlayer->salary = $value[3];
										$tempPlayer->gameInfo = $value[4];
										$tempPlayer->team = $value[5];
										$tempPlayer->ppg = $value[6];
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

									$html.=('<div data-id="'.$tempPlayer->id.'" class="player">
														<span data-pposition="'.$tempPlayer->position.'">'.$tempPlayer->position.'</span>
														<span data-pname="'.$tempPlayer->name.'">'.$tempPlayer->name.'</span>
														<span data-pteam="'.$tempPlayer->team.'">'.$tempPlayer->team.'</span>
														<span data-psalary="'.$tempPlayer->salary.'">'.$tempPlayer->salary.'</span>
														<span data-pinfo="'.$tempPlayer->gameInfo.'">'.$tempPlayer->gameInfo.'</span>
														<span data-pppt="'.$tempPlayer->ppg.'">'.$tempPlayer->ppg.'</span>
														<span data-availIn="">0</span>
														<span data-usedIn="">0</span>
													</div>');

												
									}
								}
			$html.=('	</div>
							</section>');
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
					}
				}
				if($elemCount==2){
					$currentPlayer = array_shift($playerGroup);
					foreach ($playerGroup as $player) {
						$combo = array();
						array_push($combo, $currentPlayer);
						array_push($combo, $player);
						array_push($combos[$cPos], $combo);
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
						}
					}
					if(count($nextGroup)>2){
						getCombos($nextGroup, 3, $cPos, $combos);
					}	
				}
				if($elemCount==4){
					for ($i=0; $i < count($playerGroup); $i++) { 
						$firstPlayer = $playerGroup[$i];
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
								}
							}
						}
					}
				}
			}			

			$qbColors = array('blue', 'orange', 'red');

			$totalCombinations = 0;
			$output="";
			$html .= '<section class="final-lineups">
									<h2>In Use: <span class="inUseCount"></span></h2>
									<div class="lineup-list in-use">
										<div class="lineup header-row">
											<div class="index">#</div>
											<div class="move">Remove Lineup</div>
											<div class="salary">Total Salary</div>
											<div class="points">PPT</div>
											<div class="qbs">QB</div>
											<div class="rbs">RBs</div>
											<div class="wrs">WRs</div>
											<div class="tes">TEs</div>
											<div class="dsts">DST</div>
										</div>
									</div>
								</section>';
			$html .= '<section class="available-lineups">
									<h2>Available: <span class="availLineupCount"></span></h2>
									<div class="sort-fields">
										<div><span>QB:</span><input name="qb-filter" class="qb-filter" type="text"></div>
										<div><span>RB:</span><input name="rb-filter" class="rb-filter" type="text"></div>
										<div><span>WR:</span><input name="wr-filter" class="wr-filter" type="text"></div>
										<div><span>TE:</span><input name="te-filter" class="te-filter" type="text"></div>
										<div><span>DST:</span><input name="dst-filter" class="dst-filter" type="text"></div>
										<div class="filter-button">Filter</div>
										<div class="clear-button">Clear</div>
									</div>
									<div class="lineup-list">
										<div class="lineup header-row">
											<div class="index">#</div>
											<div class="move">Add Lineup</div>
											<div class="salary">Total Salary<br><span class="sort-link available">SORT</span></div>
											<div class="points">PPT<br><span class="sort-link available">SORT</span></div>
											<div class="qbs">QB</div>
											<div class="rbs">RBs</div>
											<div class="wrs">WRs</div>
											<div class="tes">TEs</div>
											<div class="dsts">DST</div>
										</div>
									';
			$playerUsage = array();

			{
				getCombos($QBs, 1, "QB", $combinations);
				getCombos($RBs, 2, "RB", $combinations);
				getCombos($WRs, 4, "WR", $combinations);
				getCombos($TEs, 1, "TE", $combinations);
				getCombos($DSTs, 1, "DST", $combinations);

				$comboCount = 0;
				foreach ($combinations["QB"] as $comboKey=>$QBCombo) {
					foreach ($combinations["RB"] as $RBCombo) {
						foreach ($combinations["WR"] as $WRCombo) {
							foreach ($combinations["TE"] as $TECombo) {
								foreach ($combinations["DST"] as $DSTCombo) {
									$comboCount+=1;
									$totalCost = 0;
									$potentialPoints = 0;
									$QBnames="";
									$QBid="";
									$QBteam = "";//TODO if no receivers match QB team, then continue;
									foreach ($QBCombo as $key=>$QB) {
										$totalCost+=$QB->salary;
										$potentialPoints += $QB->ppg;
										$QBnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$QB->id.'">'.$QB->name."</span><br>";
										$QBid.=$QB->id.",";
										$QBteam = $QB->team;
									}
									$RBnames="";
									$RBid="";
									foreach ($RBCombo as $key=>$RB) {
										if($RB->team==$QBteam){
											$RBnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$RB->id.'">'.$RB->name."</span><br>";
										}else{
											$RBnames.='<span class="player" data-id="'.$RB->id.'">'.$RB->name."</span><br>";
										}
										$totalCost+=$RB->salary;
										$potentialPoints += $RB->ppg;
										
										$RBid.=$RB->id.",";
									}
									$WRnames="";
									$WRid="";
									foreach ($WRCombo as $key=>$WR) {
										if($WR->team==$QBteam){
											$WRnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$WR->id.'">'.$WR->name."</span><br>";
										}else{
											$WRnames.='<span class="player" data-id="'.$WR->id.'">'.$WR->name."</span><br>";
										}
										$totalCost+=$WR->salary;
										$potentialPoints += $WR->ppg;
										$WRid.=$WR->id.",";
									}
									if($totalCost>50000){
										continue;
									}
									$TEnames="";
									$TEid="";
									foreach ($TECombo as $key=>$TE) {
										if($TE->team==$QBteam){
											$TEnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$TE->id.'">'.$TE->name."</span><br>";
										}else{
											$TEnames.='<span class="player" data-id="'.$TE->id.'">'.$TE->name."</span><br>";
										}
										$totalCost+=$TE->salary;
										$potentialPoints += $TE->ppg;
										$TEid.=$TE->id.",";
									}
									if($totalCost>50000){
										continue;
									}
									$DSTnames="";
									$DSTid="";
									foreach ($DSTCombo as $key=>$DST) {
										$totalCost+=$DST->salary;
										$potentialPoints += $DST->ppg;
										$DSTnames.='<span class="player" data-id="'.$DST->id.'">'.$DST->name."</span><br>";
										$DSTid.=$DST->id.",";
									}
									if($totalCost<=50000){
										$totalCombinations+=1;
										$html.='<div class="lineup" data-inuse="false">
															<div class="index">'.$comboCount.'</div>
															<div class="move"><span class="toggle-lineup">ADD</span></div>
															<div class="salary">'.$totalCost.'</div>
															<div class="points">'.$potentialPoints.'</div>
															<div class="qbs">'.rtrim($QBnames, ",").'</div>
															<div class="rbs">'.rtrim($RBnames, ",").'</div>
															<div class="wrs">'.rtrim($WRnames, ",").'</div>
															<div class="tes">'.rtrim($TEnames, ",").'</div>
															<div class="dst">'.rtrim($DSTnames, ",").'</div>
														</div>';
										$output.=$totalCost.",".$potentialPoints.",".rtrim($QBid, ",").",".rtrim($RBid, ",").",".rtrim($WRid, ",").",".rtrim($TEid, ",").",".rtrim($DSTid, ",")."\n";
									}
								}
							}
						}
					}
				}
			}
			$combinations = array();
			$combinations["QB"] = array();
			$combinations["RB"] = array();
			$combinations["WR"] = array();
			$combinations["TE"] = array();
			$combinations["DST"] = array();
			{
				getCombos($QBs, 1, "QB", $combinations);
				getCombos($RBs, 3, "RB", $combinations);
				getCombos($WRs, 3, "WR", $combinations);
				getCombos($TEs, 1, "TE", $combinations);
				getCombos($DSTs, 1, "DST", $combinations);

				$comboCount = 0;
				foreach ($combinations["QB"] as $comboKey=>$QBCombo) {
					foreach ($combinations["RB"] as $RBCombo) {
						foreach ($combinations["WR"] as $WRCombo) {
							foreach ($combinations["TE"] as $TECombo) {
								foreach ($combinations["DST"] as $DSTCombo) {
									$comboCount+=1;
									$totalCost = 0;
									$potentialPoints = 0;
									$QBnames="";
									$QBid="";
									$QBteam = "";//TODO if no receivers match QB team, then continue;
									foreach ($QBCombo as $key=>$QB) {
										$totalCost+=$QB->salary;
										$potentialPoints += $QB->ppg;
										$QBnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$QB->id.'">'.$QB->name."</span><br>";
										$QBid.=$QB->id.",";
										$QBteam = $QB->team;
									}
									$RBnames="";
									$RBid="";
									foreach ($RBCombo as $key=>$RB) {
										if($RB->team==$QBteam){
											$RBnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$RB->id.'">'.$RB->name."</span><br>";
										}else{
											$RBnames.='<span class="player" data-id="'.$RB->id.'">'.$RB->name."</span><br>";
										}
										$totalCost+=$RB->salary;
										$potentialPoints += $RB->ppg;
										
										$RBid.=$RB->id.",";
									}
									$WRnames="";
									$WRid="";
									foreach ($WRCombo as $key=>$WR) {
										if($WR->team==$QBteam){
											$WRnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$WR->id.'">'.$WR->name."</span><br>";
										}else{
											$WRnames.='<span class="player" data-id="'.$WR->id.'">'.$WR->name."</span><br>";
										}
										$totalCost+=$WR->salary;
										$potentialPoints += $WR->ppg;
										$WRid.=$WR->id.",";
									}
									if($totalCost>50000){
										continue;
									}
									$TEnames="";
									$TEid="";
									foreach ($TECombo as $key=>$TE) {
										if($TE->team==$QBteam){
											$TEnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$TE->id.'">'.$TE->name."</span><br>";
										}else{
											$TEnames.='<span class="player" data-id="'.$TE->id.'">'.$TE->name."</span><br>";
										}
										$totalCost+=$TE->salary;
										$potentialPoints += $TE->ppg;
										$TEid.=$TE->id.",";
									}
									if($totalCost>50000){
										continue;
									}
									$DSTnames="";
									$DSTid="";
									foreach ($DSTCombo as $key=>$DST) {
										$totalCost+=$DST->salary;
										$potentialPoints += $DST->ppg;
										$DSTnames.='<span class="player" data-id="'.$DST->id.'">'.$DST->name."</span><br>";
										$DSTid.=$DST->id.",";
									}
									if($totalCost<=50000){
										$totalCombinations+=1;
										$html.='<div class="lineup" data-inuse="false">
															<div class="index">'.$comboCount.'</div>
															<div class="move"><span class="toggle-lineup">ADD</span></div>
															<div class="salary">'.$totalCost.'</div>
															<div class="points">'.$potentialPoints.'</div>
															<div class="qbs">'.rtrim($QBnames, ",").'</div>
															<div class="rbs">'.rtrim($RBnames, ",").'</div>
															<div class="wrs">'.rtrim($WRnames, ",").'</div>
															<div class="tes">'.rtrim($TEnames, ",").'</div>
															<div class="dst">'.rtrim($DSTnames, ",").'</div>
														</div>';
										$output.=$totalCost.",".$potentialPoints.",".rtrim($QBid, ",").",".rtrim($RBid, ",").",".rtrim($WRid, ",").",".rtrim($TEid, ",").",".rtrim($DSTid, ",")."\n";
									}
								}
							}
						}
					}
				}
			}
			$combinations = array();
			$combinations["QB"] = array();
			$combinations["RB"] = array();
			$combinations["WR"] = array();
			$combinations["TE"] = array();
			$combinations["DST"] = array();
			{
				getCombos($QBs, 1, "QB", $combinations);
				getCombos($RBs, 2, "RB", $combinations);
				getCombos($WRs, 3, "WR", $combinations);
				getCombos($TEs, 2, "TE", $combinations);
				getCombos($DSTs, 1, "DST", $combinations);

				$comboCount = 0;
				foreach ($combinations["QB"] as $comboKey=>$QBCombo) {
					foreach ($combinations["RB"] as $RBCombo) {
						foreach ($combinations["WR"] as $WRCombo) {
							foreach ($combinations["TE"] as $TECombo) {
								foreach ($combinations["DST"] as $DSTCombo) {
									$comboCount+=1;
									$totalCost = 0;
									$potentialPoints = 0;
									$QBnames="";
									$QBid="";
									$QBteam = "";//TODO if no receivers match QB team, then continue;
									foreach ($QBCombo as $key=>$QB) {
										$totalCost+=$QB->salary;
										$potentialPoints += $QB->ppg;
										$QBnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$QB->id.'">'.$QB->name."</span><br>";
										$QBid.=$QB->id.",";
										$QBteam = $QB->team;
									}
									$RBnames="";
									$RBid="";
									foreach ($RBCombo as $key=>$RB) {
										if($RB->team==$QBteam){
											$RBnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$RB->id.'">'.$RB->name."</span><br>";
										}else{
											$RBnames.='<span class="player" data-id="'.$RB->id.'">'.$RB->name."</span><br>";
										}
										$totalCost+=$RB->salary;
										$potentialPoints += $RB->ppg;
										
										$RBid.=$RB->id.",";
									}
									$WRnames="";
									$WRid="";
									foreach ($WRCombo as $key=>$WR) {
										if($WR->team==$QBteam){
											$WRnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$WR->id.'">'.$WR->name."</span><br>";
										}else{
											$WRnames.='<span class="player" data-id="'.$WR->id.'">'.$WR->name."</span><br>";
										}
										$totalCost+=$WR->salary;
										$potentialPoints += $WR->ppg;
										$WRid.=$WR->id.",";
									}
									if($totalCost>50000){
										continue;
									}
									$TEnames="";
									$TEid="";
									foreach ($TECombo as $key=>$TE) {
										if($TE->team==$QBteam){
											$TEnames.='<span class="player" style="color:'.$qbColors[$comboKey].'" data-id="'.$TE->id.'">'.$TE->name."</span><br>";
										}else{
											$TEnames.='<span class="player" data-id="'.$TE->id.'">'.$TE->name."</span><br>";
										}
										$totalCost+=$TE->salary;
										$potentialPoints += $TE->ppg;
										$TEid.=$TE->id.",";
									}
									if($totalCost>50000){
										continue;
									}
									$DSTnames="";
									$DSTid="";
									foreach ($DSTCombo as $key=>$DST) {
										$totalCost+=$DST->salary;
										$potentialPoints += $DST->ppg;
										$DSTnames.='<span class="player" data-id="'.$DST->id.'">'.$DST->name."</span><br>";
										$DSTid.=$DST->id.",";
									}
									if($totalCost<=50000){
										$totalCombinations+=1;
										$html.='<div class="lineup" data-inuse="false">
															<div class="index">'.$comboCount.'</div>
															<div class="move"><span class="toggle-lineup">ADD</span></div>
															<div class="salary">'.$totalCost.'</div>
															<div class="points">'.$potentialPoints.'</div>
															<div class="qbs">'.rtrim($QBnames, ",").'</div>
															<div class="rbs">'.rtrim($RBnames, ",").'</div>
															<div class="wrs">'.rtrim($WRnames, ",").'</div>
															<div class="tes">'.rtrim($TEnames, ",").'</div>
															<div class="dst">'.rtrim($DSTnames, ",").'</div>
														</div>';
										$output.=$totalCost.",".$potentialPoints.",".rtrim($QBid, ",").",".rtrim($RBid, ",").",".rtrim($WRid, ",").",".rtrim($TEid, ",").",".rtrim($DSTid, ",")."\n";
									}
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
			echo $html;
			?>
		</section>
	</body>
</html>