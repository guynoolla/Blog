<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Class Token
 */
class Token {

	protected $token;

	private $secret_key;

	/**
	 * Class constructor
	 * Accepts token value as argument if it is provided 
	 * if not it creates it calling php function bin2hex
	 * 
	 * @param string $token_value
	 */
	public function __construct($token_value="") {
    $this->secret_key = SECRET_KEY;
    
    if ($token_value) {
			$this->token = $token_value;
		} else {
      // 16 bytes = 128 bits = 32 hex characters
			$this->token = bin2hex(random_bytes(16)); 
		}
	}

	/**
	 * Returns the token
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->token;
	}

	/**
	 * Creates hash from token
	 * This hash usually stored in the database as a secret key
	 *  and can be used for example by user email confirmation
	 *
	 * @return string
	 */
	public function getHash() {
    // sha256 = 64 chars
		return hash_hmac('sha256', $this->token, $this->secret_key);
  }

}