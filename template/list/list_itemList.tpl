<table class="tx-ptlist-list tx-ptlist-list-standard {$listIdentifier}">

	<tr>
		{foreach from=$columns item=column name="columnHeaders"}
		    {if is_array($structure_by_cols) }
			    {if $column.identifier|in_array:$structure_by_cols}
			        {*Don't show column header, if it's a structured column *}
			    {/if}
			{else}
			    <th class="tx-ptlist-list-header">
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
    			</th>
    	    {/if}
		{/foreach}
	</tr>
	{foreach from=$listItems item=row name="rows"}
    	<tr class="{if $smarty.foreach.rows.index % 2}even{else}odd{/if}">
    		{foreach from=$row item=value key=columnDescriptionIdentifier}
    		    {if is_array($structure_by_cols) && $columnDescriptionIdentifier|in_array:$structure_by_cols}
    		    {elseif $columnDescriptionIdentifier == '__combined_struct_col__'}
    		        {*Don't show cell, if it's a structured column*}
    		    {elseif $columnDescriptionIdentifier == '__structure_header__'}
    		        {*Generate header for structuring list*}
    		        <td class="tx-ptlist-structure-header" colspan="{$spanned_cols_by_header}">{$value}</td>
    		    {else}
    		        {*Generate "normal" cell*}
    			    <td class="tx-ptlist-field-{$columnDescriptionIdentifier}">{$value}</td>
    			{/if}
    		{/foreach}
    	</tr>
	{foreachelse}
    	<tr>
    		<td colspan="{$smarty.foreach.columnHeaders.total}">
    		    {* TODO ry21: Replace this by translation handling in pt_mvc! *}
    		    {if $no_elements_found_text != ""}
    		        <center>{$no_elements_found_text|ll:0}</center>
    		    {else}
    			    <center>{"noItemsFound"|ll}</center>
    			{/if}
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