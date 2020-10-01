<div class="d-flex">
  <div class="search-widget flex-grow-1 py-1">
    <form id="adminSearchForm" data-type="search" method="post" action="<?php echo url_for($_SERVER['PHP_SELF']) ?>" class="form-search w-100" role="search">
      <div class="input-group">
        <label class="screen-reader-text" for="s">Search for:</label>
        <input id="s" name="s" type="text" class="form-control search-query rounded-left" placeholder="Search..." value="">
        <div class="input-group-append">
          <button type="submit" name="submit" class="btn btn-outline-primary">Search</button>
        </div>
      </div>
    </form>
  </div>
</div>
