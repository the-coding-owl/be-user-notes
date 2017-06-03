#
# Table structure for table 'sys_note_viewed'
#
CREATE TABLE sys_note_viewed (
    be_user int(11) unsigned DEFAULT '0' NOT NULL,
    sys_note int(11) unsigned DEFAULT '0' NOT NULL,
    viewed boolean DEFAULT '0' NOT NULL,
    PRIMARY KEY (be_user,sys_note),
    KEY be_user (be_user),
    KEY sys_note (sys_note)
);

#
# Table structure for table 'sys_note'
#
CREATE TABLE sys_note (
    owner int(11) unsigned DEFAULT '0' NOT NULL,
    viewed boolean DEFAULT '0' NOT NULL,
    KEY owner (owner)
);