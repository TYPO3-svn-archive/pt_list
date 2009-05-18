<div class="tx-ptlist-pagercontainer" id="ry21">
    <form name="{$pager_form_name}" action="{url parameter=$currentPage}" method="get"><ul class="tx-ptlist-pager">
        {foreach from=$hidden_fields_array item=hiddenFieldValue key=hiddenFieldName}
            <input type="hidden" name="{$hiddenFieldName}" value="{$hiddenFieldValue}" />
        {/foreach}
        {foreach from=$pager.pages item=page}
            {strip}
                {if $page.type == pageitem || $page.type == current}
                    {if $page.pageNumber == 1}
                        <select name="{$controllerName}[page]" style="float:left" onChange="{$pager_form_name}.submit()">
                    {/if}
                        {assign var="index" value=$page.pageNumber}
                        {if $index--}{/if}
                        <option value="{$page.pageNumber}" {if $page.current == 1}selected="selected"{/if}>{$dropdown_list_items[$index]}</option>
                    {if $page.pageNumber == $pager.amountPages}
                        </select>
                    {/if}
                {else $pager.amountPages > 1 || $page.type == offsetinfo}
                    <li class="pageritem {$page.type}{if $page.current} {$page.type}-current{/if} ">
                        {if $page.type == fillitem || $page.current == true}
                            <span class="unlinkedelement">
                                <span class="wrapper">{$page.label|ll:0}</span>
                            </span>
                        {elseif $page.type == offsetinfo}
                            <span class="unlinkedelement tx-ptlist-pager-offsetinfo">
                                <span class="wrapper">{'displayRows'|ll|vsprintf:$pager.offSetStart:$pager.offSetEnd:$pager.totalItemCount}</span>
                            </span>
                        {else}
                            {* ry21 use TS configuration to set up additional params *}
                            <a href="{url parameter=$currentPage additionalParams='&%1$s[page]=%2$s&%3$s'|vsprintf:$controllerName:$page.pageNumber:$addUrlParams setup='lib.tx_ptlist.typolinks.pagerLink'}">
                                <span class="wrapper">{$page.label|ll:0}</span>
                            </a>
                        {/if}
                    </li>
                {/if}
            {/strip}
        {/foreach}
    </ul></form>
</div>