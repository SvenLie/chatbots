CREATE TABLE tx_chatbots_domain_model_chatsession
(
  uid int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
  pid int(11) DEFAULT 0 NOT NULL,

  sender_token varchar(255) NOT NULL,
  access_token text NOT NULL,
  timestamp varchar(255) NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid),
);
