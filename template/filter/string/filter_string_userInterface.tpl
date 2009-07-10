<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.string'}">
	<input type="text" name="{$prefixId}[value]" value="{$value}" />
	<br />
	<br />
	<input type="submit" value="{$conf.view.tx_ptlist_view_filter_string_userInterface.submitValue|ll:0}" />
</form>
