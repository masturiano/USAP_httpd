<?php
# Set default time zone
date_default_timezone_set('Asia/Manila'); // CDT
# Include file
include('function.php');
# Database connection
$db_connection = mysql_connect('localhost','root','');
if (!$db_connection)
{
    die('Could not connect: ' . mysql_error());
}
else
{
    # Use database
    mysql_select_db(usap_feeds_file);
    $query_select = "
        select 
            sc.server_id,
            sc.username,
            sc.password,
            si.server_name,
            si.ip_address,
            at.type_id,
            at.type_name,
            ai.account_id,
            ai.account_name,
            ai.account_description,
            ai.folder_location_backup,
            ai.folder_location_latest,
            ai.folder_location_rollback,
            ai.file_size_reference,
            ai.record_count_reference
        from 
            server_credentials sc
        left join
            server_info si 
            on si.server_id = sc.server_id
        left join
            account_info ai
            on ai.server_id = sc.server_id
        left join
            account_type at
            on at.type_id = ai.type_id
        where 
            sc.server_id = 1
        order by
            si.server_name,
            at.type_name,
            ai.account_name;
    ";
    $query_execute = mysql_query($query_select);
}

// mysql_close($link);

# Start ssh2 connection to server
if($ssh = ssh2_connect('174.129.233.71', 22)) 
{
    # Start ssh2 connection credentials
    if(ssh2_auth_password($ssh, 'masturiano', 'masturianousap1q2w')) 
    {
        # ssh2 sftp connection
        $sftp = ssh2_sftp($ssh);
        # loop table server credentials
        while ($fetch_array_row = mysql_fetch_array($query_execute)) 
        {
            # Display account name
            echo "<b>ACCOUNT NAME : </b>";
            echo $fetch_array_row['account_name'];
            echo "<br/>";
            echo "<b>TYPE : </b>";
            echo $fetch_array_row['type_name'];
            # field assign to variable
            $folder_location_latest = $fetch_array_row['folder_location_latest'];
            # file size reference
            $file_size_reference = $fetch_array_row['file_size_reference'];
            # open account folder location
            if ($dir_handler = opendir("ssh2.sftp://$sftp/$folder_location_latest/")) 
            {
                ?>
                    <table border=1>
                        <tr>
                            <td><b>FILENAME</b></td>
                            <td><b>SIZE</b></td>
                            <td><b>SIZE REFERENCE</b></td>
                            <td><b>DATE CREATED</b></td>
                            <td><b>TIME CREATED</b></td>
                        </tr>
                <?php
                while ($file = readdir($dir_handler)) 
                {
                    $file_explode_by_dot = explode('.',$file);
                    $file_num_count = (count($file_explode_by_dot)-1);
                    $file_extension = $file_explode_by_dot[$file_num_count ];
                    $file_extension = substr($file_extension,0,3);
                    $file_extension = strtolower($file_extension);
                    if($file_extension == "txt" || $file_extension == "zip")
                    {
                        ?>
                        <tr>
                        <?php
                        #file name
                        $filename = $file;
                        ?>
                            <td><?=$filename;?></td>
                        <?php
                        # file info
                        $file_info = "ssh2.sftp://$sftp/$folder_location_latest/$file";
                        # file size
                        $file_size = filesize($file_info);
                        # file created
                        $file_created = filemtime($file_info);
                        # file date created
                        $file_date_created = date('F d, Y',($file_created));
                        # file time created
                        $file_time_created = date('h:i:s A',($file_created));
                        # file line count
                        /*
                        $file_line_count = 0;
                        $file_handler = fopen($file_info, "r");
                        while(!feof($file_handler))
                        {
                            $line = fgets($file_handler);
                            $file_line_count++;
                        }
                        fclose($file_handler);
                        if($file_size == '0 bytes')
                        {
                            $file_line_count = 0;
                        }
                        else
                        {
                            $file_line_count = (int)$file_line_count - 1;
                        }
                        */
                        ?>
                            <td><?=formatSizeUnits($file_size);?></td>
                            <?php
                            if($file_extension == "txt")
                            {
                            ?>
                                <td><?=formatSizeUnits($file_size_reference);?></td>
                            <?php
                            }
                            else
                            {
                            ?>
                                <td><? echo " ";?></td>
                            <?php
                            }
                            ?>
                            <td><?=$file_date_created;?></td>
                            <td><?=$file_time_created;?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                    </table>
                    <br/>
                <?php
            }
            else
            {
                echo "Failed to open the directory.";
                echo "<br/>";
            }
        }
    }
    else
    {
        echo "Failed to connect using the account.";
    }
}
else
{
    echo "Failed to connect to remote server.";
}
?>