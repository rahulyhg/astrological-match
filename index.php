<?php
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  exit();
}

require_once('FBUtils.php');
require_once('AppInfo.php');
require_once('utils.php');

$token = FBUtils::login(AppInfo::getHome());
if ($token) {
  $basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
  $my_id = assertNumeric(idx($basic, 'id'));

  $app_id = AppInfo::appID();
  $app_info = FBUtils::fetchFromFBGraph("$app_id?access_token=$token");
  
  $friends = FBUtils::fql(
    "SELECT uid, name, pic_square, birthday_date FROM user WHERE uid in (SELECT uid2 FROM friend WHERE uid1 = me()) ORDER BY birthday_date",
    $token
  );
  
  $info = FBUtils::fetchFromFBGraph("me?access_token=$token", 'data');
  $birthday = $info["birthday"];

  $encoded_home = urlencode(AppInfo::getHome());
  $redirect_url = $encoded_home . 'close.php';

  $send_url = "https://www.facebook.com/dialog/send?redirect_uri=$redirect_url&display=popup&app_id=$app_id&link=$encoded_home";
  $post_to_wall_url = "https://www.facebook.com/dialog/feed?redirect_uri=$redirect_url&display=popup&app_id=$app_id";
  
  date_default_timezone_set('UTC');
  $todays_date = date("m/d");
  
	$aquarius = array();
	$pisces = array();
	$aries = array();
	$taurus = array();
	$gemini = array();
	$cancer = array();
	$leo = array();
	$virgo = array();
	$libra = array();
	$scorpio = array();
	$sagitarius = array();
	$capricorn = array();
	
	foreach ($friends as $friend) {
		$name = idx($friend, 'name');
		$birthday = idx($friend, 'birthday_date');
		if (!empty($birthday)) {
			if (('01/21' <= $birthday) && ($birthday <= '02/19')) {
				$aquarius[] = $friend;
			} else if (('02/20' <= $birthday) && ($birthday <= '03/20')) {
				$pisces[] = $friend;
			} else if (('03/21' <= $birthday) && ($birthday <= '04/20')) {
				$aries[] = $friend;
			} else if (('04/21' <= $birthday) && ($birthday <= '05/20')) {
				$taurus[] = $friend;
			} else if (('05/21' <= $birthday) && ($birthday <= '06/21')) {
				$gemini[] = $friend;
			} else if (('06/22' <= $birthday) && ($birthday <= '07/22')) {
				$cancer[] = $friend;
			} else if (('07/23' <= $birthday) && ($birthday <= '08/23')) {
				$leo[] = $friend;
			} else if (('08/24' <= $birthday) && ($birthday <= '09/22')) {
				$virgo[] = $friend;
			} else if (('09/23' <= $birthday) && ($birthday <= '10/23')) {
				$libra[] = $friend;
			} else if (('10/24' <= $birthday) && ($birthday <= '11/22')) {
				$scorpio[] = $friend;
			} else if (('11/23' <= $birthday) && ($birthday <= '12/21')) {
				$sagitarius[] = $friend;
			} else if ((('12/22' <= $birthday) && ($birthday <= '12/31'))
				|| (('01/01' <= $birthday) && ($birthday <= '01/20'))) {
				$capricorn[] = $friend;
			}
		}
	}

	function showFriends($list, $type) {
		for ($i = 0; $i < 4; $i++) {
			$birthday = $list[$i]["birthday_date"];
			$id = $list[$i]["uid"];
			$name = $list[$i]["name"];
			$picture = $list[$i]["pic_square"];
			
			if (isset($list[$i])) {
				echo('
					<a href="#" onclick="window.open(\'http://www.facebook.com/' . $id . '\')">
					<img src="https://graph.facebook.com/' . $id . '/picture?type=square" alt="' . $name . '">
					</a>');
			} else {
				break;
			}
		}
		echo ('<span class="count">Total ' . $type . ' friends: ' . count($list) . '</span');
	}
} else {
  exit("Invalid credentials");
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <title><?php echo(idx($app_info, 'name')) ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="screen">

    <!-- Open Graph tags. -->
    <meta property="og:title" content=""/>
    <meta property="og:type" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:site_name" content=""/>

    <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
    <script>
      function popup(pageURL, title,w,h) {
        var left = (screen.width/2)-(w/2);
        var top = (screen.height/2)-(h/2);
        var targetWin = window.open(
          pageURL,
          title,
          'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left
          );
      }
    </script>
  </head>
  <body>
    <header class="clearfix">
      <p id="picture" style="background-image: url(https://graph.facebook.com/me/picture?type=normal&access_token=<?php echoEntity($token) ?>)"></p>

      <div>
        <h1>Welcome, <strong><?php echo idx($basic, 'name'); ?></strong></h1>
		
        <div id="share-app">
          <p>Share this app:</p>
          <ul>
            <li>
              <a href="#" class="facebook-button" onclick="popup('<?php echo $post_to_wall_url ?>', 'Post to Wall', 580, 400);">
                <span class="plus">Post to Wall</span>
              </a>
            </li>
            <li>
              <a href="#" class="facebook-button speech-bubble" onclick="popup('<?php echo $send_url ?>', 'Send', 580, 400);">
                <span class="speech-bubble">Send to Friends</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </header>

	<section>
	Check out the astrological signs of your friends!
	</section>
	<section id="guides" class="clearfix">
		<p>
		<img src="images/aquarius.png" />
		<?php showFriends($aquarius, 'Aquarius'); ?>
		</p>
		<p>
		<img src="images/pisces.png" />
		<?php showFriends($pisces, 'Pisces'); ?>
		</p>
		<p>
		<img src="images/aries.png" />
		<?php showFriends($aries, 'Aries'); ?>
		</p>
		<p>
		<img src="images/taurus.png" />
		<?php showFriends($taurus, 'Taurus'); ?>
		</p>
		<p>
		<img src="images/gemini.png" />
		<?php showFriends($gemini, 'Gemini'); ?>
		</p>
		<p>
		<img src="images/cancer.png" />
		<?php showFriends($cancer, 'Cancer'); ?>
		</p>
		<p>
		<img src="images/leo.png" />
		<?php showFriends($leo, 'Leo'); ?>
		</p>
		<p>
		<img src="images/virgo.png" />
		<?php showFriends($virgo, 'Virgo'); ?>
		</p>
		<p>
		<img src="images/libra.png" />
		<?php showFriends($libra, 'Libra'); ?>
		</p>
		<p>
		<img src="images/scorpio.png" />
		<?php showFriends($scorpio, 'Scorpio'); ?>
		</p>
		<p>
		<img src="images/sagitarius.png" />
		<?php showFriends($sagitarius, 'Sagitarius'); ?>
		</p>
		<p>
		<img src="images/capricorn.png" />
		<?php showFriends($capricorn, 'Capricorn'); ?>
		</p>		
	</section>
  </body>
  </body>
</html>
