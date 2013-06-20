<?php

require_once ('settings.php');
require_once ('api.php');
require_once ('db.php');

db_connect();

// the hook is always called with an action POST variable
switch($_POST[action]) {
  
 // called when a video is first created (uploaded), before transcoding
  case "create":
    $now = date('Y-m-d G:i:s');
    $query = "INSERT INTO videos (video_id, created_at, title, username, category)
                   VALUES ('$_POST[vid]', '$now', '$_POST[title]', '$_POST[contributor]', '$_POST[category]')";
    runQuery($query);
    break;

 // called whenever any change to a video occurs.
 // when the action pertains to a specific video, the request also contains
 // an asset_action request variable.  in this scenario we're not interested
 // in asset-level actions, only in video-level actions like changed video metadata
 // and when the video's status changes.
  case 'update':
	
    // call the video metadata api
	$metadata = getVideoMetadata($_POST[vid]);
    // get the username from the video's metadata
	$username = $metadata->contributor;
    // get the video's description from the metadata
	$description = $metadata->description;
    // collect the videos' tags' names
	$tags = array();
	
	foreach ($metadata->tags as $tag) {
	  $tags[] = $tag->name;
	}
    // for storage in the database as a comma-delimited string
	$tagsString = join(', ', $tags);
	
    // update the video's info in the local database
	$query = "UPDATE videos SET title='$_POST[title]', status='$_POST[status]', description='$description',
				username='$username', tags='$tagsString' where video_id='$_POST[vid]'";
	
	runQuery($query);
	break;
  // called when the video's processing finishes and makes the video available for update  
  case "ready":
    runQuery("UPDATE videos SET status = '$_POST[status]' WHERE video_id = '$_POST[vid]'");
    break;

  // called when an error occurs during the video's processing	
  case "error":
    error_log("An error occurred processing the Twistage video with ID: $_POST[vid]");
    runQuery("UPDATE videos SET status = '$_POST[status]' WHERE video_id = '$_POST[vid]'");
    break;

 // called when a video is deleted from the trash.
 // videos that are deleted are initially just given a status of 'trash',
 // so at first just the 'update' action will be called.  after a week
 // with a status of 'trash', the video will be sweeped permanently from the console
 // and this delete hook action will be called.
  case "delete":
    runQuery("DELETE FROM videos WHERE video_id = '$_POST[vid]'");
    break;
     
 default:
    header("HTTP/1.0 400 Server Error");
}

function debug($message) {
    // comment this out once the hook has been implemented and tested
    // if (true) print $message;
}

?>