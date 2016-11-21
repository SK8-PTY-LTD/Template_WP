<?php
function spider_featured_themes($current_plugin = '') {
 $themes = array(
     "business_elite" => array(
      'title'    => 'Business Elite',
      'content'  => 'Business Elite is a robust parallax theme for business websites. The theme uses smooth transitions and many functional sections.',
      'href'     => 'https://web-dorado.com/wordpress-themes/business-elite.html',
      "demo"     => "http://themedemo.web-dorado.com/theme-businesselite/"
    ),
    "portfolio_gallery" => array(
      'title'    => 'Portfolio Gallery',
      'content'  => 'Portfolio Gallery helps to display images using various color schemes and layouts combined with elegant fonts and content parts.',
      'href'     => 'https://web-dorado.com/wordpress-themes/portfolio-gallery.html',
      "demo"     => "http://themedemo.web-dorado.com/theme-portfoliogallery/"
    ),
    "sauron" => array(
      'title'    => 'Sauron',
      'content'  => 'Sauron is a multipurpose parallax theme, which uses multiple interactive sections designed for the client-engagement.',
      'href'     => 'https://web-dorado.com/wordpress-themes/sauron.html',
      "demo"     => "http://themedemo.web-dorado.com/theme-sauron/"
    ),
     "business_world" => array(
      'title'    => 'Business World',
      'content'  => 'Business World is an innovative WordPress theme great for Business websites.',
      'href'     => 'https://web-dorado.com/wordpress-themes/business-world.html',
      "demo"     => "http://themedemo.web-dorado.com/theme-businessworld/"
    ),
    "best_magazine" => array(
      'title'    => 'Best Magazine',
      'content'  => 'Best Magazine is an ultimate selection when you are dealing with multi-category news websites.',
      'href'     => 'https://web-dorado.com/wordpress-themes/best-magazine.html',
      "demo"     => "http://themedemo.web-dorado.com/theme-bestmagazine/"
    ),
    "magazine" => array(
      'title'    => 'News Magazine',
      'content'  => 'Magazine theme is a perfect solution when creating news and informational websites. It comes with a wide range of layout options.',
      'href'     => 'https://web-dorado.com/wordpress-themes/news-magazine.html',
      "demo"     => "http://themedemo.web-dorado.com/theme-newsmagazine/"
    ),
    
  );
  ?>
  <div id="main_featured_themes_page">
   <div class="page_header">
    <h3>Featured Themes</h3>
   </div>
    <div class="featured_header">
      <a href="https://web-dorado.com/wordpress-themes.html?source=<?php echo $current_plugin; ?>" target="_blank">
        <h1>WORDPRESS THEMES</h1>
        <h2 class="get_plugins">ALL FOR $40 ONLY <span>- SAVE 80%</span></h2>
        <div class="try-now">
					<span>TRY NOW</span>
				</div>
      </a>
    </div>
    <ul id="featured-plugins-list">
      <?php
      foreach ($themes as $key => $themes) {
        ?>
      <li class="<?php echo $key; ?>">
				<div class="product"></div>
				<div class="title">
					<strong class="heading"><?php echo $themes['title']; ?></strong>
				</div>
				<div class="description">
					<p><?php echo $themes['content']; ?></p>
				</div>
        <div class="links">
          <a target="_blank" href="<?php echo $themes["demo"]; ?>?source=<?php echo $current_plugin; ?>" class="demo"> Demo </a>
				  <a target="_blank" href="<?php echo $themes['href']; ?>?source=<?php echo $current_plugin; ?>" class="download">Free Download</a>
        </div>
			</li>
        <?php
      }
      ?>
    </ul>
  </div>
  <?php
}