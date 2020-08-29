<?php
use App\Classes\Topic;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id'])) {
  $cmd = $_GET['cmd'] ?? false;
  $topic = Topic::findById($_GET['id']);

  if (!$cmd || !$topic) {
    redirect_to(url_for('/staff/topics/index.php'));
  }

  if ($cmd == 'delete') {
    $result = $topic->delete();
    $session->message("The topic '" . $topic->name . "' was deleted.");
    redirect_to(url_for('/staff/topics/index.php'));
  }
}

$topics = Topic::findAll();

$page_title = 'Topics';
include SHARED_PATH . '/staff_header.php';
require '../_common-html.php';

?>
<div class="container-xl">
  <div class="page-admin">

    <div class="row">
      <div class="topbox col-12"></div>
    </div>
  
    <div class="row">
      <?php include SHARED_PATH . '/staff_sidebar.php' ?>

      <main class="main col-lg-9">
        <div class="main-content">
          <?php echo page_back_button() ?>
          <a class="btn btn-outline-secondary btn-md mb-1" href="<?php echo url_for('staff/topics/create.php') ?>">New Topic</a>

          <h2 style="text-align: center;"><?php echo $page_title ?></h2>

          <?php if (empty($topics)): ?>
            <p class="lead">You have not topics yet.</p>
          
          <?php else: ?>
            <?php echo display_session_message('msg success') ?>

            <table class="table table-striped table-bordered table-hover table-light table-sm">
              <thead class="bg-muted-lk text-muted">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Description</th>
                  <th scope="colgroup" colspan="2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($topics as $key => $topic): ?>
                  <tr>
                    <th scope="row"><?php echo $key + 1 ?></th>
                    <td><a href="#"><?php echo $topic->name ?></a></td>
                    <td><?php echo $topic->description ?></td>
                    <td scope="colgroup" colspan="1">
                      <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/topics/edit.php?id=' . $topic->id) ?>">
                        Edit
                      </a>
                    </td>
                    <td scope="colgroup" colspan="1">
                      <a class="btn-lk btn-lk--danger" href="<?php echo url_for('/staff/topics/index.php?id=' . $topic->id . '&cmd=delete') ?>">
                        Delete
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
      
          <?php endif; ?>

        </div>
      </main>
    </div><!-- row -->

  </div><!--page admin-->
</div><!--container-->

<?php include SHARED_PATH . '/staff_footer.php'; ?>