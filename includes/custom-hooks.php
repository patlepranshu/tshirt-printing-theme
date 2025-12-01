<?php
/**
 * Custom WordPress Hooks
 */

// Disable block editor for posts
add_filter('use_block_editor_for_post', '__return_false', 10);

// Disable block editor for all post types
add_filter('use_block_editor_for_post_type', '__return_false', 10);
