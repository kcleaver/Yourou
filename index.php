<?php

require_once ('settings.php');
require_once ('api.php');
require_once ('db.php');

// a convenience constant
define ('THIS_PAGE_URL', "http://" . $_SERVER['SERVER_NAME'] . ":8001" . $_SERVER['SCRIPT_NAME']);

// connect to db, get all videos in the local database
db_connect();
$videos = db_getVideos();

// put some extra information on the video objects for the page
foreach ($videos as $video) {

  // the video's screenshot url
  $video->screenshot_url = createVideoScreenshotUrl ($video->video_id, 150);

  // add a url to the video object for its "display video" link
  $video->display_video_url = "index.php?action=display_video&display_video_id=$video->video_id";
  
  // the video's delete link
  $video->delete_link = "index.php?action=delete&video_id=$video->video_id";
}

// checks if an action has been passed
if(isset($_REQUEST['action']))
{	
	// respond to the action request parameter
	switch($_REQUEST['action']) {	
		// user submits username and category to upload video
		//case 'upload': 
		
	      // if user didn't specify username or category, display a message
		  //if (empty($_REQUEST['username']) || empty($_REQUEST['category'])) {
			//$pageMessage = "Please specify a username and category for the video.";
		  //}
		  //else { 
			// generate the embed code for an upload wizard, passing in the category
			// as a custom data field to be returned to the callback hook
			//$uploadWizard = createUploadWizard ($_REQUEST['username'],
					  //array ('category' => $_REQUEST['category']),
					  //THIS_PAGE_URL . "?action=upload_finished");
		  //}
		  //break;
		  
		// called in redirect from upload wizard after uploading

	         case 'upload': 

	   		if (empty($_REQUEST['username']) || empty($_REQUEST['category'])) {
			$pageMessage = "Please specify a username and category for the video.";
			}

	   		$uploadWizard = createUploadWizard ($_REQUEST['username'],
					  array ('category' => $_REQUEST['category']),
					  THIS_PAGE_URL . "?action=upload_finished");
	   		break;


		case 'upload_finished':
		if(isset($_REQUEST['_op']) && $_REQUEST['_op'] == 'Cancel'){
		  $pageMessage = 'Upload Canceled';
		  }else{
		  $pageMessage = 'Your video has been uploaded and will show up at the top ' .
			'of the list in a few minutes.';
		  }

		  break; 
		  
		// called when user clicks a video to play it
		case 'display_video':

		  // go through the videos we read from the database
		  // and set a variable $displayVideo to the selected video, for use in the page
		  foreach ($videos as $video) {      
			if ($_REQUEST['display_video_id'] == $video->video_id) {
				$displayVideo = $video;
			}
		  }
		  
		  // create the embed code for the selected video's player,
		  // for use in the page
		  $displayVideoPlayer = createPlayer ($displayVideo->video_id, 
											  array ('server_detection' => true,
													 'width' => 480, 
													 'height' => 383,
													 'config' => array('config' => array ('autoplay' => 'true'))));
		break;
		
		// admin user deletes a video
		case 'delete': 

		  // call the Delete Video API to set its status to 'trash' in your account 
		  deleteVideo($_REQUEST['video_id']);

		  // set its status to 'trash' in the local database so the page doesn't have to wait
		  // for the hook call to remove the video from the list
		  db_setStatusToTrash ($_REQUEST['video_id']);    

		  // since we've already queried the database for the page's videos,
		  // remove the deleted one from the array so it isn't there when the page renders
		  $oldVideos = array_merge($videos);
		  $videos = array();
		  foreach ($oldVideos as $video) {      
			if ($_REQUEST['video_id'] != $video->video_id) {
			  $videos[] = $video;
			}
		  }

			  $pageMessage = 'Your video has been moved to the trash.';

		break;
	}
}

if (! isset($pageMessage)) {
  $pageMessage = "Welcome to Lexmark Video Upload portal, where you upload videos.";
}

// render the page
include ('page.php');

?>