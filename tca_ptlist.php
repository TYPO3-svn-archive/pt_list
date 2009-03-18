<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$TCA["tx_ptlist_bookmarks"] = array (
    "ctrl" => $TCA["tx_ptlist_bookmarks"]["ctrl"],
    "interface" => array (
        "showRecordFieldList" => "hidden,name,list,filterstates,feuser"
    ),
    "feInterface" => $TCA["tx_ptlist_bookmarks"]["feInterface"],
    "columns" => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        "name" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_bookmarks.name",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "list" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_bookmarks.list",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "filterstates" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_bookmarks.filterstates",        
            "config" => Array (
                "type" => "none",
            )
        ),
        "feuser" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_bookmarksfe.user",        
            "config" => Array (
                "type" => "group",    
                "internal_type" => "db",    
                "allowed" => "fe_users",    
                "size" => 1,    
                "minitems" => 0,
                "maxitems" => 1,
            )
        ),
    ),
    "types" => array (
        "0" => array("showitem" => "hidden;;1;;1-1-1, name, list, filterstates, feuser")
    ),
    "palettes" => array (
        "1" => array("showitem" => "")
    )
);


$TCA["tx_ptlist_databases"] = array (
    "ctrl" => $TCA["tx_ptlist_databases"]["ctrl"],
    "interface" => array (
        "showRecordFieldList" => "hidden,host,db,username,pass"
    ),
    "feInterface" => $TCA["tx_ptlist_databases"]["feInterface"],
    "columns" => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        "host" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_databases.host",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",    
                "eval" => "required",
            )
        ),
        "db" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_databases.db",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",    
                "eval" => "required",
            )
        ),
        "username" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_databases.username",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",    
                "eval" => "required",
            )
        ),
        "pass" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_databases.pass",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",    
                "eval" => "pass",
            )
        ),
    ),
    "types" => array (
        "0" => array("showitem" => "hidden;;1;;1-1-1, host, db, username, pass")
    ),
    "palettes" => array (
        "1" => array("showitem" => "")
    )
);


?>