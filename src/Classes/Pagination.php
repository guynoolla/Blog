<?php
declare(strict_types=1);

namespace App\Classes;

class Pagination {

  public $current_page;
  public $per_page;
  public $total_count;
  protected $css_class = 'pagination';
  protected $numbers_scope;
  protected $delimiter;

  public function __construct($page=1, $per_page=4, $total_count=0, $css_class='pagination-md') {
    $this->current_page = (int) $page;
    $this->per_page = (int) $per_page;
    $this->total_count = (int) $total_count;
    $this->css_class .= ' ' . $css_class;

    $this->numbers_scope = 4;
  }

  public function offset() {
    return $this->per_page * ($this->current_page - 1);
  }

  public function totalPages() {
    return ceil($this->total_count / $this->per_page);
  }

  public function previousPage() {
    $prev = $this->current_page - 1;
    return ($prev > 0) ? $prev : false;
  }

  public function nextPage() {
    $next = $this->current_page + 1;
    return ($next <= $this->totalPages()) ? $next : false;
  }

  public function previousLink($url="") {
    $link = "";
    $text = (strpos($this->css_class, 'pagination-lg') !== false) ?
    '&laquo; Previous' : '&laquo;';
    if ($this->previousPage() != false) {
      $link .= "<li class=\"page-item\">";
      $link .= "<a class=\"page-link page-link--text\" href=\"{$url}{$this->delimiter}page={$this->previousPage()}\" aria-lable=\"Previous\">";
      $link .= "<span aria-hidden=\"true\">{$text}</span></a>";
      $link .= "</li>";
    }
    return $link;
  }

  public function nextLink($url="") {
    $link = "";
    $text = (strpos($this->css_class, 'pagination-lg') !== false) ?
            'Next &raquo;' : '&raquo;';

    if ($this->nextPage() != false) {
      $link .= "<li class=\"page-item\">";
      $link .= "<a class=\"page-link page-link--text\" href=\"{$url}{$this->delimiter}page={$this->nextPage()}\" aria-label=\"Next\">";
      $link .= "<span aria-hidden=\"true\">{$text}</span></a>";
      $link .= "</li>";
    }
    return $link;
  }

  public function numberLinks($url="") {
    $output = "";
    $numbers_to_show = $this->getNumbersToShow(); 
    for ($i = 1; $i <= $this->totalPages(); $i++) {
      if (in_array($i, $numbers_to_show)) {
        if ($i == $this->current_page) {
          $output .= "<li class=\"page-item active\" aria-current=\"page\" id=\"item-{$i}\">";
          $output .= "<a class=\"page-link\" href=\"#\">{$i}<span class=\"sr-only\">(current)</span></a>";
          $output .= "</li>";
        } else {
          $output .= "<li class=\"page-item\" id=\"item-{$i}\">";
          $output .= "<a class=\"page-link\" href=\"{$url}{$this->delimiter}page={$i}\">{$i}</a>";
          $output .= "</li>";
        }
      }
    }
    return $output;
  }

  public function pageLinks($url) {
    $url = $this->prepareUrl($url);

    $output = "";
    if ($this->totalPages() > 1) {
      $output .= "<nav class=\"pagination-nav\">";
      $output .= "<ul class=\"<?php $this->css_class ?>\">";
      $output .= $this->previousLink($url);
      $output .= $this->numberLinks($url);
      $output .= $this->nextLink($url);
      $output .= "</ul>";
      $output .= "</nav>";
    }
    return $output;
  }

  public function getNumbersToShow() {
    $scope_depth = ceil($this->current_page / $this->numbers_scope);
    $scope_max = $scope_depth * $this->numbers_scope;
    $pages_max = ceil($this->total_count / $this->per_page);
    $scope_max = $scope_max > $pages_max ? $pages_max : $scope_max;
    $scope_min = $scope_max - $this->numbers_scope > 0 ? 
                  $scope_max - $this->numbers_scope : 0;
    $numbers = [];
    for ($i = $scope_max; $i > $scope_min; $i--) {
      $numbers[] = $i;
    }
    return $numbers;
  }

  protected function prepareUrl($url) {
    if (strpos($url, '?') === false) {
      $this->delimiter = '?';
      return $url;
    }
    $data = explode('?', $url);
    $base = $data[0];
    if ($_SERVER['SERVER_NAME'] == 'localhost') {
      $base = url_for('/');
    }
    $params_str = $data[1];

    if (strpos($params_str, '&') !== false) {
      $params = explode('&', $params_str);
    } else {
      $params[0] = $params_str; 
    }
    foreach ($params as $key => $param) {
      if (explode('=', $param)[0] == 'page') {
        unset($params[$key]);
      }
    }

    if (count($params) == 0) {
      $this->delimiter = '?';
      return $base;
    } elseif (count($params) == 1) {
      $this->delimiter = '&';
      return $base . '?' . $params[0];
    } elseif (count($params) > 1) {
      $this->delimiter = '&';
      $first = array_shift($params);
      $url =  $base . '?' . $first . '&' . implode('&', $params);
      return $url;
    }
  }

}
?>