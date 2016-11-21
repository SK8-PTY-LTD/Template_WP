<?php
function fmc_featured_themes_page() { 

	$slug = 'contact-form-maker';
	$image_url = WD_FMC_URL . "/featured/images/";
	$demo_url = 'http://themedemo.web-dorado.com/';
	$site_url = 'https://web-dorado.com/wordpress-themes/';


	$WDWThemes = array(
			"business_elite" => array(
				"title" => "Business Elite",
				"description" =>"Business Elite is a robust parallax theme for business websites. The theme uses smooth transitions and many functional sections.",
				"link" => "business-elite.html",
				"demo" => "theme-businesselite",
				"image" => "business_elite.jpg"
			),
			"portfolio" => array(
				"title" => "Portfolio Gallery",
				"description" =>"Portfolio Gallery helps to display images using various color schemes and layouts combined with elegant fonts and content parts.",
				"link" => "portfolio-gallery.html",
				"demo" => "theme-portfoliogallery",
				"image" => "portfolio_gallery.jpg"
			),
			"sauron" => array(
				"title" => "Sauron",
				"description" =>"Sauron is a multipurpose parallax theme, which uses multiple interactive sections designed for the client-engagement.",
				"link" => "sauron.html",
				"demo" => "theme-sauron",
				"image" => "sauron.jpg"
			),
			"business_world" => array(
				"title" => "Business World",
				"description" => "Business World is an innovative WordPress theme great for Business websites.",
				"link" => "business-world.html",
				"demo" => "theme-businessworld",
				"image" => "business_world.jpg"
			),
			"best_magazine" => array(
				"title" => "Best Magazine",
				"description" =>"Best Magazine is an ultimate selection when you are dealing with multi-category news websites.",
				"link" => "best-magazine.html",
				"demo" => "theme-bestmagazine",
				"image" => "best_magazine.jpg"
			),
			"magazine" => array(
				"title" => "News Magazine",
				"description" =>"Magazine theme is a perfect solution when creating news and informational websites. It comes with a wide range of layout options.",
				"link" => "news-magazine.html",
				"demo" => "theme-newsmagazine",
				"image" => "news_magazine.jpg"
			)
		);
	?>
	
	<style>
	
	#main_featured_themes_page #featured-themes-list li a.download {
			padding-right: 30px;
			background:url(<?php echo $image_url; ?>down.png) no-repeat right;
	}
	
	</style>
	
	
	<div id="main_featured_themes_page">
    <div class="featured_container">
				<div class="page_header">
					<h1><?php echo "Featured Themes"; ?></h1>
				</div>
        <div class="featured_header">
            <a target="_blank" href="https://web-dorado.com/wordpress-themes.html?source=<?php echo $slug; ?>">
                <h1><?php echo "WORDPRESS THEMES"; ?></h1>
                <h2 class="get_themes"><?php echo "ALL FOR $40 ONLY "; ?><span>- <?php echo "SAVE 80%"; ?></span></h2>
								<div class="try-now">
									<span><?php echo "TRY NOW"; ?></span>
								</div>
            </a>
        </div>
				<ul id="featured-themes-list">
				<?php foreach($WDWThemes as $key=>$WDWTheme) : ?>
						<li class="<?php echo $key; ?>">
								<div class="theme_img">
									<img src="<?php echo $image_url . $WDWTheme["image"]; ?>">
								</div>
								<div class="title">
										<h3 class="heading"><?php echo $WDWTheme["title"]; ?></h3>
								</div>
								<div class="description">
										<p><?php echo $WDWTheme["description"]; ?></p>
								</div>
								<div class="links">
								<a target="_blank" href="<?php echo $demo_url . $WDWTheme["demo"]."?source=".$slug; ?>" class="demo"><?php echo "Demo"; ?></a>
								<a target="_blank" href="<?php echo $site_url . $WDWTheme["link"]."?source=".$slug; ?>" class="download"><?php echo "Free Download"; ?></a>
								</div>
						</li>		
				<?php endforeach; ?>
				</ul>
		</div>
	</div>


<?php }
?>
