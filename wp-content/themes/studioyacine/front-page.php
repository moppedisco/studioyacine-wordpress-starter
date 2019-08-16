<?php get_header(); ?>

<main id="main" class="" role="main" itemscope itemprop="mainContentOfPage">

	<header class="article-header">

		<h1 class="page-title"><?php the_title(); ?></h1>

	</header>

	<section>

		<? // EXAMPLE USAGE ?>
		<?php get_template_part('templates/home', 'hero'); ?>

	</section>

</main>

<?php get_footer(); ?>