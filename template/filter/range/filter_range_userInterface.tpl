<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.range'}">
    <label for="{$prefixId}[minval]">{"filterRangeMin"|ll}</label><input type="text" name="{$prefixId}[minval]" value="{$minval}" size="3" /> 
    <label for="{$prefixId}[maxval]">{"filterRangeMax"|ll}</label><input type="text" name="{$prefixId}[maxval]" value="{$maxval}" size="3" />
    <br />
    <br />
    <input type="submit"  value="{$filterconf.submitValue|ll:0}"/>
</form>
