<ul class="tx-ptlist-filterbox {$filterboxId}" id="tx-ptlist-filterbox-{$filterboxId}">
{foreach from=$filterbox item=filter}
	<li class="tx-ptlist-filter {$filter.filterClass}{if $filter.isActive} filteractive{/if} {$filter.filterId}">
		{$filter.label|ll:0|wrap:'<h4 class="filterbox-label">|</h4>'}
		<div class="tx-ptlist-filterbox-userinterface">
			{$filter.userInterface}
		</div>
		
		{if $filter.isActive && $filter.hideResetLink == false && !$filter.renderResetLinkWithinFilter}
			{strip}{* This action is implemented in the abstract tx_ptlist_filter class *}
			{if $filter.dropResetParameter}
				<a href="{url parameter=$resetLinkPid additionalParams=$appendToUrl setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
			{else}
				<a href="{url parameter=$resetLinkPid additionalParams='%2$s&%1$s[action]=reset'|vsprintf:$filter.filterPrefixId:$appendToUrl setup='lib.tx_ptlist.typolinks.filterResetLink'}" class="resetlink">{"reset"|ll}</a>
			{/if}
			{/strip}
		{/if}
		
	</li>
{/foreach}
</ul>