import $, { data } from 'jquery';
import Cookies from 'js-cookie';

class Like {

  constructor() {
    this.currentLikeBox = $(".like-box");
    this.postId = this.currentLikeBox.attr('data-pid');
    
    this.onload();
    this.events();
  }

  onload() {
    let likeBox = this.currentLikeBox;
    const postId = this.postId;

    if (!server.isLoggedIn) {
      this.clearClasses(likeBox);
      likeBox.addClass("like-default");
      likeBox.attr('data-action', 'create');

      let data = this.getCookie();

      if (data.length > 0) {
        data.forEach((pid, idx, obj) => {
          if (pid == postId) {
            this.clearClasses(likeBox);
            likeBox.addClass("like-red")
            likeBox.attr('data-action', 'delete')
            this.increment(likeBox)
          }
        });
      }
    
    } else {
      if (server.singlePost) {
        const postId = likeBox.attr('data-pid');
        const action = likeBox.attr('data-action');

        if (action == 'create') {
          let data = this.getCookie();

          data.forEach((pid, idx, obj) => {
            if (pid == server.singlePost && pid == postId) {
              this.postLikeHandler(likeBox);
              return false;
            }
          })
        }
      }
    }
  }

  events() {
    $(".like-box").on("click", this.clickDispatcher.bind(this));
  }

  clickDispatcher(e) {
    let likeBox = $(e.target).closest(".like-box");
    
    if (!server.isLoggedIn) {
      const action = likeBox.attr('data-action');
      let data = this.getCookie();

      if (action == 'create') {
        this.addToCookie(data, likeBox);
        this.createLike(likeBox);

      } else if (action == 'delete') {
        this.deleteLike(likeBox);
        this.deleteFromCookie(data, likeBox);
      }

    } else {
      this.postLikeHandler(likeBox);
    }
  }

  getCookie() {
    let data = [];
    if (Cookies.get('lk_pid_liked')) {
      data = (Cookies.get('lk_pid_liked')).split('-');
    }
    return data;
  }

  addToCookie(data, likeBox) {
    const postId = likeBox.attr('data-pid');
    let exists = false;
    data.forEach((pid, idx, obj) => {
      if (pid == postId) {
        exists = true;
        return false;
      }
    })
    if (!exists) {
      data.push(postId);
      Cookies.set('lk_pid_liked', data.join("-"), { expires: 365 });
    }
  }

  deleteFromCookie(data, likeBox) {
    const postId = likeBox.attr('data-pid');
    data.forEach((pid, idx, obj) => {
      if (pid == postId) {
        obj.splice(idx, 1);
      }
    })
    if (data.length > 0) {
      Cookies.set('lk_pid_liked', data.join("-"), { expires: 365 });
    } else {
      Cookies.remove('lk_pid_liked');
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
      success: res => {
        const data = JSON.parse(res);

        if (data.action == 'created') {
          this.createLike(likeBox);
          this.addToCookie(this.getCookie(), likeBox);
        } else if (data.action == 'deleted') {
          this.deleteLike(likeBox);
          this.deleteFromCookie(this.getCookie(), likeBox);
        } else if (data.action == 'error') {
          console.log('Error: ' + data);
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