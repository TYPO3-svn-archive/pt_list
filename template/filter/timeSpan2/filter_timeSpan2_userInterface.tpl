{*********************************************
 * User interface template for Timespan filter
 * $ID$
 * author Michael Knoll <knoll@punkt.de>
 * since 2009-07-21
 *
 * TS configuration for filter can be accessed 
 * via $filter.<tsValue>
 *********************************************}
 
<form method="post" action="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=custom'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.timeSpan'}">
	<label for="date_from">From:</label> <input type="text" value="{$value.from}" name="{$prefixId}[from]" class="date-pick dp-applied"  id="date_from">
	<br />
	<label for="date_to">To:</label> <input type="text" value="{$value.to}" name="{$prefixId}[to]" class="date-pick dp-applied"  id="date_to">

	<br />
	<input type="submit" value="{$filterconf.submitValue|ll:0}" />
</form>
