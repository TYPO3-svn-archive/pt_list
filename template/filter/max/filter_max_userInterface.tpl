<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.max'}">
	Max: 
	<input type="text" name="{$prefixId}[value]" value="{$value}" />
	<input type="submit" />
</form>