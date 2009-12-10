{*debug*}
<div class="tx-ptlist-datePager">
  <div class="tx-ptlist-datePager-header">{$span.header}</div>
  <span class="tx-ptlist-datePager-browse tx-ptlist-datePager-previous">
    <a href="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[prevValue]=%2$s&%1$s[mode]=prev'|vsprintf:$prefixId:$span.prevValue setup='lib.tx_ptlist.typolinks.datePager'}">
      {$span.labelPrevious}
    </a>
  </span>
  <span class="tx-ptlist-datePager-browse tx-ptlist-datePager-next">
    <a href="{url parameter=$currentPage additionalParams='&%1$s[action]=submit&%1$s[nextValue]=%2$s&%1$s[mode]=next'|vsprintf:$prefixId:$span.nextValue setup='lib.tx_ptlist.typolinks.datePager'}">
      {$span.labelNext}
    </a>
  </span>
</div>
