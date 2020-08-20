<?php
use App\Classes\Post;
use App\Classes\Topic;

require_once '../src/initialize.php';

// Handle Contact Form Submit -->
if (is_post_request()) {
  $email = $_POST['email'] ?? '';
  $message = $_POST['message'] ?? '';

  if ($email && $message) {
    $mailer = new App\Contracts\Mailer;
    $text = strip_tags($message);
    
    $mailer->send(ADMIN_EMAIL,'Contact Form', $text, $message);
    $session->message('Thank you for your message!');
    redirect_to(url_for('index.php'));
  }
} // <--Contact Form

$trend_posts = Post::queryProvedPosts();

if (isset($_GET['search_term'])) {
  $term = $_GET['search_term'] ?? '';
  $posts = Post::querySearchPosts(trim($term));
  if ($posts) {
    $page_title = "You searched for '" . $term . "'";
  } else {
    $page_title = "Nothing found for '" . $term . "'";
  }
} elseif (isset($_GET['id'])) {
  $topic_id = $_GET['id'] ?? 0;
  $posts = Post::queryPostsByTopic($topic_id);
  $topic_name = Topic::findById($topic_id)->name;

  if ($posts) {
    $page_title = "You searched for posts under '" . $topic_name . "'";
  } else {
    $page_title = "Sorry, no posts under '" . $topic_name . "' found.";
  }
} else {
  $posts = Post::queryProvedPosts();
  $page_title = 'Recent Posts';
}

?><!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//fonts.googleapis.com/css?family=Lora:400,400italic,700,700italic|Montserrat:400,700|Maven+Pro:400,700" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo url_for('/assets/css/style.css') ?>">
<!--
Puzzle Template
http://www.templatemo.com/tm-477-puzzle
-->
    <title>Puzzle by templatemo</title>
</head>

