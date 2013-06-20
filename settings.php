<?php

/**
  The license key of the library you want to upload videos to.  See the
  Libraries page in the console for a list of your account's libraries.
  You can also create a new library for this example library's videos.
*/
define ('API_LICENSE_KEY', 'f60025352e8df');

/**
   The base URL of the Twistage API, or your CNAME for this 
   URL if you've set one up.  Unless you've set up a CNAME, the value here is correct.
 */
define ('API_BASE_URL', 'http://service-staging.twistage.com');

/**
   The location of your database.  Often 'localhost'.
 */
define ('DB_LOCATION', '127.0.0.1');

/**
   The username and password of the database login.
 */
define ('DB_USER', 'root');
define ('DB_PASSWORD', ''); 

/**
   The name of the database.  For example, you might create a ugc_example_videos
   database for this example.
 */
define ('DB_NAME', 'ugc_example');

?>