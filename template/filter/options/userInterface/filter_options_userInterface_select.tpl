<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.options_select'}">
	<select size="{$selectBoxSize}" name="{$prefixId}[value][]" {if $submitOnChange}onchange="submit()"{/if} {if $multiple}multiple{/if}>
		{foreach from=$possibleValues item=possibleValue}
			<option value="{$possibleValue.item|urlencode}"{if $possibleValue.active} selected{/if}>
				{$possibleValue.label} <span class="count">{$possibleValue.quantity|wrap:"(|)"}</span>
			</option>
		{/foreach}
	</select>

	{if !$submitOnChange}
		<br />
		<br />
		<input type="submit" value="{$filterconf.submitValue|ll:0}" />
	{/if}
</form>