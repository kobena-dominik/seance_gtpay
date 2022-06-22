<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'gtbank' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'admin' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'admin' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'f5d-J/kif=cvDbD?a% :,5X(A.<l>3SLUrP!-vK@@0*|^Gg^xenJ@x1G2%BC-B/{' );
define( 'SECURE_AUTH_KEY',  'k#u-1]{@+>mExX#,TP<^{-8EF &87D4 gv#r%<rkTp[T!?+S{NNG]-fG=-aw~r@s' );
define( 'LOGGED_IN_KEY',    ';$03qL0X{_Y@`2*alkvHHi7(?ag}]j;-uD` Pj$u%SXu<hmwz*bb[gB=N|vZ! 7v' );
define( 'NONCE_KEY',        'zO>n1,}g$&=o,H?*mxED*drpV?_1SP 0ig6orp^c4V9BO_+jRhJ}mwb%gy?G9,&4' );
define( 'AUTH_SALT',        '}mfX{yW2C=I39fW<N=&3l^Mx<3b-5x|8/V5Jc_gA4m^gm?#uX5<vyt#<g*?j0n2?' );
define( 'SECURE_AUTH_SALT', '6o@~Rw$]F+>aU.F|7hyc_(51T,R<6lAe@`!la!>O#.C)HQ zfgB2F>=DDy{c$&<P' );
define( 'LOGGED_IN_SALT',   ']8A4=(s[ylRl5o5kj<iH?%9s;UN-]p(zk~M2k:,wqYDZS6lHCz#sf<L$^&2)Pc ^' );
define( 'NONCE_SALT',       'v@ps{{IO8z1CPcQxFLulS5(QK{,Z)TFM8JTk9#iRNvwE`neF:dQ>b<JAkH,S}q-J' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
