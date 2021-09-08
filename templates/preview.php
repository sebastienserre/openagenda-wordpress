<?php
/**
 * Template part for displaying the preview widget
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @var     $atts       Shortcode attributes
 * @package Openagenda
 */
?>
<div class="oa-preview-event oa-preview-card">
    <div class="oa-card">
        <a class="oa-card-image-link" href="<?php openagenda_event_permalink(); ?>">
            <?php openagenda_event_image( 'image' ); ?>
        </a>
        <div class="oa-card-body">
            <a class="oa-card-link" href="<?php openagenda_event_permalink() ?>">
                <strong><?php openagenda_field( 'title' ); ?></strong>
            </a>
            <ul class="oa-card-metas oa-list-unstyled">
                <li class="oa-card-meta">
                    <?php openagenda_icon( 'month' ); ?>
                    <span class="oa-card-range"><?php openagenda_field( 'range' ); ?></span>
                </li>
                <li class="oa-card-meta">
                    <?php openagenda_icon( 'location' ); ?>
                    <span class="oa-card-location"><?php openagenda_field( 'location.name' ); ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>
