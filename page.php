<html>
<head>
  <title>Upload a Video</title>
  <script type="text/javascript" src="<?= API_BASE_URL ?>/api/script"></script>
  <script type="text/javascript" src="<?= API_BASE_URL ?>/api/upload_script"></script> 
  <link rel="stylesheet" type="text/css" href="all.css"/>
</head>
<body>

  <h1><a id="home-link" href="index.php">Upload a Video</a></h1>
  
  <div class="message"> 
    <?= $pageMessage ?> 
  </div>

  <div id="upload">
	<h2>Upload a video</h2>

	<? if (isset($uploadWizard)): ?>
      <?= $uploadWizard ?>
	<? else: ?>
   
    <form action="index.php?action=upload" method="post">
		<h3><a name='toc-Enter-a-username-and-category-to-upload-a-video'> </a>Enter a username and category to upload a video:</h3>
		<div>
			<label>Username:</label>
			<input name="username"/>
		</div>
		<div>
		  <label>Category:</label>
		  <input name="category"/>
		</div>
		<input type="submit" value="Go"/>
    </form>

   <? endif; ?>
   
  </div>

  <div id="display-video">
	<? if (isset($displayVideo)): ?>
     <div>
		<?= $displayVideo->title ?> (created: <?= $displayVideo->created_at ?>)
     </div>
     <div>
		<?=$displayVideoPlayer ?>
     </div>
	<? endif; ?>
  </div>

  <div id="videos">
	  <? if ($videos): ?>

		<h2>Recent videos (click to view):</h2>

		<? foreach ($videos as $video): ?>
		  <div class="video-summary">

			 <a href="<?= $video->display_video_url ?>">
			   <img src="<?= $video->screenshot_url ?>"/> 
			 </a>

			<div class="video-summary-info">
			  <div> <label>Title:</label> <?= $video->title ?> </div>
			  <div> <label>Category:</label> <?= $video->category ?> </div>
			  <div> <label>Uploaded by:</label> <?= $video->username ?> </div>
			  <div> <label>Description:</label> <?= $video->description ?> </div>
			  <div> <label>Tags:</label> <?= $video->tags ?> </div>
			  <div> <label>Created:</label> <?= $video->created_at ?> </div>
			  <div> <a href="<?= $video->delete_link ?>">(delete)</a> </div>
			</div>
		  </div><br />
		<? endforeach; ?>

	  <? endif; ?>
  </div>



</body>
</html>