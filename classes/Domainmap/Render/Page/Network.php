<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// | Based on an original by Donncha (http://ocaoimh.ie/)                 |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * Network admin page render class.
 *
 * @category Domainmap
 * @package Render
 * @subpackage Page
 *
 * @since 4.0.0
 */
class Domainmap_Render_Page_Network extends Domainmap_Render {

	/**
	 * Returns array of mapping variations.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 * @return array The array of mapping variations.
	 */
	private function _get_mapping_options() {
		return array(
			'user'     => __( 'domain entered by the user', 'domainmap' ),
			'mapped'   => __( 'mapped domain', 'domainmap' ),
			'original' => __( 'original domain', 'domainmap' ),
		);
	}

	/**
	 * Builds and renders messages array.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 */
	private function _get_messages() {
		$messages = array();
		$messages[1] = __( 'Options updated.','domainmap' );
		if ( isset( $_GET['msg'] ) ) {
			echo '<div id="message" class="updated fade">' . $messages[(int)$_GET['msg']] . '</div>';
			$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'message' ), $_SERVER['REQUEST_URI'] );
		}
	}

	/**
	 * Renders site admin page.
	 *
	 * @since 4.0.0
	 *
	 * @access protected
	 */
	protected function _to_html() {
		$tabs = array(
			'#domainmapping-general-options' => __( 'Mapping options', 'domainmap' ),
			'#domainmapping-reseller-options' => __( 'Reseller options', 'domainmap' ),
		);

		$active_href = '';
		$active_tab = filter_input( INPUT_COOKIE, 'domainmapping-actab', FILTER_DEFAULT, array(
			'options' => array(
				'default' => md5( key( $tabs ) ),
			),
		) );

		?><div id="domainmapping-content" class="wrap">
			<?php screen_icon( 'ms-admin' ) ?>
			<h2><?php _e( 'Domain Mapping', 'domainmap' ) ?></h2>

			<form action="<?php echo add_query_arg( 'noheader', 'true' ) ?>" method="post">
				<?php wp_nonce_field( 'update-dmoptions' ) ?>
				<?php $this->_get_messages() ?>
				<div class="domainmapping-tab-switch domainmapping-tab-switch-js">
					<ul>
						<?php foreach ( $tabs as $href => $label ) : ?>
						<li>
							<?php if ( $active_tab == md5( $href ) ) : ?>
								<?php $active_href = $href ?>
								<a class="active" href="<?php echo $href ?>"><?php echo $label ?></a>
							<?php else : ?>
								<a href="<?php echo $href ?>"><?php echo $label ?></a>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
					<div class="domainmapping-clear"></div>
				</div>

				<input type="hidden" id="domainmapping-active-tab" name="active_tab" value="<?php echo $active_href ?>">

				<div class="domainmapping-tabs">
					<div id="domainmapping-general-options" class="domainmapping-tab<?php echo $active_href == '#domainmapping-general-options' ? ' active' : '' ?>"><?php
						$this->_render_mapping_options_tab()
					?></div>

					<div id="domainmapping-reseller-options" class="domainmapping-tab<?php echo $active_href == '#domainmapping-reseller-options' ? ' active' : '' ?>"><?php
						$this->_render_reseller_options_tab()
					?></div>
				</div>

				<p class="submit">
					<button type="submit" class="domainmapping-form-submit"><i class="icon-save"></i> <?php _e( 'Save Changes', 'domainmap' ) ?></button>
				</p>
			</form>
		</div><?php
	}

	/**
	 * Renders mapping options tab.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 */
	private function _render_mapping_options_tab() {
		$msg = array();
		if ( !file_exists( WP_CONTENT_DIR . '/sunrise.php' ) ) {
			$msg[] = "<p><strong>" . __( "Please copy the sunrise.php to ", 'domainmap' ) . ABSPATH . __( "wp-content/sunrise.php and uncomment the SUNRISE setting in the ", 'domainmap' ) . ABSPATH . __( "wp-config.php file", 'domainmap' ) . "</strong></p>";
		}

		if ( !defined( 'SUNRISE' ) ) {
			$msg[] = "<p><strong>" . __( "If you've followed the instructions and not already added define( 'SUNRISE', 'on' ); then please do so. If you added the constant be sure to uncomment this line: //define( 'SUNRISE', 'on' ); in the wp-config.php file.", 'domainmap' ) . "</strong></p>";
		}

		if ( defined( 'DOMAIN_CURRENT_SITE' ) ) {
			$str = "<p><strong>" . __( "If you are having problems with domain mapping you should try removing the following lines from your wp-config.php file:.", 'domainmap' ) . "</strong></p>";
			$str .= "<ul>";
			$str .= "<li>" . "define( 'DOMAIN_CURRENT_SITE', '" . DOMAIN_CURRENT_SITE . "' );" . "</li>";
			$str .= "<li>" . "define( 'PATH_CURRENT_SITE', '" . PATH_CURRENT_SITE . "' );" . "</li>";
			$str .= "<li>" . "define( 'SITE_ID_CURRENT_SITE', 1 );" . "</li>";
			$str .= "<li>" . "define( 'BLOG_ID_CURRENT_SITE', 1 );" . "</li>";
			$str .= "</ul>";
			$str .= "<p><strong>" . __( "Note: If your domain mapping plugin is WORKING correctly, then please LEAVE these lines in place.", 'domainmap' ) . "</strong></p>";

			$msg[] = $str;
		}

		if ( !empty( $msg ) ) {
			echo '<div class="domainmapping-info">', implode( '</div><div class="domainmapping-info">', $msg ), '</div>';
		}

		$this->_render_domain_configuration();
		$this->_render_administration_mapping();
		$this->_render_login_mapping();
		$this->_render_domain_mapping_table();
		$this->_render_pro_site();
	}

	/**
	 * Renders domain configuration section.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 */
	private function _render_domain_configuration() {
		?><h4><?php _e( 'Domain mapping Configuration', 'domainmap' ) ?></h4>
		<p>
			<?php _e( "Enter the IP address users need to point their DNS A records at. If you don't know what it is, ping this blog to get the IP address.", 'domainmap' ) ?><br>
			<?php _e( "If you have more than one IP address, separate them with a comma. This message is displayed on the Domain mapping page for your users.", 'domainmap' ) ?>
		</p>
		<p>
			<?php _e( "Server IP Address: ", 'domainmap' ) ?>
			<input type="text" name="map_ipaddress" class="regular-text" value="<?php echo esc_attr( $this->map_ipaddress ) ?>">
		</p><?php
	}

	/**
	 * Renders admin mapping section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 */
	private function _render_administration_mapping() {
		?><h4><?php _e( 'Administration mapping', 'domainmap' ) ?></h4>
		<p><?php _e( "The settings below allow you to control how the domain mapping plugin operates with the administration area.", 'domainmap' ) ?></p>
		<p>
			<?php _e('The domain used for the administration area should be the', 'domainmap') ?>
			<select name='map_admindomain'>
				<?php foreach ( $this->_get_mapping_options() as $map => $label ) : ?>
				<option value="<?php echo $map ?>"<?php selected( $map, $this->map_admindomain ) ?>><?php echo $label ?></option>
				<?php endforeach; ?>
			</select>
		</p><?php
	}

	/**
	 * Renders login mapping section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 */
	private function _render_login_mapping() {
		?><h4><?php _e( 'Login mapping', 'domainmap' ) ?></h4>
		<p><?php _e( "The settings below allow you to control how the domain mapping plugin operates with the login area.", 'domainmap' ) ?></p>
		<p>
			<?php _e( 'The domain used for the login area should be the', 'domainmap' ) ?>
			<select name="map_logindomain">
				<?php foreach ( $this->_get_mapping_options() as $map => $label ) : ?>
				<option value="<?php echo $map ?>"<?php selected( $map, $this->map_logindomain ) ?>><?php echo $label ?></option>
				<?php endforeach; ?>
			</select>
		</p><?php
	}

	/**
	 * Renders domain mapping table section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 * @global domain_map $dm_map The instance of core class.
	 */
	private function _render_domain_mapping_table() {
		global $dm_map;

		?><h4><?php _e( 'Domain Mapping Table', 'domainmap' ) ?></h4>
		<?php if ( $dm_map->check_for_table() ) : ?>
			<p><?php echo __( "The domain mapping table is called <strong>", 'domainmap' ), $dm_map->dmtable, __('</strong> and exists in your database.','domainmap') ?></p>
		<?php else : ?>
			<p>
				<?php echo __( "The domain mapping table should be called <strong>", 'domainmap' ), $dm_map->dmtable, __( '</strong> but does not seem to exist in your database and cannot be created.', 'domainmap' ) ?>
				<?php _e( "To create the database table you need to run the following SQL.", 'domainmap' ) ?>
			</p>
			<p>
				<textarea class="code" rows="10" cols="100" readonly><?php
					echo "CREATE TABLE IF NOT EXISTS `{$dm_map->dmtable}` (
    `id` bigint(20) NOT NULL auto_increment,
    `blog_id` bigint(20) NOT NULL,
    `domain` varchar(255) NOT NULL,
    `active` tinyint(4) default '1',
    PRIMARY KEY  (`id`),
    KEY `blog_id` (`blog_id`,`domain`,`active`)
);"
				?></textarea>
			</p><?php
		endif;
	}

	/**
	 * Renders pro site section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 */
	private function _render_pro_site() {
		if ( function_exists( 'is_pro_site' ) ) : ?>
			<h4><?php _e( 'Restricted Access', 'domainmap' ) ?></h4>
			<p><?php _e( 'Make this functionality only available to certain Pro Sites levels', 'domainmap' ) ?></p>

			<table>
				<tr>
					<td valign="top">
						<strong><?php _e( "Select Pro Sites Levels: ", 'domainmap' ) ?></strong>
					</td>
					<td valign="top">
						<ul style="margin-top: 0"><?php
							$levels = (array)get_site_option( 'psts_levels' );
							if ( !is_array( $this->map_supporteronly ) && !empty( $levels ) && $this->map_supporteronly == '1' ) {
								$keys = array_keys( $levels );
								$this->map_supporteronly = array( $keys[0] );
							}

							foreach ( $levels as $level => $value ) : ?>
								<li>
									<label>
										<input type="checkbox" name="map_supporteronly[]" value="<?php echo $level ?>"<?php checked( in_array( $level, (array)$this->map_supporteronly ) ) ?>>
										<?php echo $level, ': ', esc_html( $value['name'] ) ?>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
			</table>
		<?php endif;
	}

	/**
	 * Renders reseller options tab.
	 *
	 * @sicne 4.0.0
	 *
	 * @access private
	 */
	private function _render_reseller_options_tab() {
		$selected = false;

		?><div>
			<h4><?php _e( 'Select reseller provider if you want to sell domains to your users:', 'domainmap' ) ?></h4>

			<ul class="domainmapping-resellers-switch"><?php
				foreach ( $this->resellers as $hash => $reseller ) :
				?><li>
					<label>
						<input type="radio" class="domainmapping-reseller-switch" name="map_reseller"<?php checked( $hash, $this->map_reseller ) ?> value="<?php echo esc_attr( $hash ) ?>">
						<?php echo esc_html( $reseller->get_title() ) ?>
						<?php $selected = $hash == $this->map_reseller ? $hash : $selected ?>
					</label>
				</li><?php
				endforeach;

				?><li>
					<label>
						<input type="radio" class="domainmapping-reseller-switch" name="map_reseller"<?php checked( empty( $selected ) ) ?>>
						<?php _e( "Don't use any", 'domainmap' ) ?>
					</label>
				</li>
			</ul>
		</div><?php

		foreach ( $this->resellers as $hash => $reseller ) :
			?><div id="reseller-<?php echo $hash ?>" class="domainmapping-reseller-settings<?php echo $selected == $hash ? ' active' :'' ?>">
				<?php $reseller->render_options() ?>
			</div><?php
		endforeach;
	}

}