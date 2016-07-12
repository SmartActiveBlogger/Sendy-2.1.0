<?php include('../functions.php');?>
<?php include('../login/auth.php');?>
<?php include('../../debug/debug.php');?>
<?php 
	$campaign_id = mysqli_real_escape_string($mysqli, $_POST['campaign_id']);
    //Modified - Check if campaign is scheduled but not sent, and if quota in use, update quota before deleting
    $query = "SELECT app, sent, send_date, scheduled_recipients FROM campaigns WHERE id = '".$campaign_id."'";
	$result = mysqli_query($mysqli,$query);
    if($result){
        while($row = mysqli_fetch_array($result)){
            debug_to_console($row);
            if($row['sent']=='' && $row['send_data']!=''){
                //campaign is scheduled but not sent
                $scheduled_recipients = $row['scheduled_recipients'];
                $app = $row['app'];
                //Check if monthly quota needs to be updated
                $q = 'SELECT allocated_quota, current_quota FROM apps WHERE id = '.$app;
                $r = mysqli_query($mysqli, $q);
                if($r)
                {
                    while($row = mysqli_fetch_array($r))
                    {
                        $allocated_quota = $row['allocated_quota'];
                        $current_quota = $row['current_quota'];
                        $updated_quota = $current_quota - $scheduled_recipients;
                    }
                }
                //Update quota if a monthly limit was set
                if($allocated_quota!=-1)
                {
                    //if so, update quota
                    $q = 'UPDATE apps SET current_quota = '.$updated_quota.' WHERE id = '.$app;
                    mysqli_query($mysqli, $q);
                }
            }
        }
    }

	$q = 'DELETE FROM campaigns WHERE id = '.$campaign_id.' AND userID = '.get_app_info('main_userID');
	$r = mysqli_query($mysqli, $q);
	if ($r)
	{
		$q2 = 'DELETE FROM links WHERE campaign_id = '.$campaign_id;
		$r2 = mysqli_query($mysqli, $q2);
		if ($r2)
		{
			if(file_exists('../../uploads/attachments/'.$campaign_id))
			{
				$files = glob('../../uploads/attachments/'.$campaign_id.'/*'); // get all file names
				foreach($files as $file){
				    unlink($file); 
				}
				rmdir('../../uploads/attachments/'.$campaign_id);
			}
		    echo true; 
		}
	}
	
?>