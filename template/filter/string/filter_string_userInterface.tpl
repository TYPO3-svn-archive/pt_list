{if $filterconf.dropActionParameter}
	<form method="{if $filterconf.useGet}get{else}post{/if}" action="{url parameter=$currentPage additionalParams='%2$s'|vsprintf:$prefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.string'}">
{else}
	<form method="{if $filterconf.useGet}get{else}post{/if}" action="{url parameter=$currentPage additionalParams='%2$s&%1$s[action]=submit'|vsprintf:$prefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.string'}">
{/if}
	<input type="text" name="{$prefixId}[value]" value="{$value}" />
	<br />
	<br />
	<input type="submit" value="{$filterconf.submitValue|ll:0}" />
</form>

{if filterActive && !$filterconf.hideResetLink && $filterconf.renderResetLinkWithinFilter}
	{strip}{* This action is implemented in the abstract tx_ptlist_filter class *}
	{if $filterconf.dropResetParameter}
		<a href="{url parameter=$resetLinkPid additionalParams=$appendToUrl setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
	{else}
		<a href="{url parameter=$resetLinkPid additionalParams='%2$s&%1$s[action]=reset'|vsprintf:$filter.filterPrefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
	{/if}
	{/strip}
{/if}