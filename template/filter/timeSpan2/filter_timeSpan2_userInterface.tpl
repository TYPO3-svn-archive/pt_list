{debug}
<form method="post" action="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=custom'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.timeSpan'}">
	
	Custom: <br />
	
	Von: <input type="text" value="{$value.from}" name="{$prefixId}[from]" class="date-pick dp-applied"  id="date_from">
	<br />
	Bis: <input type="text" value="{$value.to}" name="{$prefixId}[to]" class="date-pick dp-applied"  id="date_to">

	<br />
	<input type="submit" value="{$filter.submitValue|ll:0}" />
</form>
