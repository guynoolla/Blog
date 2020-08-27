<?php
use App\Classes\Post;
use App\Classes\User;

require_once('../../src/initialize.php');

// Check LoggedIn >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//require_login();
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check LoggedIn

//$user = User::findById($session->getUserId());

include SHARED_PATH . '/staff_header.php';
?>

  <div class="container-xl">
    <div class="page-admin">
      <div class="row">
        <div class="topbox col-12"></div>
      </div>
      <div class="row">
        <aside class="sidebar col-lg-3">
          <ul class="sidebar-nav">
            <li class="nav-item logo">
              <a href="#" class="nav-link">
                <span class="link-text logo-text">Dashboard</span>
                <svg
                  aria-hidden="true"
                  focusable="false"
                  data-prefix="fad"
                  data-icon="angle-double-right"
                  role="img"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 448 512"
                  class="svg-inline--fa fa-angle-double-right fa-w-14 fa-5x"
                >
                  <g class="fa-group">
                    <path
                      fill="currentColor"
                      d="M224 273L88.37 409a23.78 23.78 0 0 1-33.8 0L32 386.36a23.94 23.94 0 0 1 0-33.89l96.13-96.37L32 159.73a23.94 23.94 0 0 1 0-33.89l22.44-22.79a23.78 23.78 0 0 1 33.8 0L223.88 239a23.94 23.94 0 0 1 .1 34z"
                      class="fa-secondary"
                    ></path>
                    <path
                      fill="currentColor"
                      d="M415.89 273L280.34 409a23.77 23.77 0 0 1-33.79 0L224 386.26a23.94 23.94 0 0 1 0-33.89L320.11 256l-96-96.47a23.94 23.94 0 0 1 0-33.89l22.52-22.59a23.77 23.77 0 0 1 33.79 0L416 239a24 24 0 0 1-.11 34z"
                      class="fa-primary"
                    ></path>
                  </g>
                </svg>
              </a>
            </li>
      
            <li class="nav-item">
              <a href="#" class="nav-link">
                <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="edit" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-edit fa-w-18 fa-5x"><path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z" class=""></path></svg>
                <span class="link-text">Add Post</span>
              </a>
            </li>
      
            <li class="nav-item">
              <a href="#" class="nav-link">
                <svg style="margin-bottom:-7px" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="thumbtack" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 484 612" class="svg-inline--fa fa-thumbtack fa-w-12 fa-2x"><path fill="currentColor" d="M298.028 214.267L285.793 96H328c13.255 0 24-10.745 24-24V24c0-13.255-10.745-24-24-24H56C42.745 0 32 10.745 32 24v48c0 13.255 10.745 24 24 24h42.207L85.972 214.267C37.465 236.82 0 277.261 0 328c0 13.255 10.745 24 24 24h136v104.007c0 1.242.289 2.467.845 3.578l24 48c2.941 5.882 11.364 5.893 14.311 0l24-48a8.008 8.008 0 0 0 .845-3.578V352h136c13.255 0 24-10.745 24-24-.001-51.183-37.983-91.42-85.973-113.733z" class=""></path></svg>
                <span class="link-text">Admin Posts</span>
              </a>
            </li>
      
            <li class="nav-item">
              <a href="#" class="nav-link">
                <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="sticky-note" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 518 572" class="svg-inline--fa fa-sticky-note fa-w-14 fa-3x"><path fill="currentColor" d="M448 348.106V80c0-26.51-21.49-48-48-48H48C21.49 32 0 53.49 0 80v351.988c0 26.51 21.49 48 48 48h268.118a48 48 0 0 0 33.941-14.059l83.882-83.882A48 48 0 0 0 448 348.106zm-128 80v-76.118h76.118L320 428.106zM400 80v223.988H296c-13.255 0-24 10.745-24 24v104H48V80h352z" class=""></path></svg>
                <span class="link-text">Posts - <i>Drafts</i></span>
              </a>
            </li>
      
            <li class="nav-item">
              <a href="#" class="nav-link">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="shield-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 532 532" class="svg-inline--fa fa-shield-alt fa-w-16 fa-3x"><path fill="currentColor" d="M466.5 83.7l-192-80a48.15 48.15 0 0 0-36.9 0l-192 80C27.7 91.1 16 108.6 16 128c0 198.5 114.5 335.7 221.5 380.3 11.8 4.9 25.1 4.9 36.9 0C360.1 472.6 496 349.3 496 128c0-19.4-11.7-36.9-29.5-44.3zM256.1 446.3l-.1-381 175.9 73.3c-3.3 151.4-82.1 261.1-175.8 307.7z" class=""></path></svg>
                <span class="link-text">Posts - <i>Moderation</i></span>
              </a>
            </li>
      
            <li class="nav-item" id="themeButton">
              <a href="#" class="nav-link">
                <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="check-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 498 562" class="svg-inline--fa fa-check-square fa-w-14 fa-3x"><path fill="currentColor" d="M400 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V80c0-26.51-21.49-48-48-48zm0 400H48V80h352v352zm-35.864-241.724L191.547 361.48c-4.705 4.667-12.303 4.637-16.97-.068l-90.781-91.516c-4.667-4.705-4.637-12.303.069-16.971l22.719-22.536c4.705-4.667 12.303-4.637 16.97.069l59.792 60.277 141.352-140.216c4.705-4.667 12.303-4.637 16.97.068l22.536 22.718c4.667 4.706 4.637 12.304-.068 16.971z" class=""></path></svg>
                <span class="link-text">Posts - <i>Proved</i></span>
              </a>
            </li>

            <li class="nav-item" id="themeButton">
              <a href="#" class="nav-link">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="paperclip" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 548 612" class="svg-inline--fa fa-paperclip fa-w-14 fa-3x"><path fill="currentColor" d="M43.246 466.142c-58.43-60.289-57.341-157.511 1.386-217.581L254.392 34c44.316-45.332 116.351-45.336 160.671 0 43.89 44.894 43.943 117.329 0 162.276L232.214 383.128c-29.855 30.537-78.633 30.111-107.982-.998-28.275-29.97-27.368-77.473 1.452-106.953l143.743-146.835c6.182-6.314 16.312-6.422 22.626-.241l22.861 22.379c6.315 6.182 6.422 16.312.241 22.626L171.427 319.927c-4.932 5.045-5.236 13.428-.648 18.292 4.372 4.634 11.245 4.711 15.688.165l182.849-186.851c19.613-20.062 19.613-52.725-.011-72.798-19.189-19.627-49.957-19.637-69.154 0L90.39 293.295c-34.763 35.56-35.299 93.12-1.191 128.313 34.01 35.093 88.985 35.137 123.058.286l172.06-175.999c6.177-6.319 16.307-6.433 22.626-.256l22.877 22.364c6.319 6.177 6.434 16.307.256 22.626l-172.06 175.998c-59.576 60.938-155.943 60.216-214.77-.485z" class=""></path></svg>
                <span class="link-text">Topics</span>
              </a>
            </li>

            <li class="nav-item" id="themeButton">
              <a href="#" class="nav-link">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="users" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-users fa-w-20 fa-5x"><path fill="currentColor" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" class=""></path></svg>
                <span class="link-text">Users</span>
              </a>
            </li>
          </ul>

        </aside>
        <main class="main col-lg-9">
          <div class="main-content">
            <button type="button" class="btn btn-primary btn-sm ml-auto mb-1">Back</button>

            <h1>Add New Post</h1>

            <form>
              <div class="form-group">
                <label for="exampleInputEmail1">Title</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
              </div>
              <div class="form-group">
                <label for="exampleFormControlTextarea1">Content</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
              </div>
              <div class="form-group">
                <label for="exampleFormControlFile1">Select Post Image</label>
                <input type="file" class="form-control-file" id="exampleFormControlFile1">
              </div>
              <div class="form-group mt-4">
                <select class="form-control">
                  <option>Default select</option>
                </select>
              </div>
              <div class="custom-control custom-switch mt-4">
                <input type="checkbox" class="custom-control-input" id="customSwitch1">
                <label class="custom-control-label" for="customSwitch1">Publish</label>
                <small id="emailHelp" class="form-text text-muted">Leave it as Draft or Publish</small>
              </div>

              <button type="submit" class="btn btn-primary float-right">Save</button>
            </form>

            <div class="clearfix"><br><br><br></div>



            <h1 class="text-center mt-3">Admin Posts</h1>

            <table class="table table-bordered table-hover table-light">
              <thead class="bg-secondary text-white">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Title</th>
                  <th scope="col">Author</th>
                  <th scope="col">Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th scope="row">1</th>
                  <td>Mark</td>
                  <td>Otto</td>
                  <td class="text-danger font-weight-bold">published</td>
                  <td class="table-active font-weight-bold">
                    <a href="#" class="text-primary">prove</a>
                  </td>
                </tr>
                <tr>
                  <th scope="row">2</th>
                  <td>Jacob</td>
                  <td>Thornton</td>
                  <td class="text-secondary font-weight-bold">draft</td>
                  <td class="table-active font-weight-bold">
                    <a href="#" class="text-secondary">&mdash;</a>
                  </td>
                </tr>
                <tr>
                  <th scope="row">3</th>
                  <td colspan="2">Larry the Bird</td>
                  <td class="text-success font-weight-bold">proved</td>
                  <td class="table-active font-weight-bold">
                    <a href="#" class="text-primary">disprove</a>
                  </td>
                </tr>
              </tbody>
            </table>

          </div>
        </main>
      </div>
    </div>
  </div>  

  <footer class="footer" role="contentinfo">
    <div class="footer-content">
      <div class="social-links-widget more-space-between">
        <ul class="menu-social-items" class="social-menu">
          <li class="menu-item menu-item-type-custom">
            <a href="https://www.facebook.com/colorlib">
              <i class="social_icon fa fa-facebook"><span>Facebook</span></i>
            </a>
          </li>
          <li class="menu-item menu-item-type-custom">
            <a href="https://twitter.com/colorlib">
              <i class="social_icon fa fa-twitter"><span>Twitter</span></i>
            </a>
          </li>
          <li class="menu-item menu-item-type-custom">
            <a href="https://www.youtube.com/channel/UCOaovjLNXdIch2vLFsw_uew">
            <i class="social_icon fa fa-youtube"><span>youtube</span></i></a>
          </li>
          <li class="menu-item menu-item-type-custom">
            <a href="https://plus.google.com/100289203607749737039">
              <i class="social_icon fa fa-google-plus"><span>Google+</span></i>
            </a>
          </li>
          <li class="menu-item menu-item-type-custom">
            <a href="https://instagram.com">
              <i class="social_icon fa fa-instagram"><span>Instagram</span></i>
            </a>
          </li>
          <li class="menu-item menu-item-type-custom">
            <a href="https://github.com/puikinsh/">
              <i class="social_icon fa fa-github"><span>Github</span></i>
            </a>
          </li>
        </ul>
      </div>
      <div class="copyright mt-4">
        Light Kite. Theme by <a href="https://colorlib.com/" target="_blank">Gainulla</a> web developer
      </div>
    </div>
    <a href="#scrollTop" class="scroll-to-top" style="display: block;"><i class="fa fa-angle-up"></i></a>
  </footer>

  <script src="/assets/js/main.js" type="text/javascript"></script>
</body>
</html>

<?php
////////////////////////////////////////////////////////////////////////////
?>

<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = ($session->isAdmin() ? 'Admin Posts' : 'User Posts');
    include SHARED_PATH . '/staff_header.php';
  ?>

  <div class="admin-wrapper clearfix">

    <?php include SHARED_PATH . '/staff_sidebar.php'; ?>

    <div class="admin-content clearfix">
      <div class="">

        <h2 style="margin-left:0">Logged In</h2>

        <?php echo display_session_message(); ?>

        <?php if (!$user->isEmailConfirmed()): ?>
          <p class="lead">
            Welcome, <strong><?php echo $user->username ?></strong>! Now you are logged in and can like posts.<br>
            You, also will be able to add posts if you confirm your email address.<br>
            Please, <a href="<?php echo url_for('email/confirm_mail.php?email=' . $user->email) ?>" class="link-underlined">confirm</a> you email address if you want to have author capability.
          </p>

        <?php else: ?>

          <!-- Email Confirmed User -->

        <?php endif; ?>

      </div>
    </div>

  </div>

  <?php include SHARED_PATH . '/staff_footer.php'; ?>
</body>

</html>