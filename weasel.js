$(document).ready(function(){
	
	
	$("input[name='bugReport']").click(function(){
		var value = $(this).val();

		if(value== "notfullScript"){
			$("input[name='reportBugMob']").prop( "disabled", true );
			$("input[name='displayWeaselIco']").prop( "disabled", true );
			$("#reportBugMobO").prop( "checked", true );
			$("#alwaysShown").prop( "checked", true );
		}else{
			var valueResponsive = $("input[name='reportBugMob']:checked").val();
			if(valueResponsive == "N"){
				$("input[name='displayWeaselIco']").prop( "disabled", false );
			}
			$("input[name='reportBugMob']").prop( "disabled", false );
		}
	});
	
	$("input[name='reportBugMob']").click(function(){
		var value = $(this).val();

		if(value== "responsive"){
			$("input[name='displayWeaselIco']").prop( "disabled", true );
			$("#alwaysShown").prop( "checked", true );
		}else{
			$("input[name='displayWeaselIco']").prop( "disabled", false );
		}
	});

	$("input[name='bugReport']:checked").trigger("click");
});
