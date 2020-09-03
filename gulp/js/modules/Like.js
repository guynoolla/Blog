import $ from 'jquery';

class Like {

  constructor() {
    this.events();
  }

  events() {
    $(".like-box").on("click", this.clickDispatcher.bind(this));
  }

  clickDispatcher(e) {
    if (!server.isLoggedIn) {
      console.log('You must be logged in to like a post!');
      return;
    }
    let currentLikeBox = $(e.target).closest(".like-box");
    this.postLikeHandler(currentLikeBox);
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
          likeBox.attr('data-action', 'delete');
          let likeCount = parseInt(likeBox.find(".like-count").html(), 10);
          likeCount++;
          likeBox.find(".like-count").html(likeCount);
          likeBox.toggleClass('like-red like-default');

        } else if (res.action == 'deleted') {
          likeBox.attr('data-action', 'create');
          let likeCount = parseInt(likeBox.find(".like-count").html(), 10);
          likeCount--;
          likeBox.find(".like-count").html(likeCount);
          likeBox.toggleClass('like-red like-default');
        } else if (res.action == 'error') {
          console.log('Error: ' + res);
        }
      },
      error: res => console.log(res)
    });
  }

}

export default Like;