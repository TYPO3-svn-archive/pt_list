<ul>
{foreach from=$bookmarks item=bookmark}
	<li><a href="{url parameter=$currentPage additionalParams='&%1$s[action]=loadBookmark&%1$s[bookmark_uid]=%2$s'|vsprintf:$listPrefix:$bookmark.uid setup='lib.tx_ptlist.typolinks.bookmarks'}">{$bookmark.name}</a></li>
{/foreach}
</ul>