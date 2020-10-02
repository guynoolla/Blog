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

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content adminContentJS">

      <h1 class="dashboard-headline">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
        <a
          class="btn btn-outline-primary rounded-0 btn-md"
          href="<?php echo url_for('staff/topics/create.php') ?>"
          style="position:absolute;bottom:0;left:0;"
        >New Topic</a>
      </h1>

      <?php if (empty($topics)): ?>
        <p class="lead text-center bg-secondary text-white py-5">This table is empty</p>

      <?php else: ?>
        <?php echo display_session_message('msg success') ?>

        <table class="table table-striped table-bordered table-hover table-light table-md">
          <thead class="bg-muted-lk text-muted">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Created</th>
              <th scope="colgroup" colspan="2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($topics as $key => $topic): ?>
              <tr>
                <th scope="row"><?php echo $key + 1 ?></th>
                <td><span class="h5"><?php echo $topic->name ?></span></td>
                <td><?php echo $topic->description ?></td>
                <td>
                  <span class="h5"><?php echo date('M j, Y', strtotime($topic->created_at)) ?></span>
                </td>
                <td scope="colgroup" colspan="1">
                  <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/topics/edit.php?id=' . $topic->id) ?>">
                    Edit
                  </a>
                </td>
                <td scope="colgroup" colspan="1">
                  <?php
                    $data = no_gaps_between("
                      table-topics,
                      id-{$topic->id},
                      name-{$topic->name}
                    ")
                  ?>
                  <a data-delete="<?php echo $data ?>" class="btn-lk btn-lk--danger"
                    href="<?php echo url_for('staff/delete.php?table=topics&id=' . $topic->id)
                  ?>">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>