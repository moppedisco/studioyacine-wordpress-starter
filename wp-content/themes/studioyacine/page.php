<?php get_header(); ?>

<main id="main" class="" role="main" itemscope itemprop="mainContentOfPage">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

			<header class="article-header">

				<h1 class="page-title"><?php the_title(); ?></h1>

			</header>

			<section class="entry-content">

				<?php the_content(); ?>

			</section> 

			<footer class="article-footer">

			</footer>

		</article>

	<?php endwhile; endif; ?>

</main>


<?php get_footer(); ?>