<?php
/**
 * Recupera y crea el fichero wp-config.php.
 *
 * Los permisos del directoriio base deben permitir la escritura de archvios para que
 * se pueda crear el wp-config.php usando esta página.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Estamos instalando.
 *
 * @package WordPress
 */
define('WP_INSTALLING', true);

/**
 * Esto es totalmente automático.
 */
define('WP_SETUP_CONFIG', true);

/**
 * Inhabilitar el informe de errores
 *
 * Establece esto a error_reporting( E_ALL ) o error_reporting( E_ALL | E_STRICT ) para hacer debug
 */
error_reporting(0);

/**#@+
 * Estos tres defines se requieren para permitirnos usar require_wp_db() para que cargue
 * la clase de la base de datos mientras haya un wp-content/db.php.
 * @ignore
 */
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
define('WP_DEBUG', false);
/**#@-*/

require_once(ABSPATH . WPINC . '/load.php');
require_once(ABSPATH . WPINC . '/version.php');
wp_check_php_mysql_versions();

require_once(ABSPATH . WPINC . '/compat.php');
require_once(ABSPATH . WPINC . '/functions.php');
require_once(ABSPATH . WPINC . '/class-wp-error.php');

if (!file_exists(ABSPATH . 'wp-config-sample.php'))
	wp_die('Lo siento, necesito un fichero wp-config-sample.php desde el que trabajar. Por favor, vuelve a subir este archivo desde tu instalación de WordPress.');

$configFile = file(ABSPATH . 'wp-config-sample.php');

// Comprobamos si se ha creado el wp-config.php
if (file_exists(ABSPATH . 'wp-config.php'))
	wp_die("<p>El archivo 'wp-config.php' ya existe. Si necesitas reiniciar alguno de los elementos de la configuración de este archivo bórralo primero. Puedes tratar de <a href='install.php'>instalar ahora</a>.</p>");

// Comprobamos si existe un wp-config.php por encima del directorio raiz pero que no sea parte de otra instalación
if (file_exists(ABSPATH . '../wp-config.php') && ! file_exists(ABSPATH . '../wp-settings.php'))
	wp_die("<p>El archivo 'wp-config.php' ya existe un nivel por encima de tu instalación de WordPress. Si necesitas reiniciar alguno de los elementos de la configuración de este archivo bórralo primero. Puedes tratar de <a href='install.php'>instalar ahora</a>.</p>");

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Muestra la cabecera de configuración del fichero wp-config.php.
 *
 * @ignore
 * @since 2.3.0
 * @package WordPress
 * @subpackage Installer_WP_Config
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Archivo de configuración de WordPress</title>
<link rel="stylesheet" href="css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>Bienvenid@ a WordPress. Antes de empezar necesitamos algo de información de la base de datos. Necesitas conocer la siguiente información antes de seguir.</p>
<ol>
	<li>Nombre de la base de datos</li>
	<li>Nombre de usuario de la base de datos</li>
	<li>Contraseña de la base de datos</li>
	<li>Host de la base de datos (en el 99% de los casos, <em>localhost</em>)</li>
	<li>Prefijo de tabla (si quieres ejecutar más de un WordPress en una sola base de datos)</li>
</ol>
<p><strong>Si por alguna razón no funciona la creación automática de este archivo no te preocupes. Todo lo que hace es rellenar un fichero de configuración con la información de la base de datos. También puedes simplemente abrir el fichero <code>wp-config-sample.php</code> en un editor de texto, rellenar la información y guardarlo como <code>wp-config.php</code>. </strong></p>
<p>En la mayoría de las ocasiones esta información te la facilita tu proveedor de alojamiento. Si no tienes esta información tendrás que contactar con ellos antes de poder continuar. Si ya estás listo &hellip;</p>

<p class="step"><a href="setup-config.php?step=1<?php if ( isset( $_GET['noapi'] ) ) echo '&amp;noapi'; ?>" class="button">¡Vamos a ello!</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>A continuación deberás introducir los detalles de conexión con tu base de datos. Si no estás seguro de cuáles son contacta con tu proveedor de alojamiento. </p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname">Nombre de la base de datos</label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" value="wordpress" /></td>
			<td>El nombre de la base de datos en la que quieres que se ejecute WP. </td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">Nombre de usuario</label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="username" /></td>
			<td>Tu nombre de usuario de MySQL</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Contraseña</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="password" /></td>
			<td>…y la contraseña de MySQL.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">Host de la base de datos</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>Si no funciona <code>localhost</code> tendrás que contactar con tu proveedor de alojamiento para que te diga cual es.</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">Prefijo de tabla</label></th>
			<td><input name="prefix" id="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>Si quieres ejecutar varias instalaciones de WordPress en una sola base de datos cambia esto.</td>
		</tr>
	</table>
	<?php if ( isset( $_GET['noapi'] ) ) { ?><input name="noapi" type="hidden" value="true" /><?php } ?>
	<p class="step"><input name="submit" type="submit" value="Enviar" class="button" /></p>
