<?php

/* ContactPop jQuery Plugin
 *
 * By Jon Raasch
 * http://jonraasch.com
 *
 * Copyright (c)2009 Jon Raasch. All rights reserved.
 * Released under FreeBSD License, see readme.txt
 * Do not remove the above copyright notice or text.
 *
 * For more information please visit:
 * http://jonraasch.com/blog/contact-pop-jquery-plugin
*/


class ContactPop {
    /****** config *********/

    // the email to which you want to send the info from the contact form
    var $siteEmail = 'brian@globalshowcases.com, mike@globalshowcases.com';

    // the title of the emails that the form sends
    var $emailTitle = 'Message from Global Showcases website';

    var $thankYouMessage = "Thank you for contacting us, we'll get back to you shortly.";

    /******* end config ********/


    var $error = '';

    function getFormHtml($ajax = 0) {

        $postedName = $_POST['name'];
        $postedEmail = $_POST['email'];
        $postedMessage = $_POST['message'];

        $formHtml = '';

        // send congratulations message
        if ( isset($_POST['httpReferer']) && !$this->error ) {
            $out = '<p id="contact-pop-error" class="formItem success">' . $this->thankYouMessage . '</p>';

            if ( $ajax ) $out ;

            return $out;
        }

        if ( $this->error ) $formHtml .= '<p id="contact-pop-error" class="formItem">' . $this->error . '</p>';


        $httprefi = $_SERVER["HTTP_REFERER"];

        $cancelLink = $ajax ? '' : '';

        $formHtml .= <<<EOT

        <input type="hidden" name="httpReferer" value="$httprefi" />

        <div class="formItem">
            <label>Name</label>
            <input type="text" name="name" class="inputText" value="$postedName" size="35" placeholder="Name" />
        </div>

        <div class="formItem">
            <label>Email</label>
            <input type="text" name="email" class="inputText" value="$postedEmail" size="35" placeholder="Email" />
        </div>

        <div class="formItem textarea">
            <label>Message</label>
            <textarea name="message" class="textarea" rows="4" cols="38">$postedMessage</textarea>
        </div>

        <div class="formItem">
            <input type="submit" value="Submit" class="submit" />
        </div>

EOT;
  return $formHtml;
    }

    function checkEmail($emailAddress) {
        if (preg_match('/[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)*\@[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)+/i', $emailAddress)){
            $emailArray = explode("@",$emailAddress);
            if (checkdnsrr($emailArray[1])){
                return TRUE;
            }
        }
        return false;
    }

    function processForm() {

        // check data
        if ( !$_POST['name'] ) $this->error .= 'Please enter your name<br />';
        if ( !$this->checkEmail( $_POST['email'] ) ) $this->error .= 'Please enter a valid email address<br />';
        if ( !$_POST['message'] ) $this->error .= 'Please enter a message<br />';

        if ( !$this->error ) $this->sendFormEmail();
    }

    function sendFormEmail() {
        $message = "Name: " . stripslashes($_POST['name']) .
            "\nEmail: " . $_POST['email'] .
            "\n\nMessage: " . stripslashes($_POST['message']);

        if ( $_POST['ajaxForm'] ) $message .= "\n\nFrom a Contact-Pop Form on page: " . $_SERVER["HTTP_REFERER"];
        else $message .= "\n\nReferrer: " . $_POST['httpReferer'];

        $message .= "\n\nBrowser Info: " . $_SERVER["HTTP_USER_AGENT"] .
            "\nIP: " . $_SERVER["REMOTE_ADDR"] .
            "\n\nDate: " . date("Y-m-d h:i:s");

        mail($this->siteEmail, $this->emailTitle, $message, 'From: ' . $_POST['name'] . ' <' . $_POST['email'] . '>');
    }
}

$contactPop = new ContactPop();

if (isset($_POST['httpReferer'])) $contactPop->processForm();

