<?php

/**
  $contributor: whatever string you want to use as the contributor for the video.
  $custom: a PHP array of custom configuration parameters, including any custom
           data fields you want to have passed to your hook.
  $redirectUrl: the URL to redirect to when the upload wizard is done.

  returns the text of the upload wizard embed code.

  Calls getIngestAuth() for you to get an ingest authenticaiton signature.
*/

function createUploadWizard ($contributor, $custom, $redirectUrl) {

  // get a one-time ingest auth token for this contributor
  $authSignature = getIngestAuth($contributor);

  // create a json-encoded string from the custom data field array
  $customAsJson = json_encode($custom);

  // generate a <script> tag for the upload wizard, specifying
  // the category as metadata
  return <<<DOC

    <script type="text/javascript"> 
      uploadWizard('$authSignature', '$redirectUrl', $customAsJson);		    
    </script> 

DOC;
}

/**
  $videoId: the ID of the video to play.
  $custom: a PHP array of custom configuration parameters for the layer, like
           width and height.

  returns the text of the embed code for the video player.
 */
function createPlayer ($videoId, $custom) {
  $customAsJson = json_encode ($custom);
  return <<<DOC

    <script type="text/javascript">      
      viewNode("$videoId", $customAsJson);
    </script>

DOC;
}

/**
  Returns a view authentication signature, using the constant API_BASE_URL
  and API_LICENSE_KEY.  
 */
function getViewAuth () {
  return file_get_contents (API_BASE_URL . "/api/view_key?licenseKey=" . API_LICENSE_KEY);
}

/**
  Returns an update authentication signature, using the constant API_BASE_URL
  and API_LICENSE_KEY.  
 */
function getUpdateAuth() {
  return file_get_contents (API_BASE_URL . "/api/update_key?licenseKey=" . API_LICENSE_KEY);
}

/**
  Returns a view authentication signature, using the constant API_BASE_URL
  and API_LICENSE_KEY, for the given $contributor, which can be any string.
 */
function getIngestAuth($contributor=null) {
  return file_get_contents (API_BASE_URL . "/api/ingest_key?licenseKey=" . API_LICENSE_KEY . 
			    "&contributor=$contributor&library_id=testlibrary");
}

/**
  Returns the URL of the given video's stillframe for the given width.
 */
function createVideoScreenshotUrl ($videoId, $width) {
  return API_BASE_URL . "/videos/${videoId}/screenshots/${width}w.jpg";
}

/**
  Returns a PHP object representing the given video including all its metadata
  and its assets.
 */
function getVideoMetadata($videoId) {
  return json_decode (file_get_contents (API_BASE_URL . "/videos/$videoId.json" . 
					 "?signature=" . getViewAuth()));
}

/**
  Calls the Video Delete API, deleting the given video.
 */
function deleteVideo($videoId) {
  
  $url = API_BASE_URL . "/videos/$videoId?signature=" . getUpdateAuth();

  // issue HTTP DELETE request using PHP's lib curl
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-length: 0"));

  // get response info and close the connection
  $response = curl_exec($ch);
  $info = curl_getinfo($ch);
  $error = curl_error($ch);
  curl_close($ch);    

  // if there was a problem, blow up (delete video api currently redirects)
  if (! $response) {
    throw new Exception ("CURL request for url $url failed.  HTTP code = " . $info['http_code'] . ", CURL error = " . $error);
  }
}

?>