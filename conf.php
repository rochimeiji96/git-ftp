<?php
/*
| Configuration
*/

/*
| Directory to htdocs from git-ftp
*/
$conf['dir_htdocs'] = "../";

/*
| Realtime upload information
| Set websocket url or false to unused
*/
$conf['websocket'] = "http://nodejs-rochimeiji.rhcloud.com/pub";

/*
| Remove directory number of part
| ex : /var/www/html/project/dev/
| set : [4]
| res : /var/www/html/project
*/
$conf['rm_dir_part'] = [];