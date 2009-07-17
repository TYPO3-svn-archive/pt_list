{*debug*}
<form method="post" action="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=custom'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.timeSpan'}">
	
	Custom: <br />
	
	<label for="date_from">Von:</label> <input type="text" value="{$value.from}" name="{$prefixId}[from]" class="date-pick dp-applied"  id="date_from">
	<br />
	<label for="date_to">Bis:</label> <input type="text" value="{$value.to}" name="{$prefixId}[to]" class="date-pick dp-applied"  id="date_to">

	<br />
	<input type="submit" value="{$filter.submitValue|ll:0}" />
</form>
