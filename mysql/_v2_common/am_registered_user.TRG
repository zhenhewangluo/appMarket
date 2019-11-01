TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `tr_v2common_uc_members_Insert` AFTER INSERT ON `am_registered_user` FOR EACH ROW BEGIN\n \n /**\nSET @str:=rand_string(6);\n     \n\nINSERT INTO `ucenter`.`uc_members`  SET   regdate=UNIX_TIMESTAMP(),  `salt`= @str,`username`=NEW.name,`email`=NEW.email,`password`= MD5(CONCAT(NEW.`password`,@str));\n\n**/\n\nEND' 'CREATE DEFINER=`root`@`localhost` TRIGGER `tr_v2common_uc_members_Update` AFTER UPDATE ON `am_registered_user` FOR EACH ROW BEGIN\n \n /**\n\n IF NEW.`password` <> OLD.`password`  THEN  \n	SET @str:=rand_string(6);   \n update `ucenter`.`uc_members`  SET  `password`= MD5(CONCAT(NEW.`password`,@str))  where `ucenter`.`uc_members` .`email`=OLD.`email`;\n\nEND IF;\n\n**/\n\nEND'
sql_modes=0 0
definers='root@localhost' 'root@localhost'
client_cs_names='utf8' 'utf8'
connection_cl_names='utf8_general_ci' 'utf8_general_ci'
db_cl_names='utf8_general_ci' 'utf8_general_ci'
