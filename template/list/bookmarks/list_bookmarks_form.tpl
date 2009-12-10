<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=addBookmark'|vsprintf:$listPrefix setup='lib.tx_ptlist.typolinks.addBookmark'}">
	<input type="text" name="{$listPrefix}[bookmark_name]" /><br />
	<input type="submit" value="Save" />
</form>