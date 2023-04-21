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
