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
 * eNom reseller settings template.
 *
 * @category Domainmap
 * @package Render
 * @subpackage Reseller
 *
 * @since 4.0.0
 */
class Domainmap_Render_Reseller_WHMCS_Settings extends Domainmap_Render {

	/**
	 * Renders eNom settings notifications.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 */
	private function _render_notifications() {
		?><div id="domainmapping-whmcs-header">
			<div id="domainmapping-whmcs-logo"></div>
		</div>

		<div class="domainmapping-info">
            <?php _e( 'Keep in mind that to start using WHMCS API you have to white list and authorize your IP address. Go to "My Account" > General Settings > Security tab and add your IP to the list under "API IP Access Restriction"' , 'domainmap' ); ?></div>

		<div class="domainmapping-info">
			<b><?php esc_html_e( 'Signup for a WHMCS account.', 'domainmap' ) ?></b><br>
<!--			--><?php //esc_html_e( 'By signing up here as a sub-reseller you will avoid the high setup fees of direct accounts. You can of course switch to a direct eNom account later and change the credentials here to that.', 'domainmap' ) ?>
			<a target="_blank" href="http://www.whmcs.com/order-now/"><?php esc_html_e( 'Register new WHMCS account', 'domainmap' ) ?></a>.
            </div>
        <?php
	}

	/**
	 * Renders account credentials settings.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 */
	private function _render_account_settings() {
		// it is a bad habit to show raw password even if we use password input field
		// so lets shuffle it and render in the password field
		$pwd = str_shuffle( (string)$this->pwd );
		// we save shuffle hash to see on POST if the password was changed by an user
		$pwd_hash = md5( $pwd );

		?><h4 class="domainmapping-block-header"><?php _e( 'Account credentials:', 'domainmap' ) ?></h4>

		<?php if ( $this->valid === false ) : ?>
		<div class="domainmapping-info domainmapping-info-error">
			<p><?php _e( 'Looks like your credentials are invalid. Please, check the errors sent by WHMCS server:', 'domainmap' ) ?></p>
			<?php if ( is_wp_error( $this->errors ) ) : ?>
				<ul>
					<li>
						<b><?php echo implode( '</b></li><li><b>', array_map( 'esc_html', $this->errors->get_error_messages() ) ) ?></b>
					</li>
				</ul>
			<?php endif; ?>
		</div>
		<?php endif; ?>
        <div>
            <label for="whmcs-api" class="domainmapping-label"><?php _e( 'API url:', 'domainmap' ) ?></label>
            <input type="text" id="whmcs-api" class="regular-text" name="map_reseller_whmcs_api" value="<?php echo esc_attr( $this->api ) ?>" autocomplete="off">
        </div>

		<div>
			<label for="whmcs-uid" class="domainmapping-label"><?php _e( 'Username:', 'domainmap' ) ?></label>
			<input type="text" id="whmcs-uid" class="regular-text" name="map_reseller_whmcs_uid" value="<?php echo esc_attr( $this->uid ) ?>" autocomplete="off">
		</div>
		<div>
			<label for="whmcs-pwd" class="domainmapping-label"><?php _e( 'Password:', 'domainmap' ) ?></label>
			<input type="password" id="whmcs-pwd" class="regular-text" name="map_reseller_whmcs_pwd" value="<?php echo esc_attr( $pwd ) ?>" autocomplete="off">
			<input type="hidden" name="map_reseller_whmcs_pwd_hash" value="<?php echo $pwd_hash ?>">
		</div><?php
	}

	/**
	 * Renders payment gateways settings.
	 *
	 * @sine 4.0.0
	 *
	 * @access private
	 */
	private function _render_payment_settings() {
		?><h4 class="domainmapping-block-header"><?php _e( 'Select payment gateway:', 'domainmap' ) ?></h4>


		<div>
			<ul>
				<?php foreach ( $this->gateways as $key => $label ) : ?>
				<li>
					<label>
						<input type="radio" class="domainmapping-radio" name="map_reseller_enom_payment" value="<?php echo esc_attr( $key ) ?>"<?php checked( $key, $this->gateway )  ?>>
						<?php echo esc_html( $label ) ?>
					</label>
				</li>
				<?php endforeach; ?>
			</ul>
		</div><?php
	}


	/**
	 * Renders template.
	 *
	 * @since 4.0.0
	 *
	 * @access protected
	 */
	protected function _to_html() {
		$this->_render_notifications();
		$this->_render_account_settings();
		$this->_render_payment_settings();
	}

}