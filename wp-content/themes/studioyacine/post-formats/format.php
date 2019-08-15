<?php
/*
* This is the default post format.
*
* So basically this is a regular post. if you don't want to use post formats,
* you can just copy ths stuff in here and replace the post format thing in
* single.php.
*
* The other formats are SUPER basic so you can style them as you like.
*
* Again, If you want to remove post formats, just delete the post-formats
* folder and replace the function below with the contents of the "format.php" file.
*/
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

  <header class="article-header entry-header">

    <h1 class="entry-title single-title" itemprop="headline" rel="bookmark"><?php the_title(); ?></h1>

  </header>

  <section class="entry-content " itemprop="articleBody">
    
    <?php the_content(); ?>
  
  </section> 

  <footer class="article-footer">

    <?php printf(__('filed under', 'bonestheme') . ': %1$s', get_the_category_list(', ')); ?>

    <?php the_tags('<p class="tags"><span class="tags-title">' . __('Tags:', 'bonestheme') . '</span> ', ', ', '</p>'); ?>

  </footer> 

</article>