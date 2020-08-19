<!-- Left Sidebar -->
<?php
use App\Classes\Post;

$num = Post::countAll(['published' => 1, 'proved' => 0]);
?>

<div class="left-sidebar">
  <ul>
    
    <li style="border-bottom:none;">
      <h2><a href="<?php echo url_for('staff/index.php') ?>">Dashboard</a></h2>
    </li>

    <!-- Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <?php if ($session->isAuthor()): ?>
      <li><?php echo is_active(
          'staff/posts/create', 'Add Post',
          'fa fa-pencil-square-o'
      ); ?></li>
    <?php endif; ?>
    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author -->

    <!-- Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <?php if ($session->isAdmin()): ?>
      <li><?php echo is_active(
          'staff/posts/index', 'Admin Posts','fa fa-thumb-tack'
      ) ?></li>
      <li><?php echo is_active(
          'staff/posts/drafts', 'Posts - on status draft', 'fa fa-square-o'
      ) ?></li>
      <li><?php echo is_active(
          'staff/posts/unproved', 'Posts - awaiting admin ',
          'fa fa-hourglass-o',
          '', $num
      ) ?></li>
      <li><?php echo is_active(
          'staff/posts/proved', 'Posts - proved by admin',
          'fa fa-check-square-o'
      ) ?></li>
      <li><?php echo is_active(
          'staff/topics/index', 'Topics',
          'fa fa-paperclip'
      ) ?></li>
      <li><?php echo is_active(
          'staff/users/index', 'Users',
          'fa fa-users'
      ) ?></li>
    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin -->

    <!-- Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
    <?php elseif ($session->isAuthor()): ?>
      <li><?php echo is_active(
          'staff/posts/index', 'Posts',
          'fa fa fa-thumb-tack'
      ) ?></li>
    <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author -->
    
    <?php else: ?>
      <li><?php echo is_active(
          'staff/users/edit', 'Settings',
          'fa fa-user-o',
          '?id=' . $session->getUserId()
      ) ?></li>
    <?php endif; ?>
  </ul>
</div>
<!-- // Left Sidebar -->
