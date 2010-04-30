{* **************************************************************
 *  Copyright notice
 *  
 *  (c) 2010 Fabrizio Branca (mail@fabrizio-branca.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is 
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 * 
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 * 
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ************************************************************** *}



<table class="tx-ptlist-list tx-ptlist-list-standard {$listIdentifier} {$tableClass}">

	<thead>
		<tr class="tr-even tr-0">
			{assign var="cellCounter" value="0"}
			{foreach from=$columns item=column name="columnHeaders"}
                <th class="tx-ptlist-list-header td-{$cellCounter}">
                    {if $column.isSortable}
                        {if $column.sortingState == 0}{* not sorted *}
                            <a href="{url parameter=$currentPage additionalParams='&%4$s&%1$s[sorting_column]=%2$s&%1$s[sorting_direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'1':$appendToSortingUrl setup='lib.tx_ptlist.typolinks.columnSortLinks'}">
                            	{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_default.png"}
                           	</a>
                        {elseif $column.sortingState == 1}{* ascending sorted *}
                            <a href="{url parameter=$currentPage additionalParams='&%4$s&%1$s[sorting_column]=%2$s&%1$s[sorting_direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'-1':$appendToSortingUrl setup='lib.tx_ptlist.typolinks.columnSortLinks'}">
                            	{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_desc.png"}
                            </a>
                        {elseif $column.sortingState == -1}{* descending sorted *}
                            <a href="{url parameter=$currentPage additionalParams='&%4$s&%1$s[sorting_column]=%2$s&%1$s[sorting_direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'1':$appendToSortingUrl setup='lib.tx_ptlist.typolinks.columnSortLinks'}">
                            	{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_asc.png"}
                            </a>
                        {/if}
                    {else}
                        {$column.label|ll:0}
                    {/if}
                </th>    
                {assign var="cellCounter" value=$cellCounter+1}   	
			{/foreach}
		</tr>
	</thead>
	
	<tbody>
	{assign var="rowCounter" value="1"}
	{foreach from=$listItems item=row name="rows"}
    	<tr class="tr-{if $smarty.foreach.rows.index % 2}even{else}odd{/if} tr-{$rowCounter}">
    		{assign var="cellCounter" value="0"}
    		{foreach from=$row item=value key=columnDescriptionIdentifier}
				<td class="tx-ptlist-field-{$columnDescriptionIdentifier} td-{$cellCounter}">{$value}</td>
				{assign var="cellCounter" value=$cellCounter+1}  
    		{/foreach}
    	</tr>
       {assign var="rowCounter" value=$rowCounter+1} 
	{/foreach}

	</tbody>

</table>