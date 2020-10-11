<?php
use App\Classes\Category;

$sb_categories = Category::findAll();

?>
<!-- categories -->
<div class="section categories">
  <h2>Categories</h2>
  <ul>
    <?php foreach($sb_categories as $key => $category): ?>
      <a href="<?php echo url_for('category/' . u($category->name) . '?id=' . $category->id) ?>">
        <li><?php echo $category->name ?></li>
      </a>
    <?php endforeach; ?>
  </ul>
</div>
<!-- // categories -->