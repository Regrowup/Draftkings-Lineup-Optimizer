$(document).ready(function(){
	updateUsedCount();
	totalPlayerPresence();

	$('.toggle-lineup').on('click', function(){
		var aLineup = $(this).closest('.lineup');
		if(aLineup.attr('data-inuse')=="false"){
			$(aLineup).attr('data-inuse', 'true');
			$(this).text('REMOVE');
			$('.lineup-list.in-use').append(aLineup);

			//Maintenance Functions
			updateUsedCount();
			updatePlayerUsage(aLineup, true);
		}else{
			console.log('Remove Lineup');
			$(aLineup).attr('data-inuse', 'false');
			$(this).text('ADD');
			$('.available-lineups .lineup-list').append(aLineup);

			//Maintenance Functions
			updateUsedCount();
			updatePlayerUsage(aLineup, false);
		}
	});

	$(".build-csv").on('click', function(){
		buildDraftkingsCSV();
	});

	function updateUsedCount(){
		var inUseCount = $('.lineup-list.in-use .lineup').length-1;
		var availLineupCount = $('.available-lineups .lineup-list .lineup').length-1;
		$('.availLineupCount').text(availLineupCount);
		$('.inUseCount').text(inUseCount);
	}

	function buildDraftkingsCSV(){
		var draftkingsCsvString = "QB,RB,RB,WR,WR,WR,TE,FLEX,DST\n";
		$('.lineup-list.in-use .lineup:not(:first-child)').each(function(index, el) {
			var flex = "";
			var qbs = $(el).find('.qbs span');
			$(qbs).each(function(index, el) {
				draftkingsCsvString+=$(el).attr('data-id')+",";
			});
			var rbs = $(el).find('.rbs span');
			$(rbs).each(function(index, el) {
				if(index<2){
					draftkingsCsvString+=$(el).attr('data-id')+",";
				}else{
					flex=$(el).attr('data-id');
				}
			});
			var wrs = $(el).find('.wrs span');
			$(wrs).each(function(index, el) {
				if(index<3){
					draftkingsCsvString+=$(el).attr('data-id')+",";
				}else{
					flex=$(el).attr('data-id');
				}
			});
			var tes = $(el).find('.tes span');
			$(tes).each(function(index, el) {
				if(index<1){
					draftkingsCsvString+=$(el).attr('data-id')+",";
				}else{
					flex=$(el).attr('data-id');
				}
			});
			draftkingsCsvString+=flex+",";
			var dst = $(el).find('.dst span');
			$(dst).each(function(index, el) {
				draftkingsCsvString+=$(el).attr('data-id')+"\n";
			});	
		});

		var filename = $('input.filename').val();
		console.log("--------- COPY CONTENTS BELOW - SAVE AS .CSV FILE ---------");
		console.log(draftkingsCsvString);
		console.log("--------- COPY CONTENTS ABOVE - SAVE AS .CSV FILE ---------");
	}

	function totalPlayerPresence(){
		$('.available-lineups .lineup').each(function(index, el) {
			$(el).find('span.player').each(function(pIndex, player) {
				var pId = $(player).attr('data-id');
				var playerRow = $(".player-list").find(".player[data-id='" + pId + "']");
				var availIn = parseInt($(playerRow).find('span[data-availin]').text())+1;
				$(playerRow).find('span[data-availin]').text(availIn);
			});
		});
	}

	function updatePlayerUsage(lineup, isAdd){
		var tempLineup = $(lineup).find('span.player');
		$(tempLineup).each(function(index, el) {
			var tempPlayerId = $(el).attr('data-id');
			if(isAdd){
				var playerRow = $(".player-list").find(".player[data-id='" + tempPlayerId + "']");

				var availIn = parseInt($(playerRow).find('span[data-availin]').text())-1;
				$(playerRow).find('span[data-availin]').text(availIn);

				var usedIn = parseInt($(playerRow).find('span[data-usedIn]').text())+1;
				$(playerRow).find('span[data-usedIn]').text(usedIn);
			}else{
				var playerRow = $(".player-list").find(".player[data-id='" + tempPlayerId + "']");

				var availIn = parseInt($(playerRow).find('span[data-availin]').text())+1;
				$(playerRow).find('span[data-availin]').text(availIn);

				var usedIn = parseInt($(playerRow).find('span[data-usedIn]').text())-1;
				$(playerRow).find('span[data-usedIn]').text(usedIn);
			}
		});
	}

	$('.sort-link.available').on('click', function () {
		$('.available-lineups .lineup:not(.header-row)').sort(function(a, b) {
		    return -$(a).find(".points").text() - -$(b).find(".points").text();
		})
		.appendTo($('.available-lineups .lineup-list'));
	});
});