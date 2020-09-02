<?php
declare(strict_types=1);

namespace App\Classes;

class Pagination {

  public $current_page;
  public $per_page;
  public $total_count;
  protected $css_class = 'pagination';
  protected $numbers_scope;

  public function __construct($page=1, $per_page=4, $total_count=0, $css_class='pagination-lg') {
    $this->current_page = (int) $page;
    $this->per_page = (int) $per_page;
    $this->total_count = (int) $total_count;
    $this->css_class .= ' ' . $css_class;

    $this->numbers_scope = 4;
  }

  public function offset() {
    return $this->per_page * ($this->current_page - 1);
  }

  public function total_pages() {
    return ceil($this->total_count / $this->per_page);
  }

  public function previous_page() {
    $prev = $this->current_page - 1;
    return ($prev > 0) ? $prev : false;
  }

  public function next_page() {
    $next = $this->current_page + 1;
    return ($next <= $this->total_pages()) ? $next : false;
  }

  public function previous_link($url="") {
    $link = "";
    $text = (strpos($this->css_class, 'pagination-lg') !== false) ?
    '&laquo; Previous' : '&laquo;';
    if ($this->previous_page() != false) {
      $link .= "<li class=\"page-item\">";
      $link .= "<a class=\"page-link page-link--text\" href=\"{$url}?page={$this->previous_page()}\" aria-lable=\"Previous\">";
      $link .= "<span aria-hidden=\"true\">{$text}</span></a>";
      $link .= "</li>";
    }
    return $link;
  }

  public function next_link($url="") {
    $link = "";
    $text = (strpos($this->css_class, 'pagination-lg') !== false) ?
            'Next &raquo;' : '&raquo;';

    if ($this->next_page() != false) {
      $link .= "<li class=\"page-item\">";
      $link .= "<a class=\"page-link page-link--text\" href=\"{$url}?page={$this->next_page()}\" aria-label=\"Next\">";
      $link .= "<span aria-hidden=\"true\">{$text}</span></a>";
      $link .= "</li>";
    }
    return $link;
  }

  public function number_links($url="") {
    $output = "";
    $numbers_to_show = $this->getNumbersToShow(); 
    for ($i = 1; $i <= $this->total_pages(); $i++) {
      if (in_array($i, $numbers_to_show)) {
        if ($i == $this->current_page) {
          $output .= "<li class=\"page-item active\" aria-current=\"page\">";
          $output .= "<a class=\"page-link\" href=\"#\">{$i}<span class=\"sr-only\">(current)</span></a>";
          $output .= "</li>";
        } else {
          $output .= "<li class=\"page-item\">";
          $output .= "<a class=\"page-link\" href=\"{$url}?page={$i}\">{$i}</a>";
          $output .= "</li>";
        }
      }
    }
    return $output;
  }

  public function page_links($url) {
    $output = "";
    if ($this->total_pages() > 1) {
      $output .= "<nav class=\"pagination-nav\">";
      $output .= "<ul class=\"<?php $this->css_class ?>\">";
      $output .= $this->previous_link($url);
      $output .= $this->number_links($url);
      $output .= $this->next_link($url);
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

}
?>