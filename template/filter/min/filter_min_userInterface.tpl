<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.min'}">
	<input type="text" name="{$prefixId}[value]" value="{$value}" />
	<br />
	<br />
	<input type="submit" value="{$filterconf.submitValue|ll:0}" />
</form>