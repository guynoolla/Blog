<?php
use App\Classes\User;
use App\Classes\Category;
use App\Classes\Pagination;

require_once '../../src/initialize.php';

if (!$session->isAdmin()) {
  exit;
}

$target = $_GET['target'] ?? "";
$user_id = $_GET['uid'] ?? "";

switch($target) {
  case 'admin_user_by_search':
  case 'admin_user_by_user_type':
  case 'admin_user_by_approved_order':
  case 'admin_user_by_username_order':
  case 'admin_user_by_userType_order':
  case 'admin_user_by_date_order':
  case 'admin_user_by_date':
          admin_user_data($_GET['data']);
          break;
  case 'admin_category_by_search':
  case 'admin_category_by_date_order':
  case 'admin_category_by_date':
  case 'admin_category_by_name_order':
          admin_category_data($_GET['data']);
          break;
  default:
          exit(json_encode(['target' => 'error']));
}

function admin_category_data($data) {
  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if ($type == 'search') {
    
    if ($value != "") {  // search
      $cond_arr = [["name LIKE '%?%'", $value]];
      $cond_str = "WHERE name LIKE '%{$value}%'";

    } elseif ($value == "") { // default
      $cond_arr = [];
      $cond_str = "";
    }

  } else if ($type == 'date') {

    $created = date('Y-m-d', strtotime($value));
    $nextday = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));
    $cond_arr = [[
      "( created_at >= '?' AND created_at < '?' )",
      $created,
      $nextday
    ]];
    $cond_str = "WHERE created_at >= '{$created}' AND created_at < '{$nextday}'";

  } else if ($type == 'date_order' || $type == 'name_order') {

    $cond_arr = [];
    $cond_str = "";
  }

  $total_count = Category::countAll($cond_arr);
  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);

  if ($type == 'date_order') $order = "ORDER BY created_at {$value}";
  else if ($type == 'name_order') $order = "ORDER BY name {$value}";
  else $order = 'ORDER BY name ASC';

  $categories = Category::findBySql(
    "SELECT * FROM `categories` {$cond_str} {$order}
    LIMIT {$per_page} OFFSET {$pagination->offset()}"
  );

  ob_start();

  ?><table class="table table-striped table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
    <thead class="bg-muted-lk text-muted">
      <tr>
        <th scope="col">#</th>
        <th scope="col"><a href="#name" class="click-load" data-access="admin_category" data-value="<?php echo ($value == 'asc' ? 'desc' : 'asc') ?>" data-type="name_order">Name</a></th>
        <th scope="col">Description</th>
        <th scope="col"><a href="#created" class="click-load" data-access="admin_category" data-value="<?php echo ($value == 'asc' ? 'desc' : 'asc') ?>" data-type="date_order">Created</a></th>
        <th scope="colgroup" colspan="2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($categories as $key => $category): ?>
        <tr>
          <th scope="row"><?php echo $key + 1 ?></th>
          <td><span class="h5"><?php echo $category->name ?></span></td>
          <td><?php echo $category->description ?></td>
          <td><a href="#ondate" class="click-load h5" data-type="date" data-value="<?php echo $category->created_at ?>" data-access="admin_category"><?php echo date('M j, Y', strtotime($category->created_at)) ?></span></td>
          <td scope="colgroup" colspan="1">
            <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/categories/edit.php?id=' . $category->id) ?>">
              Edit
            </a>
          </td>
          <td scope="colgroup" colspan="1">
            <?php
              $data = no_gaps_between("
                table-categories,
                id-{$category->id},
                name-{$category->name}
              ")
            ?>
            <a data-delete="<?php echo $data ?>" class="btn-lk btn-lk--danger"
              href="<?php echo url_for('staff/delete.php?table=categories&id=' . $category->id)
            ?>">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table><?php

  $output = ob_get_contents();
  ob_end_clean();

  $pag = [
    'total_count' => $total_count,
    'html' => $pagination->pageLinks($params['pathname'])
  ];

  if ($output) {
    exit(json_encode(['success', $output, $pag]));
  } else {
    exit(json_encode(['failed']));
  }
}

function admin_user_data($data) {
  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if ($type == 'search') {
    
    if ($value != "") {  // search
      $total_count = User::countAll([
        ["( username LIKE '%?%' )", $value]
      ]);

    } elseif ($value == "") { // default
      $total_count = User::countAll();
    }

  } else if ($type == 'user_type') {

    $total_count = User::countAll([
      'user_type' => $value
    ]);

  } elseif ($type == 'date') {

    $created = date('Y-m-d', strtotime($value));
    $nextday = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));
    $total_count = User::countAll([
      ["( created_at >= '?' AND created_at < '?' )", $created, $nextday]
    ]);

  } elseif ($type == 'date_order' || $type == 'userType_order' || $type == 'username_order' || $type == 'approved_order') {

    $total_count = User::countAll();
  }

  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);
  
  $sql = "SELECT u.*, COUNT(p.user_id) AS posted,";
  $sql .= " SUM(if (p.approved = '1', 1, 0)) AS approved";
  $sql .= " FROM `users` AS u LEFT JOIN `posts` AS p";
  $sql .= " ON u.id = p.user_id";
  if ($type == 'search' && $value != "") {
    $sql .= " WHERE u.username LIKE '%{$value}%'";
  } else if ($type == 'user_type') {
    $sql .= " WHERE u.user_type = '{$value}'";
  } else if ($type == 'date') {
    $sql .= " WHERE u.created_at >= '{$created}' AND u.created_at < '{$nextday}'";
  }
  if ($type == 'date_order') {
    $sql .= " GROUP BY u.id ORDER BY u.created_at {$value}";
  } else if ($type == 'userType_order') {
    $sql .= " GROUP BY u.id ORDER BY u.user_type {$value}, u.created_at ASC";
  } else if ($type == 'username_order') {
    $sql .= " GROUP BY u.id ORDER BY u.username {$value}";
  } else if ($type == 'approved_order') {
    $sql .= " GROUP BY u.id ORDER BY approved {$value}";
  } else {
    $sql .= " GROUP BY u.id ORDER BY u.username";
  }
  $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";

  $users = User::findBySql($sql);

  ob_start();

  ?><table class="table table-striped table-bordered table-hover table-light table-md">
    <thead class="bg-muted-lk text-muted">
      <tr>
        <th scope="col">#</th>
        <th scope="col"><a href="#username" class="click-load" data-access="admin_user" data-value="<?php echo ($value == 'asc' ? 'desc' : 'asc') ?>" data-type="username_order">Username</a></th>
        <th scope="col">Email</th>
        <th scope="col"><a href="#user-type" class="click-load" data-access="admin_user" data-value="<?php echo ($value == 'asc' ? 'desc' : 'asc') ?>" data-type="userType_order">Type</a></th>
        <th scope="col"><a href="#since" class="click-load" data-access="admin_user" data-value="<?php echo ($value == 'asc' ? 'desc' : 'asc') ?>" data-type="date_order">Since</a></th>
        <th scope="col"><a href="#posted" class="click-load" data-access="admin_user" data-value="<?php echo ($value == 'asc' ? 'desc' : 'asc') ?>" data-type="approved_order">Posted</a></th>
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
  </table><?php

  $output = ob_get_contents();
  ob_end_clean();

  $pag = [
    'total_count' => $total_count,
    'html' => $pagination->pageLinks($params['pathname'])
  ];

  if ($output) {
    exit(json_encode(['success', $output, $pag]));
  } else {
    exit(json_encode(['failed']));
  }
}