<body>

    <div class="fixed-header">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>                        
                </button>
                <!-- <a class="navbar-brand" href="#">Puzzle</a> -->
            </div>
            <nav class="main-menu">
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#clients">Clients</a></li>
                    <li><a class="external" href="https://www.facebook.com/templatemo" target="_blank">External</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </div>


    <section class="col-md-12 content" id="banner">
        <div class="logo">
            <h1><a class="navbar-brand" href="#">Activello</a></h1>
            <div class="tagline">Just another site by Gainulla</div>
        </div>
        <div class="slider-wrap">
            <div class="-overlay"></div>
            <div class="slider" style="background-image: url(<?php echo url_for('assets/images/slider_01.jpg') ?>)"></div>
        </div>
    </section>

    <div class="container">
 
        <div class="row">
            <section class="main-content col-sm-12 col-md-8" id="home">
                <div  class="md-one-article-row" style="background:#FFF">
                    <article>
                        <div class="main-content-inner">
                            <div class="post">
                                <div class="blog-item-wrap">
                                    <div class="blog-item-inner">
                                        <h2 class="main-title text-center dark-blue-text">Puzzle Bootstrap Template</h2>
                                        <img src="<?php echo url_for('assets/images/1.jpg') ?>" alt="Image" class="tm-image">
                                        <div class="entry-content">
                                            <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                                            <button type="button" class="btn btn-big dark-blue-bordered-btn">Big Button</button>
                                            <button type="button" class="btn btn-big dark-blue-btn">Download</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article>
                        <div class="main-content-inner">
                            <div class="post">
                                <div class="blog-item-wrap">
                                    <div class="blog-item-inner">
                                        <h2 class="main-title text-center dark-blue-text">Puzzle Bootstrap Template</h2>
                                        <img src="<?php echo url_for('assets/images/2.jpg') ?>" alt="Image" class="tm-image">
                                        <div class="entry-content">
                                            <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                                            <button type="button" class="btn btn-big dark-blue-bordered-btn">Big Button</button>
                                            <button type="button" class="btn btn-big dark-blue-btn">Download</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="md-two-articles-row">
                    <article>
                        <div class="main-content-inner">
                            <div class="post">
                                <div class="blog-item-wrap">
                                    <div class="blog-item-inner">
                                        <h2 class="main-title text-center dark-blue-text">Puzzle Bootstrap Template</h2>
                                        <img src="<?php echo url_for('assets/images/3.jpg') ?>" alt="Image" class="tm-image">
                                        <div class="entry-content">
                                            <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article>
                        <div class="main-content-inner">
                            <div class="post">
                                <div class="blog-item-wrap">
                                    <div class="blog-item-inner">
                                        <h2 class="main-title text-center dark-blue-text">Puzzle Bootstrap Template</h2>
                                        <img src="<?php echo url_for('assets/images/4.jpg') ?>" alt="Image" class="tm-image">
                                        <div class="entry-content">
                                            <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="md-two-articles-row">
                    <article>
                        <div class="main-content-inner">
                            <div class="post">
                                <div class="blog-item-wrap">
                                    <div class="blog-item-inner">
                                        <h2 class="main-title text-center dark-blue-text">Puzzle Bootstrap Template</h2>
                                        <img src="<?php echo url_for('assets/images/3.jpg') ?>" alt="Image" class="tm-image">
                                        <div class="entry-content">
                                            <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article>
                        <div class="main-content-inner">
                            <div class="post">
                                <div class="blog-item-wrap">
                                    <div class="blog-item-inner">
                                        <h2 class="main-title text-center dark-blue-text">Puzzle Bootstrap Template</h2>
                                        <img src="<?php echo url_for('assets/images/4.jpg') ?>" alt="Image" class="tm-image">
                                        <div class="entry-content">
                                            <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="col-sm-12 col-md-4" id="secondary">
                <div class="widget-area" role="complementary">
                    <div class="inner">

                        <aside id="activello_recent_posts-2" class="widget activello-recent-posts">
                            <h3 class="widget-title">Recent Posts</h3>

                            <div class="recent-posts-wrapper">

                                <div class="post">

                                    <div class="post-image ">
                                        <a href="https://colorlib.com/activello/post-format-standard/"><img width="150" height="150" src="https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1438109491414-7198515b166b-150x150.jpg" class="attachment-thumbnail size-thumbnail wp-post-image" alt="" loading="lazy" srcset="https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1438109491414-7198515b166b-150x150.jpg 150w, https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1438109491414-7198515b166b-180x180.jpg 180w, https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1438109491414-7198515b166b-300x300.jpg 300w, https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1438109491414-7198515b166b-600x600.jpg 600w" sizes="(max-width: 150px) 100vw, 150px"></a>
                                    </div> 

                                    <div class="post-content">
                                        <a href="https://colorlib.com/activello/post-format-standard/">Post Format: Standard</a>
                                        <span class="date">- 05 Oct , 2016</span>
                                    </div>
                                </div>

                                <div class="post">

                                    <div class="post-image gallery">
                                        <a href="https://colorlib.com/activello/post-format-gallery/"><img width="150" height="150" src="https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1429734160945-4f85244d6a5a-150x150.jpg" class="attachment-thumbnail size-thumbnail wp-post-image" alt="" loading="lazy" srcset="https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1429734160945-4f85244d6a5a-150x150.jpg 150w, https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1429734160945-4f85244d6a5a-180x180.jpg 180w, https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1429734160945-4f85244d6a5a-300x300.jpg 300w, https://colorlib.com/activello/wp-content/uploads/sites/10/2015/11/photo-1429734160945-4f85244d6a5a-600x600.jpg 600w" sizes="(max-width: 150px) 100vw, 150px"></a>
                                    </div> 

                                    <div class="post-content">
                                        <a href="https://colorlib.com/activello/post-format-gallery/">Post Format: Gallery</a>
                                        <span class="date">- 12 Nov , 2015</span>
                                    </div>
                                </div>

                                <div class="post">
                                    <div class="post-image ">
                                        <a href="https://colorlib.com/activello/template-featured-image-vertical/"><img width="150" height="150" src="https://colorlib.com/activello/wp-content/uploads/sites/10/2012/03/photo-1437915015400-137312b61975-150x150.jpg" class="attachment-thumbnail size-thumbnail wp-post-image" alt="" loading="lazy" srcset="https://colorlib.com/activello/wp-content/uploads/sites/10/2012/03/photo-1437915015400-137312b61975-150x150.jpg 150w, https://colorlib.com/activello/wp-content/uploads/sites/10/2012/03/photo-1437915015400-137312b61975-180x180.jpg 180w, https://colorlib.com/activello/wp-content/uploads/sites/10/2012/03/photo-1437915015400-137312b61975-300x300.jpg 300w, https://colorlib.com/activello/wp-content/uploads/sites/10/2012/03/photo-1437915015400-137312b61975-600x600.jpg 600w" sizes="(max-width: 150px) 100vw, 150px"></a>
                                    </div> 

                                    <div class="post-content">
                                        <a href="https://colorlib.com/activello/template-featured-image-vertical/">Template: Featured Image (No Sidebar Layout)</a>
                                        <span class="date">- 11 Nov , 2015</span>
                                    </div>
                                </div>
                            </div> 
                        </aside>

                        <aside id="search-2" class="widget widget_search">
                            <form role="search" method="get" class="form-search" action="https://colorlib.com/activello/">
                                <div class="input-group">
                                <label class="screen-reader-text" for="s">Search for:</label>
                                <input type="text" class="form-control search-query" placeholder="Search…" value="" name="s" title="Search for:">
                                <span class="input-group-btn">
                                <button type="submit" class="btn btn-default" name="submit" id="searchsubmit" value="Search">Search</button>
                                </span>
                                </div>
                            </form>
                        </aside>

                        <aside id="activello-cats-2" class="widget activello-cats">
                            <h3 class="widget-title">Categories</h3>
                            <div class="cats-widget">
                            <ul> <li class="cat-item cat-item-38"><a href="https://colorlib.com/activello/category/post-formats/" title="Posts in this category test post formats.">Post Formats</a> <span>11</span>
                            </li>
                            <li class="cat-item cat-item-49"><a href="https://colorlib.com/activello/category/template-2/" title="Posts with template-related tests">Template</a> <span>5</span>
                            </li>
                            <li class="cat-item cat-item-9"><a href="https://colorlib.com/activello/category/cat-a/">Cat A</a> <span>3</span>
                            </li>
                            <li class="cat-item cat-item-19"><a href="https://colorlib.com/activello/category/edge-case-2/" title="Posts that have edge-case related tests">Edge Case</a> <span>3</span>
                            </li>
                            <li class="cat-item cat-item-10"><a href="https://colorlib.com/activello/category/cat-b/">Cat B</a> <span>2</span>
                            </li>
                            </ul>
                            </div>
                        </aside>

                        <aside id="activello-social-2" class="widget activello-social"><h3 class="widget-title">Follow Me</h3>

                            <div class="social-icons sticky-sidebar-social">
                                <nav id="social" class="social-icons"><ul id="menu-social-items" class="social-menu"><li id="menu-item-1734" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1734"><a href="https://www.facebook.com/colorlib"><i class="social_icon fa fa-facebook"><span>Facebook</span></i></a></li>
                                <li id="menu-item-1735" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1735"><a href="https://twitter.com/colorlib"><i class="social_icon fa fa-twitter"><span>Twitter</span></i></a></li>
                                <li id="menu-item-1736" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1736"><a href="https://www.youtube.com/channel/UCOaovjLNXdIch2vLFsw_uew"><i class="social_icon fa fa-youtube"><span>youtube</span></i></a></li>
                                <li id="menu-item-1737" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1737"><a href="https://plus.google.com/100289203607749737039"><i class="social_icon fa fa-google-plus"><span>Google+</span></i></a></li>
                                <li id="menu-item-1738" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1738"><a href="https://instagram.com"><i class="social_icon fa fa-instagram"><span>Instagram</span></i></a></li>
                                <li id="menu-item-1739" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1739"><a href="https://github.com/puikinsh/"><i class="social_icon fa fa-github"><span>Github</span></i></a></li>
                                </ul></nav>
                            </div>
                        </aside>

                        <aside id="text-5" class="widget widget_text"> <div class="textwidget">Any text goes here</div>
                        </aside>
                        <aside id="text-4" class="widget widget_text"><h3 class="widget-title">This Theme Is AdSense Ready</h3> <div class="textwidget"><script async="" src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" type="text/javascript"></script>

                        <!-- <ins class="adsbygoogle" style="display:inline-block;width:336px;height:280px" data-ad-client="ca-pub-7397764995846596" data-ad-slot="2032203251" data-adsbygoogle-status="done"><ins id="aswift_0_expand" style="display:inline-table;border:none;height:280px;margin:0;padding:0;position:relative;visibility:visible;width:336px;background-color:transparent;"><ins id="aswift_0_anchor" style="display:block;border:none;height:280px;margin:0;padding:0;position:relative;visibility:visible;width:336px;background-color:transparent;"><iframe id="aswift_0" name="aswift_0" style="left:0;position:absolute;top:0;border:0;width:336px;height:280px;" sandbox="allow-forms allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts allow-top-navigation-by-user-activation" width="336" height="280" frameborder="0" src="https://googleads.g.doubleclick.net/pagead/ads?client=ca-pub-7397764995846596&amp;output=html&amp;h=280&amp;slotname=2032203251&amp;adk=4106803563&amp;adf=3791352688&amp;w=336&amp;lmt=1597903301&amp;psa=1&amp;guci=2.2.0.0.2.2.0.0&amp;format=336x280&amp;url=https%3A%2F%2Fcolorlib.com%2Factivello%2F&amp;flash=0&amp;wgl=1&amp;dt=1597903301208&amp;bpp=13&amp;bdt=398&amp;idt=63&amp;shv=r20200817&amp;cbv=r20190131&amp;ptt=9&amp;saldr=aa&amp;abxe=1&amp;cookie=ID%3D188bfa0a3a98771e%3AT%3D1597165128%3AS%3DALNI_MZ51BSOGFsnKpJNAfjHcNT5dhlu8Q&amp;correlator=3607671340443&amp;frm=20&amp;pv=2&amp;ga_vid=947890333.1597165125&amp;ga_sid=1597903301&amp;ga_hid=771720273&amp;ga_fc=0&amp;iag=0&amp;icsg=9663590396&amp;dssz=28&amp;mdo=0&amp;mso=0&amp;u_tz=300&amp;u_his=5&amp;u_java=0&amp;u_h=768&amp;u_w=1366&amp;u_ah=738&amp;u_aw=1366&amp;u_cd=24&amp;u_nplug=3&amp;u_nmime=4&amp;adx=871&amp;ady=1798&amp;biw=1349&amp;bih=667&amp;scr_x=0&amp;scr_y=1610&amp;eid=42530557%2C42530559%2C21066154%2C21066790%2C21066920%2C21066612&amp;oid=3&amp;pvsid=1404511627361139&amp;pem=386&amp;ref=https%3A%2F%2Fcolorlib.com%2Factivello%2F&amp;rx=0&amp;eae=0&amp;fc=640&amp;brdim=0%2C0%2C0%2C0%2C1366%2C0%2C1366%2C738%2C1366%2C667&amp;vis=1&amp;rsz=%7C%7CoeE%7C&amp;abl=CS&amp;pfx=0&amp;fu=8192&amp;bc=31&amp;ifi=1&amp;uci=a!1&amp;fsb=1&amp;xpc=973BjbfKWi&amp;p=https%3A//colorlib.com&amp;dtd=77" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" scrolling="no" allowfullscreen="true" data-google-container-id="a!1" data-load-complete="true"></iframe></ins></ins></ins>
                        <script type="text/javascript">
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script></div> -->
                        </aside>
                    </div>
                </div>
            </section>
        </div>

        <!-- <footer class="col-md-12 content" id="externals">
            <div class="col-lg-6 col-md-6 last">
            <img src="<?php echo url_for('assets/images/1.jpg') ?>" alt="Image" class="tm-image">
            </div>
            <div class="col-lg-6 col-md-6 background last about-text-container">
            <h2 class="section-title">About This Website</h2>
            <p class="about-text">Puzzle Template is brought to you by templatemo. Sed imperdiet quis ipsum a vulputate. Suspendisse sit amet nibh mi. In quis sapien a metus interdum hendrerit.</p>       
            </div>
        </footer> -->

    </div> <!-- container -->

    <div id="footer-area">
        <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="site-info container" id="contact">
            <div class="row">
            <nav id="social" class="social-icons"><ul id="menu-social-items" class="social-menu"><li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1734"><a href="https://www.facebook.com/colorlib"><i class="social_icon fa fa-facebook"><span>Facebook</span></i></a></li>
            <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1735"><a href="https://twitter.com/colorlib"><i class="social_icon fa fa-twitter"><span>Twitter</span></i></a></li>
            <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1736"><a href="https://www.youtube.com/channel/UCOaovjLNXdIch2vLFsw_uew"><i class="social_icon fa fa-youtube"><span>youtube</span></i></a></li>
            <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1737"><a href="https://plus.google.com/100289203607749737039"><i class="social_icon fa fa-google-plus"><span>Google+</span></i></a></li>
            <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1738"><a href="https://instagram.com"><i class="social_icon fa fa-instagram"><span>Instagram</span></i></a></li>
            <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1739"><a href="https://github.com/puikinsh/"><i class="social_icon fa fa-github"><span>Github</span></i></a></li>
            </ul></nav> <div class="copyright col-md-12">
            Activello. Theme by <a href="https://colorlib.com/" target="_blank">Colorlib</a> Powered by <a href="https://wordpress.org/" target="_blank">WordPress</a> </div>
            </div>
            </div>
            <button class="scroll-to-top" style="display: block;"><i class="fa fa-angle-up"></i></button>
        </footer>
    </div>

  <script src="<?php echo url_for('assets/js/main.js') ?>" type="text/javascript"></script>
</body>
</html>
