<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Glimmer</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
<style>
body {font-family: 'Open Sans', sans-serif;font-size:15px;color:rgb(0,0,0);margin:0;padding:0;line-height: 22px; background:#fff;font-weight:300;word-wrap:break-word;}
.header {text-align:center;background:#231f20;padding:10px;}
h1 {text-align:center;font-weight:300;color:rgb(34,44,47)}
h1 small {font-size:15px;}
h3 {margin-bottom:5px;color:rgb(34,44,47)}
a {color:#000;}
nav ul {background:#231f20;margin:0;padding:10px 0;text-align:center;}
nav ul li {display:inline-block;}
nav ul li a {color:#fff;text-decoration:none;padding:5px;font-size:15px;}
nav ul li a:hover {color:#ccc;}
li {list-style:none;}
section {padding:0 20px;}
section p {margin-top:0;}
ol li p {margin-left:17px;}



</style>
</head>

<body>
<h1>Frequently Asked Questions</h1>

<div class="container">
	<section>
        <h2 ng-bind-html="trustHtml(section.title)">Top Questions</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">My messages are not going through.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Please make sure you have a strong internet connection. On iOS, try tapping the exclamation mark and hit resend. On Android, you can just tap the message to try sending it again. You can also try closing the app. If the issue persists, please wait a few hours and try again at a later time. We apologize for the inconvenience.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I lost all my Matches!</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Try logging out and logging back in. To do so, go to Settings, select App Settings, scroll down, hit Logout and log back in. As long as you haven&rsquo;t accidentally deleted your account you should be fine! Note: If only one or two of your Matches disappeared, they either blocked you or deleted their account...</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I accidentally left-swiped someone, can I get them back?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only Glimmer Plus subscribers can Rewind their last swipe. To subscribe, just tap the yellow arrow button (Rewind) on the main screen and follow the instructions.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I change my name or my age?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Glimmer pulls data from Facebook to create your profile. Simply update your name / age on Facebook and it will update on Glimmer in the next few days. If your info doesn&rsquo;t update, you&rsquo;ll have to delete your Glimmer account, update your name / age on Facebook, and create a new Glimmer account. Deleting your Glimmer account will delete all your matches and messages. To delete your Glimmer account, go to settings, app settings, scroll down, and hit delete account. Make sure you&rsquo;ve entered your actual name / age on Facebook before creating a new Glimmer account, since Glimmer will use it to create your profile.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Glimmer is stuck on &quot;Finding people near you&quot;.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Open Glimmer and try logging out and logging back in. You can also try force quitting the app. On your device, go to Settings and make sure the GPS (location services) is enabled. You can also try turning location services OFF and back ON. You may have to open the Maps app in order for your GPS to pick up your current location. If the issue persists, please wait a few hours and try again.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I can&rsquo;t log in.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Make sure you have the latest version of Glimmer. Open Glimmer and try logging out and logging back in. You can also try force quitting the app. If you're using an iPhone: go to the iOS settings, select Facebook, remove your Facebook account and try logging in to Glimmer again. You can also try deleting Glimmer from your phone, downloading it again and reinstalling it. As long as you don&rsquo;t delete your account, this won&rsquo;t delete your existing matches and messages.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">One of my Matches disappeared.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">It sounds like that person either blocked you or deleted their account. Try logging out and back in just to be sure. To do so, go to Settings, scroll down, hit log out, and log back in. Glimmer on!</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Where are my Moments? What happened to Moments?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">We have permanently disabled the Moments feature.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Where did &quot;last active&quot; time go?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">This feature has been disabled.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Sign up and login</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I can&rsquo;t log in.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Make sure you have the latest version of Glimmer. Open Glimmer and try logging out and logging back in. You can also try force quitting the app. If you're using an iPhone: go to the iOS settings, select Facebook, remove your Facebook account and try logging in to Glimmer again. You can also try deleting Glimmer from your phone, downloading it again and reinstalling it. As long as you don&rsquo;t delete your account, this won&rsquo;t delete your existing matches and messages.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Why does Glimmer need to access my camera roll?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Glimmer needs permission to access your photos in order for you to save your Moments on your phone and to upload Moments from your photo library. If you accidentally denied the app access to your photos, go to the phone settings, find 'privacy', select 'photos', and make sure Glimmer is allowed access. If this doesn't solve the issue, you can try reinstalling the app. Note: As long as you don't delete your account, reinstalling the app won't delete any of your matches or messages.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">The app keeps crashing.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Make sure you have the latest version of Glimmer and try hard closing the app: If you&rsquo;re using an iPhone, double tap the &ldquo;Home&rdquo; button, and swipe Glimmer up to force quit the app. On Android: Go to &lsquo;Settings&rsquo;, go to &lsquo;Apps&rsquo;, select Glimmer, hit &lsquo;force quit&rsquo;, then open Glimmer again. You can also try restarting your phone and reinstalling the app. As long as you don't delete your account you won't lose any of your Matches or messages.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Will I lose my matches if I delete the app?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">As long as you don't delete your account, deleting the app won't delete any of your matches or messages.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Profile and settings</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I edit my job and school info?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>You can edit your profile to adjust what you share on Glimmer. First, make sure your school / job info is up to date on Facebook.</p>
                <p>To edit your school and work information, tap the Menu icon on the main screen, select View Profile, hit Edit, and scroll down to the School / Current Work headings. If you want to hide your school and/or work information, just select the option to display None.</p>
                <p>Note: if you have recently updated your school / work information on Facebook, the changes may take up to a few days to reflect on Glimmer.</p>
              </div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Where did &quot;last active&quot; time go?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">This feature has been disabled.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I edit my profile?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">To edit your profile, hit the menu icon on the top left hand corner, select 'View Profile', and hit 'Edit&rsquo;. You can add up to 6 photos, edit your bio, and select your gender.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I change my name or my age?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Glimmer pulls data from Facebook to create your profile. Simply update your name / age on Facebook and it will update on Glimmer in the next few days. If your info doesn&rsquo;t update, you&rsquo;ll have to delete your Glimmer account, update your name / age on Facebook, and create a new Glimmer account. Deleting your Glimmer account will delete all your matches and messages. To delete your Glimmer account, go to settings, app settings, scroll down, and hit delete account. Make sure you&rsquo;ve entered your actual name / age on Facebook before creating a new Glimmer account, since Glimmer will use it to create your profile.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I change my interests / page likes?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Your interests and page likes will update automatically, Glimmer periodically pulls this info from Facebook.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I turned Discovery off, but I'm still getting Matches.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">People you have already liked may still have the opportunity to see your profile and like you back; this means you may get new Matches after you&rsquo;ve turned Discovery off.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I can&rsquo;t change my settings.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Make sure you have the latest version of Glimmer. If the issue persists, please try again in a few hours.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I delete my account?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>For security purposes, if you want to delete your account, you&rsquo;ll need to do it directly on the app.</p>
                <p>You will permanently lose your matches, messages and all other information associated with your account. If you deleted the app, download the app again.</p>
                <p>To delete your account, navigate to the Settings pane, select App Settings, scroll down and select Delete Account. You&rsquo;ll see a message that says &quot;Account successfully deleted&quot;.</p>
                <p>Bear in mind, if you log in again after deleting your account, we create a whole new account for you.</p>
                <p>Note: Deleting the app does not delete your account. If you subscribed to Glimmer Plus, deleting the app and/or your account does not cancel your subscription.</p>
              </div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Verified Profiles</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I request a verified badge?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only some public figures, celebrities and brands will be verified. If you don&rsquo;t have a verified badge, there are other ways to confirm the authenticity of your profile. For example, you can connect your Instagram account to your Glimmer profile. If you are a public figure and would like to request a verified badge, please send an email to<a href="mailto:verified@tinder.com">verified@tinder.com</a>&nbsp;Note: emailing us doesn&rsquo;t guarantee that you will be verified. Please let us know if we can help with anything else!</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I have a verified badge on Facebook / Twitter / Instagram, can I have one on Glimmer?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only some public figures, celebrities and brands will be verified. If you don&rsquo;t have a verified badge, there are other ways to confirm the authenticity of your profile. For example, you can connect your Instagram account to your Glimmer profile. If you are a public figure and would like to request a verified badge, please send an email to<a href="mailto:verified@tinder.com">verified@tinder.com</a>&nbsp;Note: emailing us doesn&rsquo;t guarantee that you will be verified. Please let us know if we can help with anything else!</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Photos</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I add a profile photo?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">You can only add photos from your Facebook account to your Glimmer profile. To do so, on Glimmer, hit the menu icon on the top left hand corner, select 'View Profile', 'Edit', and select one of the available spaces to add a photo.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I can&rsquo;t upload photos.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Make sure you have the latest version of Glimmer. Make sure your pictures are in a public album or at least visible to your friends (not you only) on Facebook. You can also try moving the pictures to a different album on Facebook. Try hard closing the app: If you&rsquo;re using an iPhone, double tap the &ldquo;Home&rdquo; button, and swipe Glimmer up to force quit the app. On Android: Go to Settings, go to Apps, select Glimmer, hit force quit, then open Glimmer again. If the issue persists, try logging out and logging back in. To do so, go to Settings, select App Settings, scroll down, hit Logout, and log back in.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I get a cropping error.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Make sure you have a good network connection and try logging out and back in. To do so, go to &lsquo;Settings&rsquo;, select &lsquo;App Settings&rsquo;, scroll down, hit Logout, and log back in. If it doesn't solve the problem, please try again in a little while, this is usually temporary.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I send a photo to only one person?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">You can't send photos to individual users, but you can connect your Instagram account to your Glimmer profile. To do so, just go to &quot;settings&quot;, &quot;view profile&quot;, &quot;edit info&quot;, and &quot;connect Instagram.&quot;</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I accidentally denied Glimmer access to my photos.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p><strong>On iOS:</strong>&nbsp;go to the Facebook app, select More, Privacy Shortcuts, More Settings, Apps and find Glimmer. Remove Glimmer and make sure to check the box 'Delete all your Glimmer Activity on Facebook. This may take a few minutes.' Open Glimmer again and allow the app to access your photos. Also, you can go to the iOS settings, select &lsquo;Privacy&rsquo;, then &lsquo;Photos&rsquo;, and make sure Glimmer is enabled.</p>
                <p><strong>On Android:</strong>&nbsp;Go to the Facebook app, go to Account Settings, Apps and find Glimmer. Remove Glimmer and make sure to check the box 'Delete all your Glimmer Activity on Facebook. This may take a few minutes.' Open Glimmer again and allow the app to access your photos.</p>
              </div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Connecting Your Instagram to Your Glimmer Profile</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I display my Instagram on my Glimmer profile?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">To connect your Instagram account to your Glimmer profile, tap the menu icon in the top left hand corner on the main screen, hit &lsquo;View Profile,&rsquo; tap &lsquo;edit,&rsquo; scroll and select &lsquo;Connect Instagram.&rsquo; Please note that your Instagram photos can not be used as profile pictures. Profile pictures can only be uploaded from Facebook.<br />
                <br />
                Note: If your Instagram account is set to private (i.e, only your friends can see your photos), and if you choose to connect your Instagram to your Glimmer account, Glimmer users will be able to see your most recent Instagram photos. This won't affect your privacy settings on Instagram.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I use my Instagram photos as profile pics?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Your Instagram photos can not be used as profile pictures on Glimmer. Profile pictures can only be uploaded from Facebook.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I remove my Instagram from my profile?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">To disconnect your Instagram from your Glimmer profile, tap the menu icon in the top left hand corner on the main screen, hit 'View Profile,' tap 'edit,' and select 'Disconnect.'</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What if my Instagram is private?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">If your Instagram account is private, and if you choose to connect it to your Glimmer account, Glimmer users will be able to see your most recent photos. However, this won't affect your privacy settings on Instagram.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I remove an Instagram photo from my Glimmer profile?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Your Instagram photos will update automatically, but there might be a slight delay. If you&rsquo;ve deleted a photo on Instagram and it&rsquo;s still showing up on Glimmer, you can disconnect and reconnect your Instagram account in order to update your information. To disconnect your Instagram account from your Glimmer profile, tap the menu icon in the top left hand corner on the main screen, hit &lsquo;View Profile,&rsquo; tap &lsquo;edit,&rsquo; and select &lsquo;Disconnect.&rsquo; Posting a new photo on Instagram will also update your Instagram photos on Glimmer.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Discovery (Swiping other users)</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What is Discovery?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Discovery is the part of the app where you get to swipe other users. If you turn Discovery off, you will not be shown to anyone in Discover. You can still see and chat with your matches. To turn Discovery ON or OFF, navigate to the Settings panel by tapping the gear / menu icon in the top left hand corner on the main screen and go to Discovery Preferences. Note: People you have already liked may still have the opportunity to see your profile and like you back; this means you may still get new matches after you&rsquo;ve turned Discovery off.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I accidentally left-swiped someone, can I get them back?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only Glimmer Plus subscribers can Rewind their last swipe. To subscribe, just tap the yellow arrow button (Rewind) on the main screen and follow the instructions.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I message someone I haven&rsquo;t matched with?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">No, only users who have indicated a mutual interest in one another are allowed to chat.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I report someone?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">You can report users that you have already matched with, and users you haven&rsquo;t matched with. To report someone, go to their profile, hit the menu icon (ellipsis icon) and hit report.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Glimmer says &ldquo;There&rsquo;s no one new in my area.&rdquo;</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Go to &quot;Settings&quot;, select &quot;Discovery Preferences&quot; and try increasing the distance and age range. You can also try logging out and back in. If you&rsquo;re using an Android device, go to the phone settings, find Location, hit Mode, and select High accuracy.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Glimmer is stuck on &quot;Finding people near you&quot;.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Open Glimmer and try logging out and logging back in. You can also try force quitting the app. On your device, go to Settings and make sure the GPS (location services) is enabled. You can also try turning location services OFF and back ON. You may have to open the Maps app in order for your GPS to pick up your current location. If the issue persists, please wait a few hours and try again.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What are the numbers next to my friends photo in common connections?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">The &quot;1st&quot; next to your Facebook friend's picture under Common Connections simply means that you and your match are both friends with this person. The &quot;2nd&quot; next to your Facebook friend's picture means that your friend knows someone who knows your match.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Glimmer Plus</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What is Glimmer Plus?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>Glimmer Plus is a premium addition to the Glimmer experience. With Glimmer Plus we&rsquo;re giving our users access to their three most-requested features: Super Like, Rewind, and Passport.</p>
                <p><strong>Super Like</strong>&nbsp;Now, by swiping up, or simply tapping the new blue star icon when looking at someone's Glimmer profile, you let that special someone know that they stand out from everyone else. The person you Super Liked will take notice &ndash; when your profile appears and they're deciding whether to swipe right, it will show up with a bright blue footer and star icon, highlighting that you Super Liked them. And when they do swipe right on your Super Like, it'll be an immediate match!</p>
                <p><strong>Rewind</strong>&nbsp;lets you take back your last swipe. If you accidentally swiped left on someone you wanted to get to know, they are no longer lost in the Glimmersphere forever. Get them back with the touch of a button.</p>
                <p><strong>Passport</strong>&nbsp;lets you change your location to match with people around the world. Swipe, match and chat with Glimmer users in a destination of your choice. Navigate between your current location and new destinations.</p>
                <p>Glimmer Plus users also get&nbsp;<strong>unlimited right swipes</strong>; they can like as many users as they want.</p>
                <p>Glimmer is a free app. Glimmer Plus is an in-app subscription. To subscribe to Glimmer Plus, just tap the yellow arrow button (Rewind) on the main screen and follow the instructions.</p>
                <p>Note: You can cancel your Glimmer Plus subscription at anytime.</p>
              </div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I change my location?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only Glimmer Plus users can change their location. Tap the Settings icon, select Discovery Preferences, tap 'Swiping In' (on Android) or 'Location' (on iOS), select &quot;Add a new location,&quot; type or search for a location, select it from your search results, and when a pin appears, tap the blue banner to begin swiping in this location!</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Where did the passport button go?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">With the addition of our new feature, Super Like, the Passport button has been moved to Discovery Preferences. If you would like to change your location, tap the Settings icon, select Discovery Preferences, and tap 'Swiping In' to change your location.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I delete a location?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">While using Passport, you may only have up to 5 locations listed at one time. Once you select a 6th location, the last one on your list is removed.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I can&rsquo;t change my location.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">If you&rsquo;re unable to change your location, check your connection and try again later. You can also try logging out and logging back in. To do so, go to 'App Settings', scroll down, and hit 'Logout'.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I started a new Glimmer account but Glimmer Plus doesn&rsquo;t work anymore.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">If you have already subscribed to Glimmer Plus but the app still asks you to subscribe, try restoring your purchase. On your iPhone, open Glimmer, tap the arrow button (Rewind) on the main screen, and select &quot;Restore Purchase&quot; at the bottom of the screen. On your Android, open Glimmer, go to Settings, select App Settings, and hit &quot;Restore Purchase.&quot;</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I undo my last swipe?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only Glimmer Plus subscribers can rewind their last swipe using the yellow arrow on the main screen. To subscribe to Glimmer Plus, just tap the yellow arrow button (Rewind) on the main screen and follow the instructions.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I use Glimmer Plus on multiple devices?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Your Glimmer Plus subscription carries across platforms. This means that if you use the same Glimmer account on multiple devices, you only need one Glimmer Plus subscription. Please note, you may need to restore your purchase. On your iPhone, open Glimmer, tap the arrow button (Rewind) on the main screen, and select &quot;Restore Purchase&quot; at the bottom of the screen. On your Android, open Glimmer, go to Settings, select App Settings, and hit &quot;Restore Purchase.&quot;</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can other users see me when I&rsquo;m using Passport?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Passport lets you change your location to match with people around the world. When you change your location, users who are currently in this location will get to see your profile and swipe left or right. When they look at your profile, the the distance between the two of you will be indicated in miles or kilometers.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I get unlimited right swipes?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Only Glimmer Plus subscribers get unlimited right swipes; they can like as many users as they want. If you do not have Glimmer Plus, you will only be able to like a limited amount of profiles. Once you reach the limit, you can either get Glimmer Plus, or wait until the displayed time has elapsed.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">There was a problem setting up billing.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">If you received an error message that said &ldquo;problem setting up in-app billing&rdquo;, please make sure your Google account is setup on your device. To do so, go to Settings, select Accounts, and make sure you are logged in to your Google account.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I change payment information?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>If you&rsquo;ve subscribed to Glimmer Plus and would like to change your payment information, please read the following carefully:</p>
                <ul>
                  <li>If you subscribed to Glimmer Plus using your Apple ID, go to your phone&rsquo;s Settings, tap iTunes &amp; App Store, tap your Apple ID and select &quot;view Apple ID&quot;, select &quot;payment information&quot;, update your payment information. and tap &quot;done&quot;.</li>
                  <li>If you subscribed to Glimmer Plus using your Google Play Store account, open the Google Play Store, touch the Menu icon and select &quot;my account,&quot; tap &quot;add payment method&quot; or &quot;edit payment method.&quot; If prompted, sign in to Google Wallet and follow the on-screen instructions.</li>
                </ul>
              </div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">My payment method failed.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Please make sure that you&rsquo;ve entered your payment information accurately and try again. Please note that accepted payment methods for both iOS and Android only include credit and debit card at this time. For a list of accepted credit and debit cards, please refer to the Apple App Store or Google Play Store.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I cancel my Glimmer Plus subscription?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>You can cancel your Glimmer Plus subscription at anytime.</p>
                <p>To cancel your subscription on your iPhone, iPad or iPod Touch directly:</p>
                <ol>
                  <li>Go to the App Store on your iOS device.</li>
                  <li>Scroll to the bottom.</li>
                  <li>Tap Apple ID (your Apple ID email)</li>
                  <li>Tap View Apple ID.</li>
                  <li>Log in if it asks you to.</li>
                  <li>Scroll down to Subscriptions and tap Manage.</li>
                  <li>Select Glimmer and set the auto-renewal slider to Off or select Unsubscribe.</li>
                </ol>
                <p>If the option to turn off auto-renewal is not displayed here, you may have already chosen to turn it off, canceling future charges. If that's the case, you should be able to see the end date of your subscription on this screen.</p>
                <p>To cancel your subscription on your Android device directly:</p>
                <ol>
                  <li>Open the Google Play Store app.</li>
                  <li>Search for Glimmer and select Glimmer in your search results.</li>
                  <li>Select Cancel or Unsubscribe.</li>
                  <li>Confirm.</li>
                </ol>
                <p><strong>Note:</strong>&nbsp;After canceling your subscription, you will be able to use Glimmer Plus for the remaining of the 30 days that you have already paid for. When your subscription expires, it will not be renewed. You can opt back in at any time. Canceling your subscription will not retroactively refund subscription payments, and previously charged subscription fees cannot be prorated based on cancellation date. Deleting the app and/or your account does not cancel your subscription.</p>
              </div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I request a refund?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>If you subscribed to Glimmer Plus using your Apple ID, refunds are handled by Apple, not Glimmer. If you wish to request a refund, go to iTunes, click on your Apple ID, select &ldquo;Purchase history&rdquo;, find the transaction and hit &ldquo;Report Problem&rdquo;. You can also submit a request at&nbsp;<a href="https://getsupport.apple.com/">https://getsupport.apple.com</a></p>
                <p>If you subscribed to Glimmer Plus using your Google Play Store account: please send an email to help@gotinder.com with your order number, and we'll process your request as soon as possible. You can find the order number in the order confirmation email or by logging in to Google Wallet.</p>
                <p>Note: Refund requests may only be accepted if requested within a month of the transaction date.</p>
              </div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Matches</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I unmatch (block) someone?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">You can only unmatch (block) someone that you have matched with. Go to his/her profile, hit the icon in the top right hand corner, and select &ldquo;Unmatch&rdquo;. You&rsquo;ll disappear from their Matches, they won&rsquo;t be able to message you anymore, and they will also disappear from your Matches.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I unblock someone?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Nope, blocking / unmatching is a permanent action.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How do I report someone?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">You can report users that you have already matched with, and users you haven&rsquo;t matched with. To report someone, go to their profile, hit the menu icon (ellipsis icon) and hit report.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I got a new match notification but I don&rsquo;t see a new match.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Try typing that person&rsquo;s name in the Search bar at the top of the Matches screen. If the conversation doesn&rsquo;t come up, it means that person either blocked you or deleted their account. Just to be sure, you can try logging out and logging back in. To do so, go to Settings, scroll down, hit Logout, and log back in.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I lost all my Matches!</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Try logging out and logging back in. To do so, go to &lsquo;Settings&rsquo;, select &lsquo;App Settings&rsquo;, scroll down, hit &lsquo;Logout&rsquo; and log back in. As long as you haven&rsquo;t accidentally deleted your account you should be fine! Note: If only one or two of your Matches disappeared, they either blocked you or deleted their account.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">One of my Matches disappeared.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">It sounds like that person either blocked you or deleted their account. Try logging out and back in just to be sure. To do so, go to Settings, scroll down, hit log out, and log back in. Glimmer on!</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I don&rsquo;t have any Matches.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">You need to allow time for people to like you back! After you Swipe Right on someone, if they like you back, then it&rsquo;s a match! You should try updating your photos and bio, it will go a long way. You could also connect your Instagram account to your Glimmer profile.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What does the number next to my friend's photo in Common Connections mean?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">The &quot;1st&quot; next to your Facebook friend's picture under Common Connections simply means that you and your match are both friends with this person. The &quot;2nd&quot; next to your Facebook friend's picture means that your friend knows someone who knows your match.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Super Like</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What is Super Like?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>Now, by swiping up, or simply tapping the new blue star icon when looking at someone's Glimmer profile, you let that special someone know that they stand out from everyone else. The person you Super Liked will take notice &ndash; when your profile appears and they're deciding whether to swipe right, it will show up with a bright blue footer and star icon, highlighting that you Super Liked them. And when they do swipe right on your Super Like, it&rsquo;ll be an immediate match!</p>
                <p>We wanted Super Likes to be really special while making sure everyone can use them, so we're giving all Glimmer users one Super Like to send each day. Glimmer Plus subscribers will receive five total Super Likes per day.</p>
                <p>To subscribe to Glimmer Plus, just the tap the yellow arrow button (Rewind) on the main screen and follow the instructions.</p>
                <p>To find out more, go to tinder.com/blog</p>
              </div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">How many people can I Super Like?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">We wanted Super Likes to be really special while making sure everyone can use them, so we&rsquo;re giving all Glimmer users one Super Like to send each day. Glimmer Plus subscribers will receive five total Super Likes per day. If you're a Glimmer Plus subscriber, the number in the blue star button indicates how many Super Likes you have left for that day. To subscribe to Glimmer Plus, just the tap the yellow arrow button (Rewind) on the main screen and follow the instructions.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I rewind a Super Like?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">If you are a Glimmer Plus subscriber, you can rewind an accidental Super Like; just tap the yellow arrow (Rewind button). You can only rewind your last swipe.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Moments</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Where are my Moments? What happened to Moments?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">We have permanently disabled the Moments feature.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Chat / Messages</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">I got a new message notification but I don&rsquo;t see it.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Try typing that person&rsquo;s name in the Search bar at the top of the Matches screen. If the conversation doesn&rsquo;t come up, it means that person either blocked you or deleted their account. Just to be sure, you can try logging out and logging back in. To do so, go to Settings, scroll down, hit Logout, and log back in.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I message someone I haven&rsquo;t matched with?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">No, only users who have indicated a mutual interest in one another are allowed to chat.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">My messages are not going through.</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Please make sure you have a strong internet connection. On iOS, try tapping the exclamation mark and hit resend. On Android, you can just tap the message to try sending it again. You can also try closing the app. If the issue persists, please wait a few hours and try again at a later time. We apologize for the inconvenience.</div>
            </dd>
          </div>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">Can I unblock someone?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">Nope, blocking / unmatching is a permanent action.</div>
            </dd>
          </div>
        </dl>
        <h2 ng-bind-html="trustHtml(section.title)">Web Profile</h2>
        <dl>
          <div ng-repeat="faqItem in section.questions">
            <dt ng-class="{expanded: faqItem.expanded}" ng-click="faqItem.expanded = !faqItem.expanded">
              <h3><span ng-bind-html="trustHtml(faqItem.q)">What is a web profile?</span></h3>
            </dt>
            <dd ng-show="faqItem.expanded">
              <div ng-bind-html="trustHtml(faqItem.a)">
                <p>Now you can share your Glimmer profile with anyone, even off the app. Just create a username and share the link (URL) to your profile.</p>
                <p>To create the username that will be used in your web profile URL, tap the settings icon on the main screen, go to &ldquo;discovery settings&rdquo;, tap on &ldquo;username&rdquo; and type your preferred username. Anyone who opens your URL will be able view and swipe your profile directly on the app.</p>
                <p>Please note: you can only match with users who share the same discovery preferences.</p>
              </div>
            </dd>
          </div>
        </dl>
        <p>&nbsp;</p>
</section>
</div>
</body>
</html>