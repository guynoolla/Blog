<?php
use App\Classes\User;
use App\Classes\Pagination;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('/index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin


$current_page = $_GET['page'] ?? 1;
$per_page = DASHBOARD_PER_PAGE;
$total_count = User::countAll();
$pagination = new Pagination($current_page, $per_page, $total_count);

$users = User::queryUsersWithPostsNum($per_page, $pagination->offset());

$page_title = 'Users';
include SHARED_PATH . '/staff_header.php';
include '../_common-html-render.php';

?>
<div class="row">
  <aside class="sidebar col col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col col-lg-9">
    <div class="main-content adminContentJS">

      <h1 class="dashboard-headline">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
      </h1>

      <?php
      if (empty($users)):
        echo tableIsEmpty();

      else: ?>
        <?php echo tableSearchForm('Username') ?>

        <div class="loadContentJS" data-access="admin_user">
          <table class="table table-striped table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
            <thead class="bg-muted-lk text-muted">
              <tr>
                <th scope="col">#</th>
                <th scope="col"><a href="#username" class="click-load" data-access="admin_user" data-value="asc" data-type="username_order">Username</a></th>                
                <th scope="col">Email</th>
                <th scope="col"><a href="#user-type" class="click-load" data-access="admin_user" data-value="asc" data-type="userType_order">Type</a></th>
                <th scope="col"><a href="#since" class="click-load" data-access="admin_user" data-value="asc" data-type="date_order">Since</a></th>
                <th scope="col"><a href="#posted" class="click-load" data-access="admin_user" data-value="asc" data-type="approved_order">Posted</a></th>
                <th scope="colgroup" colspan="2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($users as $key => $user): ?>
              <tr data-user="<?php echo $user->id ?>">
                <th scope="row"><?php echo $key + 1 ?></th>
                <td><span><?php echo $user->username ?></span></td>
                <td><a href="mailto: <?php echo $user->email ?>" class="<?php echo ($user->email_confirmed ? 'text-success' : '') ?>"><?php echo $user->email ?></a></td>
                <td><a href="#user-type" data-type="user_type" data-value="<?php echo $user->user_type ?>" data-access="admin_user" class="click-load"><?php echo $user->user_type ?></a></td>
                <td><a href="#ondate" data-type="date" data-value="<?php echo $user->created_at ?>" data-access="admin_user" class="click-load"><?php echo date('M j, Y', strtotime($user->created_at)) ?></a></td>
                <td><?php echo $user->posted ?> - <span class="text-success font-weight-bold">
                  <?php echo $user->approved ?></span>
                </td>
                <td scope="colgroup" colspan="1">
                  <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/users/edit.php?id=' . $user->id) ?>">
                    Edit
                  </a>
                </td>
                <td scope="colgroup" colspan="1">
                  <?php $data = no_gaps_between("
                    table-users,
                    id-{$user->id},
                    username-{$user->username}
                  ") ?>
                  <a data-delete="<?php echo $data ?>" class="btn-lk btn-lk--danger"
                    href="<?php echo url_for('staff/delete.php?table=users&id=' . $user->id)
                  ?>">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          
          <?php
            $url = url_for('staff/users/index.php');
            echo $pagination->pageLinks($url);
          ?>
        </div>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>