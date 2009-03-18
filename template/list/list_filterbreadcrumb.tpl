<div class="tx-ptlist-filterbreadcrumb-container">
	<h4>Aktive Filter:</h4>
	{foreach from=$filters item=filter}
		{if $filter.isActive}
		<div class="tx-ptlist-filterbreadcrumbelement tx-ptlist-filterbreadcrumb-{$filter.filterId} filteractive">
			<div class="breadcrumbcontent">{$filter.breadcrumb}</div>
			<div class="breadcrumbresetlink">	
				<a href="{url parameter=$currentPage additionalParams='&%s[action]=reset'|vsprintf:$filter.filterPrefixId setup='lib.tx_ptlist.typolinks.breadCrumbResetLink'}" title="{'reset'|ll}">{image file="EXT:pt_list/res/button_cancel.png"}</a>
			</div>
		</div>
		{/if}
	{/foreach}
</div>