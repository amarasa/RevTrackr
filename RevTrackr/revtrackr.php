<?php

/**
 * Plugin Name: RevTrackr
 * Description: A plugin to updates from a Log Markdown file
 * Version: 1.1
 * Author: Angelo Marasa
 */

// Include Parsedown library
require_once(plugin_dir_path(__FILE__) . 'lib/Parsedown.php');

// Hook to add admin menu
add_action('admin_menu', 'revtrackr_admin_menu');

// Action function for the above hook
function revtrackr_admin_menu()
{
    add_menu_page('RevTrackr Updates', 'RevTrackr Updates', 'manage_options', 'revtrackr', 'revtrackr_admin_page', 'dashicons-update', 6);
}

// Hook to enqueue admin styles
add_action('admin_enqueue_scripts', 'revtrackr_enqueue_admin_styles');

// Action function to enqueue admin styles
function revtrackr_enqueue_admin_styles($hook)
{
    // Check if we are on the RevTrackr admin page
    if ('toplevel_page_revtrackr' === $hook) {
        // Enqueue the stylesheet
        wp_enqueue_style('revtrackr-admin-styles', plugin_dir_url(__FILE__) . '/src/revtrackr-admin.css');
    }
}

// Function to display the admin page
function revtrackr_admin_page()
{
    echo '<h1>RevTrackr Updates Log</h1>';
    $md_file_path = plugin_dir_path(__FILE__) . 'updates/Logs.md'; // Path to Logs.MD within the plugin directory

    if (file_exists($md_file_path)) {
        $md_content = file_get_contents($md_file_path);

        // Parse Markdown to HTML
        $Parsedown = new Parsedown();
        echo $Parsedown->text($md_content);
    } else {
        echo 'Updates log is not available.';
    }
}

add_action('wp_dashboard_setup', 'revtrackr_add_dashboard_widget');

function revtrackr_add_dashboard_widget()
{
    wp_add_dashboard_widget(
        'revtrackr_dashboard_widget',           // Widget slug
        'RevTrackr Updates',                    // Title
        'revtrackr_dashboard_widget_content'    // Display function
    );
}


// Hook to add dashboard widget
add_action('wp_dashboard_setup', 'revtrackr_add_dashboard_widget');


function revtrackr_dashboard_widget_content()
{
    $md_file_path = plugin_dir_path(__FILE__) . 'updates/Logs.md';

    if (file_exists($md_file_path)) {
        $md_content = file_get_contents($md_file_path);

        // Use regular expression to extract the first section between horizontal lines
        preg_match('/---\n(.*?)\n---/s', $md_content, $matches);

        if (isset($matches[1])) {
            // Parse Markdown to HTML
            $Parsedown = new Parsedown();
            echo $Parsedown->text($matches[1]);
        } else {
            echo 'No updates found in the specified format.';
        }
    } else {
        echo 'Updates log is not available.';
    }
}
