<div class="tx-ptlist-pagercontainer">
    <ul class="tx-ptlist-pager">
        {foreach from=$pager.pages item=page}
           {strip}
                {if $pager.amountPages > 1 || $page.type == offsetinfo}
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
                            <a href="{url parameter=$currentPage additionalParams='%3$s&%1$s[page]=%2$s'|vsprintf:$listPrefix:$page.pageNumber:$appendToUrl setup='lib.tx_ptlist.typolinks.pagerLink'}">
                                <span class="wrapper">{$page.label|ll:0}</span>
                            </a>
                        {/if}
                    </li>
                {/if}
            {/strip}
        {/foreach}
    </ul>
</div>