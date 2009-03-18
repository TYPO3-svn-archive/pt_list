<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.range'}">
    {"filterRangeMin"|ll}<input type="text" name="{$prefixId}[minval]" value="{$minval}" size="3" /> 
    {"filterRangeMax"|ll}<input type="text" name="{$prefixId}[maxval]" value="{$maxval}" size="3" />
    <br />
    <br />
    <input type="submit" />
</form>
