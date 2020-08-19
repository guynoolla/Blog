<?php
declare(strict_types=1);

namespace App\Contracts;

interface MailerInterface {
	public function send($to, $subject, $text, $html);
}
