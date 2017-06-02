#
# Table structure for table 'user_sys_note'
#
CREATE TABLE user_sys_note (
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
    be_user int(11) unsigned DEFAULT '0' NOT NULL,
    viewed boolean DEFAULT '0' NOT NULL,
    KEY be_user (be_user)
);