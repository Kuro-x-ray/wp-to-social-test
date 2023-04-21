<?php

/*
 * Plugin Name: Wp 2 Social
 * Plugin URI: https://jrpgfr.net
 * Description: Announces new WordPress posts on Discord, Facebook, Twitter.
 * Version: 0.1
 * Author: Mazzola Gino
 * Author URI: https://jrpgfr.net
 */


require_once(plugin_dir_path(__FILE__) . '/vendor/autoload.php');
require_once(plugin_dir_path(__FILE__) . '/config/PostToSocialNetworkConfig.php');

function post_to_social_network($new_status, $old_status, $post) {

    foreach (PostToSocialNetworkConfig::getInstance()->get('required_classes') as $path) {
        require_once(plugin_dir_path(__FILE__) . $path);
    }

    $fb = new SnFacebookConnection($post, $old_status, $new_status);
    $tw = new SnTwitterConnection($post, $old_status, $new_status);
    $dc = new SnDiscordConnection($post, $old_status, $new_status);

    if ($fb->canPost() && $tw->canPost() && $dc->canPost()) {
        $ptsn = new SnPostToSocialNetwork([$fb, $tw, $dc]);
        $ptsn->executePosts();
    }
}

add_action('transition_post_status', 'post_to_social_network', 10, 3);

// Ajouter des champs personnalisés à l'éditeur d'article de WordPress
function ajouter_champs_personnalises() {
    add_meta_box(
            'champs_personnalises',
            'Textes pour les réseaux sociaux',
            'afficher_champs_personnalises',
            'post',
            'normal',
            'high'
    );
}

add_action('add_meta_boxes', 'ajouter_champs_personnalises');

// Afficher les champs personnalisés dans l'éditeur d'article
function afficher_champs_personnalises($post) {
    // Ajouter un champ pour le texte Twitter
    echo '<label for="texte_twitter">Texte Twitter:</label><br>';
    echo '<textarea id="texte_twitter" name="texte_twitter" rows="4" cols="50">' . get_post_meta($post->ID, 'texte_twitter', true) . '</textarea><br>';

    // Ajouter un champ pour le texte Facebook
    echo '<label for="texte_facebook">Texte Facebook:</label><br>';
    echo '<textarea id="texte_facebook" name="texte_facebook" rows="4" cols="50">' . get_post_meta($post->ID, 'texte_facebook', true) . '</textarea><br>';

    // Ajouter un champ pour le texte Discord
    echo '<label for="texte_discord">Texte Discord:</label><br>';
    echo '<textarea id="texte_discord" name="texte_discord" rows="4" cols="50">' . get_post_meta($post->ID, 'texte_discord', true) . '</textarea><br>';
}

// Enregistrer les valeurs des champs personnalisés lors de la sauvegarde de l'article
function enregistrer_champs_personnalises($post_id) {
    // Vérifier si les champs ont été soumis
    if (isset($_POST['texte_twitter'])) {
        // Enregistrer la valeur du champ texte_twitter
        update_post_meta($post_id, 'texte_twitter', $_POST['texte_twitter']);
    }

    if (isset($_POST['texte_facebook'])) {
        // Enregistrer la valeur du champ texte_facebook
        update_post_meta($post_id, 'texte_facebook', $_POST['texte_facebook']);
    }

    if (isset($_POST['texte_discord'])) {
        // Enregistrer la valeur du champ texte_discord
        update_post_meta($post_id, 'texte_discord', $_POST['texte_discord']);
    }
}

add_action('save_post', 'enregistrer_champs_personnalises');

function hide_publish_button() {
    global $post;

    if (!empty(get_post_meta($post->ID, 'texte_twitter', true)) && !empty(get_post_meta($post->ID, 'texte_facebook', true)) && !empty(get_post_meta($post->ID, 'texte_discord', true))) {
        return;
    }
    ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script type="text/javascript">
function verifier_champs_personnalises_avant_publication() {
    // Récupérer l'ID de l'article en cours d'édition
    const post_id = document.querySelector('input[name="post_ID"]').value;

    // Vérifier si l'article est un article existant
    if (post_id) {
        // Récupérer les valeurs des champs personnalisés pour l'article
        const texte_twitter = document.getElementById('texte_twitter').value;
        const texte_facebook = document.getElementById('texte_facebook').value;
        const texte_discord = document.getElementById('texte_discord').value;

        // Vérifier si les champs sont vides
        if (!texte_twitter || !texte_facebook || !texte_discord) {
            // Désactiver le bouton Publier
            document.getElementById('publish').setAttribute('disabled', true);

            // Ajouter un message d'erreur
            const message = document.createElement('div');
            message.classList.add('error', 'below-h2');
            message.innerHTML = '<p>Les champs personnalisés texte_twitter, texte_facebook et texte_discord sont obligatoires.</p>';
            document.getElementById('titlediv').after(message);

            // Vérifier les champs avant de publier
            document.getElementById('publish').addEventListener('click', function(event) {
                if (!texte_twitter || !texte_facebook || !texte_discord) {
                    event.preventDefault();
                    message.style.display = 'block';
                    return false;
                }
            });

            // Cacher le message d'erreur si les champs sont remplis
            document.querySelectorAll('#texte_twitter, #texte_facebook, #texte_discord').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (texte_twitter && texte_facebook && texte_discord) {
                        message.style.display = 'none';
                        document.getElementById('publish').removeAttribute('disabled');
                    }
                });
            });
        }
    }
}
        
        $(document).ready(() => {
            verifier_champs_personnalises_avant_publication();
        });
        

    </script>
    <?php

}

