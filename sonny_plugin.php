<?php
/*
Plugin Name: Sonny Plugin
*/

global $sonny_slideshow_version;
$sonny_slideshow_version = '0.2';

function sonny_slideshow_activate() {
	global $wpdb;
	global $sonny_slideshow_version;

	$table_name      = $wpdb->prefix . 'sonny_slideshow';
	$charset_collate = $wpdb->get_charset_collate();
	$installed_ver   = get_option( 'sonny_slideshow_version' );

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		slug varchar(255) NOT NULL,
		published tinyint(1) DEFAULT '0' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );

	if ( empty ( $installed_ver ) ) {
		add_option( 'sonny_slideshow_version', $sonny_slideshow_version );
	}
	else if ( $installed_ver != $sonny_slideshow_version ) {
		update_option( 'sonny_slideshow_version', $sonny_slideshow_version );
	}
}

// Triggered once when the plugin is activated
register_activation_hook( __FILE__, 'sonny_slideshow_activate' );

function get_rows_data() {
	global $wpdb;
	$query = 'SELECT * FROM ' . $wpdb->prefix . 'sonny_slideshow';
	$rows = $wpdb->get_results( $query );

	return $rows;
}

function sonny_slideshow_options() {
	$rows_data = get_rows_data();
	?>
	<div class="wrap">
		<h2>Sonny Slideshow
			<a href="" class="add-new-h2" onclick="">Add new</a>
		</h2>
		<p>Welcome</p>
		<form action="" method="post" id="sonny-form">

			<table class="wp-list-table widefat fixed pages">
				<thead>
					<th class="table_small_col">
						<span>ID</span><span class="sorting-indicator"></span>
					</th>
					<th class="table_big_col">Slider Name</th>
					<th class="table_big_col">Slides</th>
					<th class="table_big_col">Shortcode</th>
					<th class="table_large_col">PHP function</th>
					<th class="table_big_col">Published</th>
					<th class="table_big_col">Edit</th>
					<th class="table_big_col">Delete</th>
				</thead>
				<tbody id="tbody_arr">
					<?php
					if ( $rows_data ) {
						foreach ( $rows_data as $row_data ) {
							$alternate = ( ! isset ( $alternate ) || $alternate == 'class="alternate"' ) ? '' : 'class="alternate"';
							?>
							<tr id = "tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
								<td class="table_small_col"><?php echo $row_data->id; ?></td>
								<td class="table_big_col">
									<?php echo $row_data->name; ?>
								</td>
								<td class="table_big_col">empty</td>
								<td class="table_big_col" style="padding-left: 0; padding-right: 0;">
									<input type="text" value='[sonny_ss id="<?php echo $row_data->id; ?>"]' onclick="" size="11" readonly="readonly" style="padding-left: 1px; padding-right: 1px;">
								</td>
								<td class="table_large_col" style="padding-left: 0; padding-right: 0;">
									<input type="text" value="&#60;?php sonny_ss_slider(<?php echo $row_data->id; ?>); ?&#62;" onclick="" size="23" readonly="readonly" style="padding-left: 1px; padding-right: 1px;">
								</td>
								<td class="table_big_col">
									<?php echo ( $row_data->published === '1' ) ? 'true' : 'false'; ?>
								</td>
								<td class="table_big_col"><a onclick="" href="">Edit</a></td>
								<td class="table_big_col"><a onclick="" href="">Delete</a></td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
		</form>
	</div>
<?php
}

function sonny_slideshow_settings() {
	?>
	<div class="wrap">
		<h2>Sonny Slideshow Settings</h2>
	</div>
	<?php
}

function sonny_slideshow_options_menu() {
	add_menu_page( 'Sonny Slideshow', 'Sonny Slideshow', 'manage_options', 'sonny-slideshow', 'sonny_slideshow_options' );
	add_submenu_page( 'sonny-slideshow', 'Slideshow Settings', 'Slideshow Settings', 'manage_options', 'sonny-slideshow-settings', 'sonny_slideshow_settings' );
}

// Create the menu item in WP admin
add_action( 'admin_menu', 'sonny_slideshow_options_menu' );
