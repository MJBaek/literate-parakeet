<?php
header('Content-type: application/json');
require_once('php-mailer/PHPMailerAutoload.php'); // Include PHPMailer

$mail = new PHPMailer();
$emailTO = $emailBCC =  $emailCC = array(); $formEmail = '';

### Enter Your Sitename 
$sitename = 'https://www.lunamint.com';

### Enter your email addresses: @required
$emailTO[] = array( 'email' => 'admin@lunamint.com', 'name' => 'Lunamint Support' ); 

### Enable bellow parameters & update your BCC email if require.
//$emailBCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

### Enable bellow parameters & update your CC email if require.
//$emailCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

### Enter Email Subject
$subject = "사이트 문의[".$sitename."]"; 

### If your did not recive email after submit form please enable below line and must change to your correct domain name. eg. noreply@example.com
$formEmail = 'noreply@lunamint.com';

### Success Messages
$msg_success = "We have <strong>successfully</strong> received your message. We'll get back to you soon.";

#test
/*
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST["contact-email"] = 'benjamin@lunamint.com';
$_POST["contact-name"] = 'test';
$_POST["contact-message"] = 'test  msg';
$_POST["form-anti-honeypot"] = '';
$_SERVER['HTTP_REFERER'] = '';
#php /var/www/html/lunamint/form/contact.php
$debug  = 2;
 */
if( $_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST["contact-email"]) && $_POST["contact-email"] != '' && isset($_POST["contact-name"]) && $_POST["contact-name"] != '') {
		### Form Fields
		$cf_email = $_POST["contact-email"];
		$cf_name = $_POST["contact-name"];
		$cf_message = isset($_POST["contact-message"]) ? $_POST["contact-message"] : '';

		$honeypot 	= isset($_POST["form-anti-honeypot"]) ? $_POST["form-anti-honeypot"] : 'bot';
		$bodymsg = '사이트 문의내역 : <br>';
		
		if ($honeypot == '' && !(empty($emailTO))) {
			### If you want use SMTP 
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = isset($debug) ? $debug : 0;
			//Set the hostname of the mail server
			$mail->Host = 'smtp.gmail.com';
			// use
			// $mail->Host = gethostbyname('smtp.gmail.com');
			// if your network does not support SMTP over IPv6
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port = 587;	//587;	//465;
			//Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPSecure = 'tls';	//'tls';	//'ssl';
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username = "lunamint.stieadmin@lunamint.com";
			//Password to use for SMTP authentication
			$mail->Password = "Mlnkbj12!@";

			### Regular email configure
			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';

			$mail->From = ($formEmail !='') ? $formEmail : $cf_email;
			$mail->FromName = $cf_name . ' - ' . $sitename;
			$mail->AddReplyTo($cf_email, $cf_name);
			$mail->Subject = $subject;
			
			/*$mail->SMTPOptions = array(
      				"ssl" => array(
				        "verify_peer" => false,
             				"verify_peer_name" => false,
	             			"allow_self_signed" => true
        			)
			);*/
			
			foreach( $emailTO as $to ) {
				$mail->AddAddress( $to['email'] , $to['name'] );
			}
			
			### if CC found
			if (!empty($emailCC)) {
				foreach( $emailCC as $cc ) {
					$mail->AddCC( $cc['email'] , $cc['name'] );
				}
			}
			
			### if BCC found
			if (!empty($emailBCC)) {
				foreach( $emailBCC as $bcc ) {
					$mail->AddBCC( $bcc['email'] , $bcc['name'] );
				}				
			}

			### Include Form Fields into Body Message
			$bodymsg .= isset($cf_name) ? "Contact Name: $cf_name<br><br>" : '';
			$bodymsg .= isset($cf_email) ? "Contact Email: $cf_email<br><br>" : '';
			$bodymsg .= isset($cf_message) ? "Message: $cf_message<br><br>" : '';
			$bodymsg .= $_SERVER['HTTP_REFERER'] ? '<br>---<br><br>This email was sent from [ICO]: ' . $_SERVER['HTTP_REFERER'] : '';
			
			// Mailing
			$mail->MsgHTML( $bodymsg );
			$is_emailed = $mail->Send();

			if( $is_emailed === true ) {
				$response = array ('result' => "success", 'message' => $msg_success);
			} else {
				$response = array ('result' => "error", 'message' => $mail->ErrorInfo);
			}
			echo json_encode($response);
			
		} else {
			echo json_encode(array ('result' => "error", 'message' => "Bot <strong>Detected</strong>.! Clean yourself Botster.!"));
		}
	} else {
		echo json_encode(array ('result' => "error", 'message' => "Please <strong>Fill up</strong> all required fields and try again."));
	}
}
