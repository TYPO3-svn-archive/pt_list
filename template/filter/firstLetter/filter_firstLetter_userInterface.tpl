<ul class="firstletterlist">
{foreach from=$possibleValues item=possibleValue}
	<li class="{if ($value == $possibleValue.value) || (empty($value) && ('reset' == $possibleValue.value))}active{/if}">
		<a href="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[value]=%2$s'|vsprintf:$prefixId:$possibleValue.value setup='lib.tx_ptlist.typolinks.firstLetter'}" title="({$possibleValue.quantity})">
			{$possibleValue.label|ll:0}
		</a>
		<span class="letter-quantity" style="display:none;">{$possibleValue.quantity}</span>		
	</li>
{/foreach}
</ul>