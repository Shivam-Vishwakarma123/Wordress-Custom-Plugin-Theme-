<h1>Ullu ho ka</h1>
<?php
get_header();

while (have_posts()) :
    the_post();
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header>
            <h1><?php the_title(); ?></h1>
        </header>

        <div class="entry-content">
            <?php the_content(); ?>
        </div>

        <?php if (comments_open() || get_comments_number()) :
            comments_template();
        endif; ?>
    </article>

<?php endwhile;

get_footer();
?>
