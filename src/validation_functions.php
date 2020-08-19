<?php

  // is_blank('abcd')
  // * validate data presence
  // * uses trim() so empty spaces don't count
  // * uses === to avoid false positives
  // * better than empty() which considers "0" to be empty
  function is_blank($value) {
    return !isset($value) || trim($value) === '';
  }

  // has_presence('abcd')
  // * validate data presence
  // * reverse of is_blank()
  // * I prefer validation names with "has_"
  function has_presence($value) {
    return !is_blank($value);
  }

  // has_length_greater_than('abcd', 3)
  // * validate string length
  // * spaces count towards length
  // * use trim() if spaces should not count
  function has_length_greater_than($value, $min) {
    $length = strlen($value);
    return $length > $min;
  }

  // has_length_less_than('abcd', 5)
  // * validate string length
  // * spaces count towards length
  // * use trim() if spaces should not count
  function has_length_less_than($value, $max) {
    $length = strlen($value);
    return $length < $max;
  }

  // has_length_exactly('abcd', 4)
  // * validate string length
  // * spaces count towards length
  // * use trim() if spaces should not count
  function has_length_exactly($value, $exact) {
    $length = strlen($value);
    return $length == $exact;
  }

  // has_length('abcd', ['min' => 3, 'max' => 5])
  // * validate string length
  // * combines functions_greater_than, _less_than, _exactly
  // * spaces count towards length
  // * use trim() if spaces should not count
  function has_length($value, $options) {
    if (isset($options['min']) && !has_length_greater_than($value, $options['min'] - 1)) {
      return false;
    } elseif (isset($options['max']) && !has_length_less_than($value, $options['max'] + 1)) {
      return false;
    } elseif (isset($options['exact']) && !has_length_exactly($value, $options['exact'])) {
      return false;
    } else {
      return true;
    }
  }

  // has_inclusion_of( 5, [1,3,5,7,9] )
  // * validate inclusion in a set
  function has_inclusion_of($value, $set) {
  	return in_array($value, $set);
  }

  // has_exclusion_of( 5, [1,3,5,7,9] )
  // * validate exclusion from a set
  function has_exclusion_of($value, $set) {
    return !in_array($value, $set);
  }

  // has_string('nobody@nowhere.com', '.com')
  // * validate inclusion of character(s)
  // * strpos returns string start position or false
  // * uses !== to prevent position 0 from being considered false
  // * strpos is faster than preg_match()
  function has_string($value, $required_string) {
    return strpos($value, $required_string) !== false;
  }

  // has_valid_email_format('nobody@nowhere.com')
  // * validate correct format for email addresses
  // * format: [chars]@[chars].[2+ letters]
  // * preg_match is helpful, uses a regular expression
  //    returns 1 for a match, 0 for no match
  //    http://php.net/manual/en/function.preg-match.php
  function has_valid_email_format($value) {
    $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
    return preg_match($email_regex, $value) === 1;
  }

  // has_unique_username('johnqpublic')
  // * Validates uniqueness of admins.username
  // * For new records, provide only the username.
  // * For existing records, provide current ID as second argument
  //   has_unique_username('johnqpublic', 4)
  function has_unique_username($username, $current_id="0") {
    $user = App\Classes\User::findByUsername($username);
    if($user === false || $user->id == $current_id) {
      // is unique
      return true;
    } else {
      // not unique
      return false;
    }
  }

  function has_unique_email($email, $current_id="0") {
    $user = App\Classes\User::findByEmail($email);
    if($user === false || $user->id == $current_id) {
      return true;
    } else {
      return false;
    }
  }

  function is_blank_ckeditor($value) {
    return (trim(str_replace('&nbsp;', '', strip_tags($value))) == '');
  }

  function has_links($value, $get_links=false) {
    //dd($value, 1, 'A');
    preg_match_all(
      '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#',
      $value, $match
    );
    if (!$get_links) {
      return (!empty($match[0]) == true);
    } else {
      //dd($match[0], 0, 'B');
      return $match[0];
    }
  }

  function has_external_link($value, $allowable_hosts=[]) {
    $links = has_links($value, true);
    if (!empty($links)) {
      foreach ($links as $link) {
        if (is_external_url($link, $allowable_hosts)) { return true; }
      }
    }
    return false;
  }

  function is_external_url($url, $allowable_hosts) {
    $components = parse_url($url);
    // we will treat url like '/relative.php' as relative
    if (empty($components['host'])) return false;
    // url host looks exactly like the local host
    if (strcasecmp($components['host'], $_SERVER['HTTP_HOST']) === 0) {
      return false;
    }
    foreach ($allowable_hosts as $value) {
      if (strcasecmp($components['host'], $value) === 0) {
        return false;
      }
    }
    // check if the url host is a subdomain 
    return strrpos(strtolower($components['host']), '.' . $_SERVER['HTTP_HOST'])
      !== strlen($components['host']) - strlen('.' . $_SERVER['HTTP_HOST']);
  }

  function has_unallowed_tag($value, $allowable_tags) {
    $original_value = $value;
    $value = strip_tags($value, $allowable_tags);
    if (strlen($value) != strlen($original_value)) {
      return true;
    } else {
      return false;
    }
  }

?>