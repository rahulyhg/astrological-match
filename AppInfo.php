<?php

class AppInfo {
  /**
   * @return the appID for this app
   */
  public static function appID() {
    return getenv('FACEBOOK_APP_ID');
  }

  /**
   * @return the appSecret for this app
   */
  public static function appSecret() {
    return getenv('FACEBOOK_SECRET');
  }
  /**
   * @return the home URL for this site
   */
  public static function getHome () {
    return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
  }

}
