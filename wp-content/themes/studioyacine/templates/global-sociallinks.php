<?php
// ===================================================
// SOCIAL LINKS
// ===================================================
// ===================================================
// ===================================================
?>
<?php if (have_rows('socialmedia','option')) : ?>
    <ul class="SocialLinks">
        <?php while (have_rows('socialmedia','option')) : the_row();?>
            <?php 
                $link = get_sub_field('link');
                $link_target = $link['target'] ? $link['target'] : '_self';
            ?>
            <li>
                <a target="<?php echo esc_attr( $link_target ); ?>" title='<?php echo $link['title']; ?>' href="<?php echo $link['url']; ?>">
                    <?php $linkUrl = $link['url']; ?>

                    <?php if(stristr($linkUrl,'twitter')):?>
                        <svg class="icon">
                            <use xlink:href="#icon-twitter"></use>
                        </svg>  
                    <?php elseif(stristr($linkUrl,'facebook')):?>
                        <svg class="icon">
                            <use xlink:href="#icon-facebook"></use>
                        </svg> 
                    <?php elseif (stristr($linkUrl,'instagram')):?>
                        <svg class="icon">
                            <use xlink:href="#icon-instagram"></use>
                        </svg>
                   <?php elseif (stristr($linkUrl,'youtube')):?>
                        <svg class="icon">
                            <use xlink:href="#icon-youtube"></use>
                        </svg>                        
                    <?php endif; ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>