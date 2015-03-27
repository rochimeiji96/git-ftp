ALTER TABLE `project`
	ADD COLUMN `pj_git_repo` VARCHAR(255) NOT NULL DEFAULT '' AFTER `pj_ftp_dir`,
	ADD COLUMN `pj_git_user` VARCHAR(64) NOT NULL DEFAULT '' AFTER `pj_git_repo`,
	ADD COLUMN `pj_git_pass` VARCHAR(128) NOT NULL DEFAULT '' AFTER `pj_git_user`;
	ADD COLUMN `pj_git_ignore_dir` VARCHAR(128) NOT NULL DEFAULT '' AFTER `pj_git_pass`;