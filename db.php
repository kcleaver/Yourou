<?php

/**
  Connects the the local database.
 */
function db_connect() {
  mysql_connect (DB_LOCATION, DB_USER, DB_PASSWORD);
  if (! mysql_select_db (DB_NAME)) {
    die ("Couldn't connect to the database.");
  }
}

/**
  Convenience function to run queries easily
  */
function runQuery($query) {
    $conn = mysql_connect(DB_LOCATION, DB_USER, DB_PASSWORD);
	$test = ("USE " . DB_NAME);
    mysql_query($test);
    $result = mysql_query($query);
    mysql_close($conn);
    return $result;
}

/**
  Returns a PHP array of videos obtained from the database.
  Each entry in he array is a PHP object with properties
  coresponding to the database columns, e.g. $video->vid.  
 */
function db_getVideos() {

  $result = mysql_query("select * from videos where status='available' order by created_at desc")
    or die ("Couldn't query database.");

  if (! $result) {
    throw new Exception ("Couldn't query database.");
  }

  $videos = array();
  while ($video = mysql_fetch_object($result)) {
    $videos[] = $video;
  }

  return $videos;
}

/**
  Sets the given video's status to 'trash' in the database.
 */
function db_setStatusToTrash ($video_id) {
  mysql_query ("update videos set status='trash' where video_id='$video_id'")
    or die ("Couldn't update video in database: video_id=$video_id, status=trash");
}


?>