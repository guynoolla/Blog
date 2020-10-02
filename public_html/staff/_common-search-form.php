<div class="d-flex">
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
</div>