add_action('admin_head', 'hide_publish_button');

function wp2social_configuration_page() {
    ?>
    <div class="wrap">
        <h1>WP2Social Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields( 'configuration_group' );
                do_settings_sections( 'wp2social_options_page' );
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

function wp2social_field_tw_account_id() {
    $tw_account_id = get_option( 'tw_account_id' );
    ?>
    <input type="text" name="tw_account_id" style="width: 100%" value="<?php echo esc_attr( $tw_account_id ); ?>">
    <?php
}

function wp2social_field_tw_consumer_key() {
    $tw_consumer_key = get_option( 'tw_consumer_key' );
    ?>
    <input type="text" name="tw_consumer_key" style="width: 100%" value="<?php echo esc_attr( $tw_consumer_key ); ?>">
    <?php
}

function wp2social_field_tw_consumer_secret() {
    $tw_consumer_secret = get_option( 'tw_consumer_secret' );
    ?>
    <input type="text" name="tw_consumer_secret" style="width: 100%" value="<?php echo esc_attr( $tw_consumer_secret ); ?>">
    <?php
}

function wp2social_field_tw_bearer_token() {
    $tw_bearer_token = get_option( 'tw_bearer_token' );
    ?>
    <input type="text" name="tw_bearer_token" style="width: 100%" value="<?php echo esc_attr( $tw_bearer_token ); ?>">
    <?php
}

function wp2social_field_tw_access_token() {
    $tw_access_token = get_option( 'tw_access_token' );
    ?>
    <input type="text" name="tw_access_token" style="width: 100%" value="<?php echo esc_attr( $tw_access_token ); ?>">
    <?php
}

function wp2social_field_tw_access_token_secret() {
    $tw_access_token_secret = get_option( 'tw_access_token_secret' );
    ?>
    <input type="text" name="tw_access_token_secret" style="width: 100%" value="<?php echo esc_attr( $tw_access_token_secret ); ?>">
    <?php
}

function wp2social_field_fb_access_token() {
    $fb_access_token = get_option( 'fb_access_token' );
    ?>
    <input type="text" name="fb_access_token" style="width: 100%" value="<?php echo esc_attr( $fb_access_token ); ?>">
    <?php
}

function wp2social_field_dc_webhook_url() {
    $dc_webhook_url = get_option( 'dc_webhook_url' );
    ?>
    <input type="text" name="dc_webhook_url" style="width: 100%" value="<?php echo esc_attr( $dc_webhook_url ); ?>">
    <?php
}


function wp2social_settings() {
    register_setting( 'configuration_group', 'tw_account_id' );
    register_setting( 'configuration_group', 'tw_consumer_key' );
    register_setting( 'configuration_group', 'tw_consumer_secret' );
    register_setting( 'configuration_group', 'tw_bearer_token' );
    register_setting( 'configuration_group', 'tw_access_token' );
    register_setting( 'configuration_group', 'tw_access_token_secret' );
    register_setting( 'configuration_group', 'fb_access_token' );
    register_setting( 'configuration_group', 'dc_webhook_url' );

    add_settings_section( 'wp2social_section_twitter', 'Twitter Settings', '', 'wp2social_options_page' );
    add_settings_field( 'wp2social_field_tw_account_id', 'Twitter Account ID', 'wp2social_field_tw_account_id', 'wp2social_options_page', 'wp2social_section_twitter' );
    add_settings_field( 'wp2social_field_tw_consumer_key', 'Twitter Consumer Key', 'wp2social_field_tw_consumer_key', 'wp2social_options_page', 'wp2social_section_twitter' );
    add_settings_field( 'wp2social_field_tw_consumer_secret', 'Twitter Consumer Secret', 'wp2social_field_tw_consumer_secret', 'wp2social_options_page', 'wp2social_section_twitter' );
    add_settings_field( 'wp2social_field_tw_bearer_token', 'Twitter Bearer Token', 'wp2social_field_tw_bearer_token', 'wp2social_options_page', 'wp2social_section_twitter' );
    add_settings_field( 'wp2social_field_tw_access_token', 'Twitter Access Token', 'wp2social_field_tw_access_token', 'wp2social_options_page', 'wp2social_section_twitter' );
    add_settings_field( 'wp2social_field_tw_access_token_secret', 'Twitter Access Token Secret', 'wp2social_field_tw_access_token_secret', 'wp2social_options_page', 'wp2social_section_twitter' );

    add_settings_section( 'wp2social_section_facebook', 'Facebook Settings', '', 'wp2social_options_page' );
    add_settings_field( 'wp2social_field_fb_access_token', 'Facebook Access Token', 'wp2social_field_fb_access_token', 'wp2social_options_page', 'wp2social_section_facebook' );

    add_settings_section( 'wp2social_section_discord', 'Discord Settings', '', 'wp2social_options_page' );
    add_settings_field( 'wp2social_field_dc_webhook_url', 'Discord Webhook URL', 'wp2social_field_dc_webhook_url', 'wp2social_options_page', 'wp2social_section_discord' );
}


add_action( 'admin_init', 'wp2social_settings' );
add_action( 'admin_menu', 'wp2social_add_admin_menu' );

function wp2social_add_admin_menu() {
    add_menu_page( 'WP2Social', 'WP2Social', 'manage_options', 'wp2social', 'wp2social_configuration_page' );
}
