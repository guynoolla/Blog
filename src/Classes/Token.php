<?php
declare(strict_types=1);

namespace App\Classes;

class Token {

	protected $token;

	private $secret_key;

	public function __construct($token_value=null) {
    $this->secret_key = SECRET_KEY;
    
    if ($token_value) {
			$this->token = $token_value;
		} else {
      // 16 bytes = 128 bits = 32 hex characters
			$this->token = bin2hex(random_bytes(16)); 
		}
	}

	public function getValue() {
		return $this->token;
	}

	public function getHash() {
    // sha256 = 64 chars
		return hash_hmac('sha256', $this->token, $this->secret_key);
  }

}