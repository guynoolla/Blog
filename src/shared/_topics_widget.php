<?php
use App\Classes\Topic;

$sb_topics = Topic::findAll();

?>
<!-- topics -->
<div class="section topics">
  <h2>Topics</h2>
  <ul>
    <?php foreach($sb_topics as $key => $topic): ?>
      <a href="<?php echo url_for('topic/' . u($topic->name) . '?id=' . $topic->id) ?>">
        <li><?php echo $topic->name ?></li>
      </a>
    <?php endforeach; ?>
  </ul>
</div>
<!-- // topics -->