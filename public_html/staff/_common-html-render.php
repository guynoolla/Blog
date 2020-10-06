<?php

function tableSearchForm() {
  ob_start();

  ?><div class="d-flex">
    <div class="search-widget flex-grow-1">
      <form id="adminSearchForm" data-type="search" method="post" action="<?php echo url_for($_SERVER['PHP_SELF']) ?>" class="form-search w-100" role="search">
        <div class="input-group">
          <label class="screen-reader-text" for="s">Search for:</label>
          <div class="input-group-prepend">
            <button type="submit" name="submit" class="btn rounded-left"></button>
          </div>
          <input id="s" name="s" type="text" class="form-control search-query" placeholder="Search..." value="">
          <div class="input-group-append">
            <button type="submit" name="submit" class="btn btn-default">Search</button>
          </div>
        </div>
      </form>
    </div>
  </div><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function tableIsEmpty($text = 'This table is empty') {
  return "<div class=\"py-4 px-2 mt-1 text-center alert alert-info\">
    <p class=\"h4\">{$text}</p>
  </div>";
}

?>