{{**
  * Javascript realizing the grid frontend
  * 
  * Hint: Delimiters (to avoid conflicts with javscript syntax) are "{{" and "}}"
  * 
  * @version 	$Id$
  * @author		Fabrizio Branca <mail@fabrizio-branca.de>
  * @since 		2009-01-28
  *}}

{{* Include Ext Js (do not change the order of following lines!) *}}

<link rel="stylesheet" type="text/css" href="fileadmin/lib/ext-2.2.1/resources/css/ext-all.css" />
<script type="text/javascript" src="fileadmin/lib/ext-2.2.1/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="fileadmin/lib/ext-2.2.1/ext-all-debug.js"></script>
<script type="text/javascript" src="fileadmin/lib/ext-2.2.1/build/locale/ext-lang-de.js"></script>

<script type="text/javascript">
/*<![CDATA[*/
<!--

Ext.onReady(function(){

	Ext.BLANK_IMAGE_URL = 'clear.gif';	

	/**
	 * Data store
	 */
    var store = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: "{{url parameter=$currentPage additionalParams='&%s[action]=fetchData&type=118&cid=%s'|vsprintf:$listPrefix:$tt_content_uid}}"
        }),

        {{* http://extjs.com/deploy/ext/docs/output/Ext.data.JsonReader.html *}}
        reader: new Ext.data.JsonReader({
            root: 'listitems',
            totalProperty: 'count',
         	successProperty: 'success',
    	}, [
    	{{strip}}
		{{foreach from=$columns item=column name="columnHeaders"}}
			{
				name: "{{$column.identifier}}",
				mapping: "{{$column.identifier}}"
			}
			{{if !$smarty.foreach.columnHeaders.last}},{{/if}}
		{{/foreach}}
		{{/strip}}
		]),

        remoteSort: true
    });   


    
	/**
	 * Grid
	 */
    var grid = new Ext.grid.GridPanel({
        // title: 'Liste',
        store: store,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true, 
   	  	region: 'center',
		layout: 'fit', 


        // grid columns
        columns:[
			{{strip}}
			{{foreach from=$columns item=column name="columnHeaders"}}
				{
					header: "{{$column.label|ll:0}}",
					dataIndex: "{{$column.identifier}}",
					sortable: {{if $column.isSortable}}true{{else}}false{{/if}},
					hidden: false,
				}
				{{if !$smarty.foreach.columnHeaders.last}},{{/if}}
			{{/foreach}}
			{{/strip}}
		],

        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:true,
        },

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: {{$itemsPerPage}},
            store: store,
            displayInfo: true,
            displayMsg: '{{"displayTopics"|ll}}',
            emptyMsg: '{{"noItemsFound"|ll}}', 
        })
    });



    /**
     * Viewport
     */
    var viewport = new Ext.Panel({
        layout: 'border',
        renderTo: '{{$element}}',
        height:500,
        width: '100%',
        items: [{
            region: 'north',
            xtype: 'panel',
            height: 27,

            // title: 'Top Filterbox',
			cls: 'tx-ptlist-northpanel',
            items: [
				{{foreach from=$topFilterbox item=filter name="filterLoop"}}
					{
						html: '{{$filter.userInterface|escape:quotes|replace:"\n":""}}',
						cls: 'tx-ptlist-filterpanel-top',
					}
					{{if !$smarty.foreach.filterLoop.last}},{{/if}}
				{{/foreach}}
			]
        }, /* {
            region: 'north',
            xtype: 'panel',
            html: 'North'
        }, */ /* {
       	  	region: 'center',
			xtype: 'panel',
			layout: 'fit',
			items: grid
		}, */
		grid,
		{
            region: 'west',
            xtype: 'panel',
            split: true,	
            autoScroll: true,
            collapsible: true,
            // title: 'Filterbox',
            width: 200,
            items: [
				{{foreach from=$defaultFilterbox item=filter name="filterLoop"}}
					{
						title: '{{$filter.label}}',
						html: '{{$filter.userInterface|escape:quotes|replace:"\n":""}}',
						cls: 'tx-ptlist-filterpanel',
						collapsible: true,
					}
					{{if !$smarty.foreach.filterLoop.last}},{{/if}}
				{{/foreach}}
			]
        }]
    });



    // render it
    grid.render();

    // trigger the data store load
    store.load({params:{start:0, limit:{{$itemsPerPage}}}});

});

// -->
	/*]]>*/
</script>