<ul class="tx-ptlist-filterbox {$filterboxId}">
{foreach from=$filterbox item=filter}
	<li class="tx-ptlist-filter {$filter.filterClass}{if $filter.isActive} filteractive{/if} {$filter.filterId}">
		{$filter.label|wrap:'<h4 class="filterbox-label">|</h4>'}
		
		<div class="tx-ptlist-filterbox-userinterface">
			{$filter.userInterface}
		</div>
		
		{if $filter.isActive && $filter.hideResetLink == false}
			{strip}{* This action is implemented in the abstract tx_ptlist_filter class *}
			<a href="{url parameter=$currentPage additionalParams='&%s[action]=reset'|vsprintf:$filter.filterPrefixId setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
			{/strip}
		{/if}
		
	</li>
{/foreach}
</ul>