</form>
<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if ( empty($prefix) )
		$prefix = 'wp_';

	// Validación del $prefix: solo puede contener letras, números y guiones bajos
	if ( preg_match( '|[^a-z0-9_]|i', $prefix ) )
		wp_die( /*WP_I18N_BAD_PREFIX*/'<strong>ERROR</strong>: "Prefijo de tabla" solo puede contener números, letras y guión bajo.'/*/WP_I18N_BAD_PREFIX*/ );

	// Probamos la conexión con la base de datos.
	/**#@+
	 * @ignore
	 */
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);
	/**#@-*/

	// Fallará si los valores son incorrectos.
	require_wp_db();
	if ( ! empty( $wpdb->error ) ) {
		$back = '<p class="step"><a href="setup-config.php?step=1" onclick="javascript:history.go(-1);return false;" class="button">Inténtalo de nuevo</a></p>';
		wp_die( $wpdb->error->get_error_message() . $back );
	}

	// Carga o generación de las claves y salts.
	$no_api = isset( $_POST['noapi'] );
	require_once( ABSPATH . WPINC . '/plugin.php' );
	require_once( ABSPATH . WPINC . '/l10n.php' );
	require_once( ABSPATH . WPINC . '/pomo/translations.php' );
	if ( ! $no_api ) {
		require_once( ABSPATH . WPINC . '/class-http.php' );
		require_once( ABSPATH . WPINC . '/http.php' );
		wp_fix_server_vars();
		/**#@+
		 * @ignore
		 */
		function get_bloginfo() {
			return ( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . str_replace( $_SERVER['PHP_SELF'], '/wp-admin/setup-config.php', '' ) );
		}
		/**#@-*/
		$secret_keys = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );
	}

	if ( $no_api || is_wp_error( $secret_keys ) ) {
		$secret_keys = array();
		require_once( ABSPATH . WPINC . '/pluggable.php' );
		for ( $i = 0; $i < 8; $i++ ) {
			$secret_keys[] = wp_generate_password( 64, true, true );
		}
	} else {
		$secret_keys = explode( "\n", wp_remote_retrieve_body( $secret_keys ) );
		foreach ( $secret_keys as $k => $v ) {
			$secret_keys[$k] = substr( $v, 28, 64 );
		}
	}
	$key = 0;

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				$configFile[$line_num] = str_replace("nombredetubasededatos", $dbname, $line);
				break;
			case "define('DB_USER'":
				$configFile[$line_num] = str_replace("'nombredeusuario'", "'$uname'", $line);
				break;
			case "define('DB_PASSW":
				$configFile[$line_num] = str_replace("'contraseña'", "'$passwrd'", $line);
				break;
			case "define('DB_HOST'":
				$configFile[$line_num] = str_replace("localhost", $dbhost, $line);
				break;
			case '$table_prefix  =':
				$configFile[$line_num] = str_replace('wp_', $prefix, $line);
				break;
			case "define('AUTH_KEY":
			case "define('SECURE_A":
			case "define('LOGGED_I":
			case "define('NONCE_KE":
			case "define('AUTH_SAL":
			case "define('SECURE_A":
			case "define('LOGGED_I":
			case "define('NONCE_SA":
				$configFile[$line_num] = str_replace('pon aquí tu frase aleatoria', $secret_keys[$key++], $line );
				break;
		}
	}
	if ( ! is_writable(ABSPATH) ) :
		display_header();
?>
<p>Lo siento, pero no se ha podido escribir en el fichero <code>wp-config.php</code>.</p>
<p>Puedes crear mahualmente el archivo <code>wp-config.php</code> y pegar dentro el siguiente texto.</p>
<textarea cols="98" rows="15" class="code"><?php
		foreach( $configFile as $line ) {
			echo htmlentities($line, ENT_COMPAT, 'UTF-8');
		}
?></textarea>
<p>Una vez hayas hecho esto haz clic en "Iniciar la instalación."</p>
<p class="step"><a href="install.php" class="button">Iniciar la instalación</a></p>
<?php
	else :
		$handle = fopen(ABSPATH . 'wp-config.php', 'w');
		foreach( $configFile as $line ) {
			fwrite($handle, $line);
		}
		fclose($handle);
		chmod(ABSPATH . 'wp-config.php', 0666);
		display_header();
?>
<p>¡Todo correcto! Ya has terminado esta parte de la instalación. Ahora WordPress puede comunicarse con tu base de datos. Si estás preparado es momento de &hellip;</p>

<p class="step"><a href="install.php" class="button">Iniciar la instalación</a></p>
<?php
	endif;
	break;
}
?>
</body>
</html>
