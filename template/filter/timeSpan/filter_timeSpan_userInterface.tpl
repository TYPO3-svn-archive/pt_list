<ul class="filter-timeSpan-links">
{foreach from=$spans item=span}
	<li class="{if $span.value == $value.preset}selected{/if}">
		{strip}
		<a href="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=%2$s'|vsprintf:$prefixId:$span.value setup='lib.tx_ptlist.typolinks.timeSpan'}" title="{$span.formattedTimeSpan}">
			{$span.label}
		</a>{/strip} <span class="count">{$span.quantity|wrap:"(|)"}</span>
	</li>
{/foreach}
</ul>

{if $customDates}
<form method="post" action="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=custom'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.timeSpan'}">
	
	Custom: <br />
	
	<input type="text" name="{$prefixId}[value][from]" class="date-pick dp-applied">
	<br />
	<input type="text" name="{$prefixId}[value][to]" class="date-pick dp-applied">

	<br />
	<input type="submit" />

</form>
{/if}
