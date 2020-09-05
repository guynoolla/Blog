import $ from 'jquery';
import Cookies from 'js-cookie';

class Like {

  constructor() {
    this.events();
  }

  events() {
    if (!server.isLoggedIn) {
      let likeBox = $(".like-box");
      const postId = likeBox.attr('data-pid');
      this.clearClasses(likeBox);

      if (Cookies.get('lk_pid_liked') == postId) {
        likeBox.addClass("like-red");
        likeBox.attr('data-action', 'delete');
        this.increment(likeBox);
      } else {
        likeBox.addClass("like-default");
        likeBox.attr('data-action', 'create');
      }
    }

    $(".like-box").on("click", this.clickDispatcher.bind(this));
  }

  clickDispatcher(e) {
    let likeBox = $(e.target).closest(".like-box");
    
    if (!server.isLoggedIn) {
      const postId = likeBox.attr('data-pid');
      const action = likeBox.attr('data-action');

      if (action == 'create') {
        this.createLike(likeBox);
        Cookies.set('lk_pid_liked', postId, { expires: 365 });
      } else if (action == 'delete') {
        this.deleteLike(likeBox);
        Cookies.remove('lk_pid_liked');
      }

    } else {
      this.postLikeHandler(likeBox);
    }
  }

  postLikeHandler(likeBox) {
    $.ajax({
      url: server.baseUrl + '/ajax.php',
      type: 'POST',
      data: {
        target: 'like',
        post_id: likeBox.attr('data-pid'),
        user_id: likeBox.attr('data-uid'),
        action: likeBox.attr('data-action')
      },
      success: response => {
        const res = $.parseJSON(response);

        if (res.action == 'created') {
          this.createLike(likeBox);
        } else if (res.action == 'deleted') {
          this.deleteLike(likeBox);
        } else if (res.action == 'error') {
          console.log('Error: ' + res);
        }
      },
      error: res => console.log(res)
    });
  }

  createLike(likeBox) {
    likeBox.attr('data-action', 'delete');
    this.increment(likeBox);
    likeBox.toggleClass('like-red like-default');
  }

  deleteLike(likeBox) {
    likeBox.attr('data-action', 'create');
    this.decrement(likeBox);
    likeBox.toggleClass('like-red like-default');
  }

  clearClasses(likeBox) {
    likeBox.removeClass("like-red");
    likeBox.removeClass("like-default");
  }

  increment(likeBox) {
    let likeCount = parseInt(likeBox.find(".like-count").html(), 10);
    likeCount++;
    likeBox.find(".like-count").html(likeCount);    
  }

  decrement(likeBox) {
    let likeCount = parseInt(likeBox.find(".like-count").html(), 10);
    likeCount--;
    likeBox.find(".like-count").html(likeCount);
  }

}

export default Like;