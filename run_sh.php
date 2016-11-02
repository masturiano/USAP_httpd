<?php
//$output = shell_exec('ls -lart');
//echo "<pre>$output</pre>";

// $commands = explode("\n",$commands);
// foreach($commands AS $command) {
//   $stuff = shell_exec($command);
//   //Do stuff with the $stuff
// }

// $mongo_start = 'mongo';
// $mongo_show_database = 'mongo';

// $separator = ' && ';

// $output = shell_exec(
// 	$mongo_start//.
// 	//$separator.
// 	//$mongo_show_database
// );

//echo "<pre>$output</pre>";




// $connection = ssh2_connect('10.10.216.51', 22);
// ssh2_auth_password($connection, 'masturiano', 'ArannNino@1509');
// $stream = ssh2_exec($connection, 'whoami');
// var_dump($stream);

// 10.10.216.51 // my local
// 174.129.233.96 // gen03
//if($ssh = ssh2_connect('174.129.233.96', 22)) {

//SUCCESS COMMAND
if($ssh = ssh2_connect('174.129.233.96', 22)) {
    if(ssh2_auth_password($ssh, 'masturiano', 'masturianousap1q2w')) {
        $stream = ssh2_exec($ssh, 'whoami');
        stream_set_blocking($stream, true);
        $data = '';
        while($buffer = fread($stream, 4096)) {
            $data .= $buffer;
        }
        fclose($stream);
        echo $data; // user
    }
}

// if($ssh = ssh2_connect('174.129.233.96', 22)) {
//     if(ssh2_auth_password($ssh, 'masturiano', 'masturianousap1q2w')) {
    	
// 		$sftp = ssh2_sftp($ssh);
// 		$handle = opendir("ssh2.sftp://$sftp/data/aopi/feeds_longbow/output/dam_ci_details/latest/");
// 		echo "Directory handle: $handle\n";
// 		echo "Entries:\n";
// 		while (false != ($entry = readdir($handle))){
// 		    echo "$entry\n";
// 		}

//     }
// }

?>