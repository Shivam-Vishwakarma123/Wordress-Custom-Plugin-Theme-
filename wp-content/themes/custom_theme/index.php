<?php get_header(); ?>

<main>
    <section>
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
        ?>
                <article>
                    <h2> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_content(); ?>
                </article>
        <?php endwhile;
        else :
            echo '<p>No content found</p>';
        endif;
        ?>
    </section>

    <aside>
        <?php
        //  dynamic_sidebar('sidebar-1'); 
        ?>
    </aside>
</main>

<?php get_footer(); ?>