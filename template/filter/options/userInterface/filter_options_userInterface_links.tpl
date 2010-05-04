<ul class="filter-options-links {if $filterActive}filter-options-links-active{else}filter-options-links-notactive{/if}">
	{foreach from=$possibleValues item=possibleValue}
		<li {if $possibleValue.active || $possibleValue.class}class="{if $possibleValue.active}selected {/if}{$possibleValue.class}"{/if}>
		{strip}
		{assign var="value" value=$possibleValue.item|urlencode}
		{if $filterconf.dropActionParameter}
			<a href="{url parameter=$currentPage additionalParams='%3$s&%1$s[value]=%2$s'|vsprintf:$prefixId:$value:$appendToUrl setup='lib.tx_ptlist.typolinks.options_links'}">
		{else}
			<a href="{url parameter=$currentPage additionalParams='%3$s&%1$s[action]=submit&%1$s[value]=%2$s'|vsprintf:$prefixId:$value:$appendToUrl setup='lib.tx_ptlist.typolinks.options_links'}">
		{/if}
			{$possibleValue.label}
		</a>{/strip} <span class="count">{$possibleValue.quantity|wrap:"(|)"}</span>
		</li>
	{foreachelse}
		<li>{"noItemsFound"|ll}</li>
	{/foreach}
</ul>

{if $filterActive && !$filterconf.hideResetLink && $filterconf.renderResetLinkWithinFilter}
	{strip}{* This action is implemented in the abstract tx_ptlist_filter class *}
	{if $filterconf.dropResetParameter}
		<a href="{url parameter=$resetLinkPid additionalParams=$appendToUrl setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
	{else}
		<a href="{url parameter=$resetLinkPid additionalParams='%2$s&%1$s[action]=reset'|vsprintf:$filter.filterPrefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
	{/if}
	{/strip}
{/if}
