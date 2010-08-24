<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.options_radio'}">
	{foreach from=$possibleValues item=possibleValue}
		<input type="radio" name="{$prefixId}[value]" value="{$possibleValue.item|urlencode}"{if $possibleValue.active} checked="checked"{/if}{if $submitOnChange} onchange="submit()"{/if}>
		{$possibleValue.label} <span class="count">{$possibleValue.quantity|wrap:"(|)"}</span><br />
	{/foreach}

	{if !$submitOnChange}
		<br />
		<input type="submit" value="{$filterconf.submitValue|ll:0}" />
	{/if}
</form>