<table class="tx-ptlist-list tx-ptlist-list-standard {$listIdentifier}">

	<tr>
		{foreach from=$columns item=column name="columnHeaders" key="columnIdentifier"}
			<th class="tx-ptlist-list-header">
			
				{if $columnIdentifier == "continentColumn"}
					{$filterbox.groupContinents.userInterface}
				{else}
					{if $column.isSortable}
						{if $column.sortingState == 0}{* not sorted *}
							<a href="{url parameter=$currentPage additionalParams='&%1$s[action]=changeSortingOrder&%1$s[column]=%2$s&%1$s[direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'1' setup='lib.tx_ptlist.typolinks.columnSortLinks'}">{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_default.png"}</a>
						{elseif $column.sortingState == 1}{* ascending sorted *}
							<a href="{url parameter=$currentPage additionalParams='&%1$s[action]=changeSortingOrder&%1$s[column]=%2$s&%1$s[direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'-1' setup='lib.tx_ptlist.typolinks.columnSortLinks'}">{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_desc.png"}</a>
						{elseif $column.sortingState == -1}{* descending sorted *}
							<a href="{url parameter=$currentPage additionalParams='&%1$s[action]=changeSortingOrder&%1$s[column]=%2$s&%1$s[direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'1' setup='lib.tx_ptlist.typolinks.columnSortLinks'}">{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_asc.png"}</a>
						{/if}
					{else}
						{$column.label|ll:0}
					{/if}
				{/if}
			</th>
		{/foreach}
	</tr>

	{foreach from=$listItems item=row name="rows"}
	<tr class="{if $smarty.foreach.rows.index % 2}even{else}odd{/if}">
		{foreach from=$row item=value key=columnDescriptionIdentifier}
			<td class="tx-ptlist-field-{$columnDescriptionIdentifier}">{$value}</td>
		{/foreach}
	</tr>
	{foreachelse}
	<tr>
		<td colspan="{$smarty.foreach.columnHeaders.total}">
			<center>{"noItemsFound"|ll}</center>
		</td>
	</tr>
	{/foreach}

	{foreach from=$aggregateRows item=row}
	<tr class="tx-ptlist-aggregate-row">
		{foreach from=$row item=value key=columnDescriptionIdentifier}
			<td class="tx-ptlist-aggregate-{$columnDescriptionIdentifier}">{$value}</td>
		{/foreach}
	</tr>
	{/foreach}

</table>