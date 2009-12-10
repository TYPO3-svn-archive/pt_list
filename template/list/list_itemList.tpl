{*

/***************************************************************
 *  Copyright notice
 *  
 *  (c) 2009 Fabrizio Branca, Michael Knoll (mail@fabrizio-branca.de, knoll@punkt.de)
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
 ***************************************************************/

*}


<table class="tx-ptlist-list tx-ptlist-list-standard {$listIdentifier}">


    {******** List headers for columns ********}


	<tr>
		{foreach from=$columns item=column name="columnHeaders"}
		    {if is_array($structure_by_cols) } {* Lazy evaluation is not working here!!! *}
			    {if $column.identifier|in_array:$structure_by_cols}
			        {*Don't show column header, if it's a structured column *}
    			{else}
    			    {* Column headers for structured lists *}
    			    <th class="tx-ptlist-list-header">
    			        {*Structured lists are NOT sortable!!!*}
        				{$column.label|ll:0}
        			</th>
        	    {/if}
        	{else}
        	    {* Column headers for non-structured lists *}
                <th class="tx-ptlist-list-header">
                    {if $column.isSortable}
                        {if $column.sortingState == 0}{* not sorted *}
                            <a href="{url parameter=$currentPage additionalParams='&%4$s&%1$s[sorting_column]=%2$s&%1$s[sorting_direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'1':$appendToSortingUrl setup='lib.tx_ptlist.typolinks.columnSortLinks'}">{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_default.png"}</a>
                        {elseif $column.sortingState == 1}{* ascending sorted *}
                            <a href="{url parameter=$currentPage additionalParams='&%4$s&%1$s[sorting_column]=%2$s&%1$s[sorting_direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'-1':$appendToSortingUrl setup='lib.tx_ptlist.typolinks.columnSortLinks'}">{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_desc.png"}</a>
                        {elseif $column.sortingState == -1}{* descending sorted *}
                            <a href="{url parameter=$currentPage additionalParams='&%4$s&%1$s[sorting_column]=%2$s&%1$s[sorting_direction]=%3$s'|vsprintf:$listPrefix:$column.identifier:'1':$appendToSortingUrl setup='lib.tx_ptlist.typolinks.columnSortLinks'}">{$column.label|ll:0} {image file="EXT:pt_list/res/icon_table_sort_asc.png"}</a>
                        {/if}
                    {else}
                        {$column.label|ll:0}
                    {/if}
                </th>       	
        	{/if}
		{/foreach}
	</tr>
	
	
	{******** List rows ********}
	
	
	{assign var="odd_even" value="odd"}
	{assign var="firstHeader" value="1"}
	{foreach from=$listItems item=row name="rows"}
	
	    {if $row.is_structure_header == 1}
	       {if $firstHeader > 1}
	           <tr><td class="fill" colspan="{$spanned_cols_by_header}">&nbsp;</td></tr>
	           <tr class="odd">
	           {assign var="odd_even" value="odd"}
	       {else}
	           {assign var="firstHeader" value="2"}
	       {/if}
	    {else}
	       {if $odd_even == "odd"}<tr class="odd">{else}<tr class="even">{/if}
	    {/if}
	
    	{* <tr class="{if $smarty.foreach.rows.index % 2}even{else}odd{/if}"> *}
    	
    	
    	    {******** List cells ********}
    	
    	
    		{foreach from=$row item=value key=columnDescriptionIdentifier}
    		    {if is_array($structure_by_cols)}
    		        {if $columnDescriptionIdentifier|in_array:$structure_by_cols}
        		    {elseif $columnDescriptionIdentifier == '__combined_struct_col__'}
        		        {*Don't show cell, if it's a structured column*}
    	   	        {elseif $columnDescriptionIdentifier == '__structure_header__'}
    		            {*Generate header for structuring list*}
    		            <td class="tx-ptlist-structure-header" colspan="{$spanned_cols_by_header}">{$value}</td>
    		        {else}
    		            {if $row.is_structure_header == '1'}
    		            {else}
    		                {*Generate "normal" cell*}
			                <td class="tx-ptlist-field-{$columnDescriptionIdentifier}">{$value}</td>
			            {/if}
    			    {/if}
    			{else}
    			    {*Generate "normal" cell*}
                    <td class="tx-ptlist-field-{$columnDescriptionIdentifier}">{$value}</td>
                {/if}
    		{/foreach}
    	</tr>
    	{if $odd_even == "odd"}
    	   {assign var="odd_even" value="even"}
    	{else}
    	   {assign var="odd_even" value="odd"}
    	{/if}
        
        
	{foreachelse}
    
	    {******** No elements found ********}
        
        
    	<tr>
    		<td colspan="{$smarty.foreach.columnHeaders.total}">
    		    {* TODO ry21: Replace this by translation handling in pt_mvc! *}
    		    {if $noElementsFoundText != ""}
    		        <center>{$noElementsFoundText|ll:0}</center>
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