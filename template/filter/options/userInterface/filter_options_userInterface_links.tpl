<ul class="filter-options-links {if $filterActive}filter-options-links-active{else}filter-options-links-notactive{/if}">
	{foreach from=$possibleValues item=possibleValue}
		<li class="{if $possibleValue.active}selected{/if} {$possibleValue.class}">
		{strip}
		<a href="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=%2$s'|vsprintf:$prefixId:$possibleValue.item|urlencode setup='lib.tx_ptlist.typolinks.options_links'}">
			{$possibleValue.label}
		</a>{/strip} <span class="count">{$possibleValue.quantity|wrap:"(|)"}</span>
		</li>
	{foreachelse}
		{"noItemsFound"|ll}
	{/foreach}
</ul>
