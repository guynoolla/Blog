import $ from 'jquery';
import Cookies from 'js-cookie';

class Posts {

  constructor() {
    this.cookieData = (this.getLikesCookie()).reverse();
    this.perPage = 2;
    this.page = 1;
    this.onload();
    this.events();
  }

  events() {
    $(".loadPostsJS").on("click", ".page-link", (e) => {
      e.preventDefault();

      const target = $(e.target);

      if (target.is("span")) {
        target.closest(".page-link").trigger("click");
        return;
      }
      if (target.parent().hasClass("active")) {
        return;
      }

      let url = target.attr("href");
      
      if (url) {
        const params = url.split("?")[1];
        const paramsArr = params.split("&");
        paramsArr.forEach(value => {
          if (value.split("=")[0] == 'page') {
            this.page = value.split("=")[1];
            $(".pagination li.page-item").removeClass("active");
            $(`#item-${this.page}`).addClass("active");
            this.loadPosts(this.cookieData);
            return false;
          }
        });
      }
    });
  }

  offset() {
    return this.perPage * (this.page - 1);
  }

  onload() {
    this.loadPosts(this.cookieData);
    this.page++;
  }

  loadPosts(ids) {
    if (server.dashboardMain) {
      $(".loadPostsJS").fadeOut(600, () => {
        $(".loading").removeClass("d-none");
      });

      console.log('IDS: ', ids.length);

      $.ajax({
        url: server.baseUrl + '/ajax.php',
        type: 'POST',
        data: {
          target: 'posts_by_ids',
          user_id: server.userId,
          ids: JSON.stringify(ids),
          per_page: this.perPage,
          offset: this.offset(),
          page: this.page
        },
        success: res => {
          const data = JSON.parse(res);
          if (data[0] == 'success') {
            const output = this.makeHtml(data[1], data[2])
            //let timer = setTimeout(() => {
              $(".loadPostsJS").fadeIn(() => {
                $(".loading").addClass("d-none")
              })
              $(".loadPostsJS").html(output)
              //clearTimeout(timer)
            //}, 1000)
            $('#page-' + this.page).addClass("active")
          }
        },
        error: res => console.log(res)
      });
    }
  }

  getLikesCookie() {
    let data = [];
    if (Cookies.get('lk_pid_liked')) {
      data = (Cookies.get('lk_pid_liked')).split('-');
    }
    return data;
  }

  makeHtml(posts, pagination) {
    let output = "";
    let postsInside = 0;
    const len = posts.length;
    let width = len == 1 ? ' col-md-6' : '';

    $.each(posts, (idx, post) => {
      let num = idx + 1;
      if (num == len) {
        width = !(len % 2 == 0) ? ' col-md-6' : '';
      }

      if (!(num % 2 == 0)) {
        output += `<div class="lg-two-articles-row${width}">`;
      }
        postsInside++;

        output += `
        <article>
          <div class="post"><div class="post-item-wrap"><div class="post-item-inner">
            <h3 class="entry-title text-center mt-0 mb-1">
              <a href="${post.to_single}">${post.title}</a>
            </h3>
            <div class="entry-meta">
              <span class="posted-on">Posted on <a href="${post.to_on_date}">
              <time class="entry-date published" datetime="${post.created_at}">
                ${post.created_at}
              </time></a></span>by
              <span><a href="${post.to_author}">${post.username}</a></span>
            </div>
            <div class="post-format ${post.format == 'video' ? 'post-format--video' : ''}">`;
              if (post.format == 'image') {
                output += `<a href="${post.to_single}">
                  <div class="ard ard--tall">
                    <img class="ard-image ard-image--center ard-image--wide"
                    srcset="${post.image}" alt="${post.title}">
                  </div>
                </a>`;
              } else if (post.format == 'video') {
                output += `<div class="embed-responsive embed-responsive-4by3">
                  ${post.video}<a class="overlay" href="${post.to_single}"></a>
                </div>`;
              }
            output += `</div>
            <a href="${post.to_topic}" class="category category--dark text-center mt-2">
              ${post.topic}
            </a>`;
            output += `<div class="entry-content mt-4">${post.excerpt}</div>
          </div></div></div>
        </article>`;
      
      if (postsInside == 2) {
        output += `</div>`;
        postsInside = 0;
      }
    });

    if (len == 1) output += `</div>`;

    output += pagination.html;

    return output;
  }

}

export default Posts;