// echo the ajax version of the form
if ( isset($_REQUEST['ajaxForm']) && $_REQUEST['ajaxForm']) {
    echo $contactPop->getFormHtml(1);
}
// or echo the full page version of the form
else {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Contact</title>
<style>
    /* OVER LAY */
    #contact-pop-overlay {
      background: rgba(0,0,0,0.85);
      text-align: center;
      min-height: 100%;
      position: fixed;
      padding: 0 5%;
      z-index: 1000;
      width: 100%;
      left: 0;
      top: 0;
    }
    #contact-pop-panel-wrapper {
      margin: 5% auto 0;
      background: #FFF;
      width: 100%;
      max-width: 700px;
    }
    #contact-pop-panel {
      padding-bottom: 5px;
      position: relative;
      min-height: 130px;
      margin: 0 auto;
    }
    #contact-pop-header {
      text-transform: uppercase;
      font-weight: normal;
      position: relative;
      text-align: center;
      margin-top: 30px;
      font-size: 20px;
      padding: 20px;
      height: 50px;
      color: #000;
      z-index: 0;
    }
      #contact-pop-header:before {
      top: 31px;
      left: 0;
      right: 0;
      bottom: 0;
      border-top: 2px solid #dfdfdf;
      content:"";
      margin: 0 auto;
      position: absolute;
      width: 95%;
      z-index: -1;
      }
        #contact-pop-header span {
          background: #fff;
          padding: 0 10px;
        }
      #contact-pop-header .close-overlay {
        background: url('../images/closeX.png') 0 0 no-repeat;
        text-indent: -9999px;
        position: absolute;
        overflow: hidden;
        outline: none;
        outline: none;
        height: 40px;
        z-index: 100;
        content: 'X';
        color: #fff;
        width: 40px;
        top: -50px;
        right: 0;
      }
    #contact-pop-panel .formItem {
      margin: 0 20px 0 5%;
      display: inline-block;
      text-align: left;
      width: 90%;
    }
    #contact-pop-error.formItem {
      background: none repeat scroll 0 0 #eb6b6b;
      border: 1px solid #d46161;
      color: #fff;
      font-size: 14px;
      line-height: 1.5;
      margin-bottom: 10px;
      padding: 10px;
    }
      #contact-pop-error.formItem.success {
        background: none repeat scroll 0 0 #6bebaa;
        border: 1px solid #5ac08c;
        color: #fff;
      }
    #contact-pop-panel label {
      display: none;
    }
    #contact-pop-panel .inputText {
      text-align: center;
      box-shadow: none;
      padding: 2px 5px;
      background: #fff;
      font-size: 18px;
      border: none;
      width: 100%;
      border-bottom: 1px solid #d7d7d7;
    }
    #contact-pop-panel textarea {
      box-shadow: none;
      background: none;
      border: 1px solid #d7d7d7;
      font-size: 18px;
      margin-top: 5px;
      padding: 5px;
      width: 100%;
    }
      #contact-pop-panel .textarea label,
      html.lt-ie9.lt-ie8.lt-ie7 #contact-pop-panel label,
      html.lt-ie9.lt-ie8 #contact-pop-panel label,
      html.ie8 #contact-pop-panel label {
        color: rgb(168, 168, 168);
        display: inline-block;
        text-align: center;
        margin: 5px 0 0 0;
        font-size: 18px;
        width: 100%;
      }
    #contact-pop-panel input[type=submit] {
      background: #16d2a0;
      margin: 10px auto;
      font-size: 22px;
      color: #ffffff;
      display: block;
      border: none;
      height: 50px;
      width: 220px;
    }
    #contact-pop-loading-gif-wrapper {
      position: absolute;
      height: 100px;
      width: 100%;
      left: 0;
      top: 0;
    }
    #contact-pop-loading-gif {
      margin: 90px auto 0 auto;
      display: block;
      height: 17px;
      width: 17px;
    }


    /****** ie6 stuff ********/

    * html #contact-pop-overlay {
        background-image: url('../images/overlay-ie6.png');
        height: 100%;
        position: absolute;
    }
    * html #contact-pop-panel {
        height: 200px;
    }
    * html #contact-pop-header {
        height: 30px;
    }
    * html #contact-pop-header .close-overlay {
        background-image: url('../images/close-overlay-ie6.png');
    }
</style>
</head>
<body>
  <div id="contact-pop-overlay">
    <div id="contact-pop-panel-wrapper">
      <div id="contact-pop-panel">
        <h2 id="contact-pop-header"><span>Contact Global Showcases</span></h2>
          <form id="contact-pop-form" method="post" action="<?=$_SERVER['REQUEST_URI']; ?>">
            <?=$contactPop->getFormHtml(); ?>
          </form>
          <div id="contact-pop-loading-gif-wrapper" style="display: none;"><img src="assets/images/ajax-loader.gif" alt="" id="contact-pop-loading-gif"></div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } ?>