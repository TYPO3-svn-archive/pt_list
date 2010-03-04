{if $filterconf.dropActionParameter}
	<form method="post" action="{url parameter=$currentPage additionalParams='%2$s'|vsprintf:$prefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.integer'}">
{else}
	<form method="post" action="{url parameter=$currentPage additionalParams='%2$s&%1$s[action]=submit'|vsprintf:$prefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.integer'}">
{/if}
	<input type="text" name="{$prefixId}[value]" value="{$value}" />
	<br />
	<br />
	<input type="submit" value="{$filterconf.submitValue|ll:0}" />
</form>

