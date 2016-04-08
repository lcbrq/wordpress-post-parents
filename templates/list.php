<?php if ($posts->have_posts()): ?>
    <ul>
    <?php
    while ($posts->have_posts()):
        $posts->the_post();
        ?>
            <li>
                <a href="<?php echo get_permalink(); ?>">
                    <?php echo get_the_title(); ?>
                </a>
            </li>
        <?php endwhile ?>
    </ul>
<?php endif; ?>