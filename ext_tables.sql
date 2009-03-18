#
# Table structure for table 'tx_ptlist_bookmarks'
#
CREATE TABLE tx_ptlist_bookmarks (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    name tinytext NOT NULL,
    list tinytext NOT NULL,
    filterstates text NOT NULL,    
	feuser blob NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_ptlist_dbs'
#
CREATE TABLE tx_ptlist_databases (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    host tinytext NOT NULL,
    db tinytext NOT NULL,
    username tinytext NOT NULL,
    pass tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);