<?php
declare(strict_types=1);

namespace App\Contracts;

class Mailer implements MailerInterface {

	private $mailer;
	private $message;
	private $admin_email;

	public function __construct() {
		$transport = (new \Swift_SmtpTransport(
			SMTP_HOST, SMTP_PORT, SMTP_SECURE
		))->setUsername(SMTP_USERNAME)
			->setPassword(SMTP_PASSWORD);

		$this->mailer = new \Swift_Mailer($transport);
		$this->message = new \Swift_Message();
		$this->admin_email = ADMIN_EMAIL;
	}

	public function send($to, $subject, $text, $html) {
		$this->message->setTo([$to]);
		$this->message->setSubject($subject);
		$this->message->setBody($html, 'text/html');
		$this->message->addPart($text, 'text/plain');
		$this->message->setFrom([$this->admin_email]);

		$this->mailer->send($this->message);
	}

}