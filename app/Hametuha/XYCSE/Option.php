<?php

namespace Hametuha\XYCSE;


use Hametuha\XYCSE\Pattern\Application;

/**
 * Option setting.
 *
 * @package Hametuha\XYCSE
 */
class Option extends Application {

	protected $slug = 'xycse-setting';

	/**
	 * Get title string
	 *
	 * @return string
	 */
	protected function get_title() {
		return __( 'XYCSE Setting', 'xycse' );
	}

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	protected function on_construct( array $arguments = [] ) {
		// Add menu
		add_action( 'admin_menu', function () {
			$title = $this->get_title();
			add_options_page( $title, $title, 'manage_options', $this->slug, [ $this, '_render' ] );
		} );
		// Enqueu scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_script' ] );
		// Save action.
		add_action( 'wp_ajax_xycse_option', [ $this, '_ajax' ] );
	}

	/**
	 * Get option
	 *
	 * @return array
	 */
	public function get() {
		return (array) get_option( 'xycse_option', [] );
	}

	/**
	 * Setting page
	 *
	 * @param string $slug
	 */
	public function enqueue_script( $slug ) {
		if ( 'settings_page_xycse-setting' != $slug ) {
			return;
		}
		// Register scripts
		wp_enqueue_script( 'xycse-setting' );
		// Add variables
		$variables = [
			'endpoint'   => admin_url( 'admin-ajax.php' ) . '?action=xycse_option&_wpnonce=' . wp_create_nonce( 'xycse_option' ),
			'post_types' => [],
			'taxonomies' => [],
			'message'    => [
				'unique'      => __( 'Model name must be unique.', 'xycse' ),
				'alnum'       => __( 'Model name should be alpha-numeric', 'xycse' ),
				'require'     => __( 'Required field is empty.', 'xycse' ),
				'unsatisfied' => __( 'Either Object or Category must be selected.', 'xycse' ),
			],
			'dates'      => [
				[
					'name'  => 'no',
					'label' => __( 'No time', 'xycse' ),
				],
				[
					'name'  => 'at',
					'label' => __( 'at particular Time', 'xycse' ),
				],
				[
					'name'  => 'range',
					'label' => __( 'Time range', 'xycse' ),
				],
			],
			'option'     => $this->get(),
		];
		foreach ( get_post_types( [], OBJECT ) as $post_type ) {
			if ( false === array_search( $post_type->name, [
					'post',
					'page',
					'attachment'
				] ) && $post_type->_builtin
			) {
				continue;
			}
			$variables['post_types'][] = [
				'name'  => $post_type->name,
				'label' => $post_type->label,
			];
		}
		foreach ( get_taxonomies( [], OBJECT ) as $taxonomy ) {
			if ( false === array_search( $taxonomy->name, [ 'category', 'post_tag' ] ) && $taxonomy->_builtin ) {
				continue;
			}
			$variables['taxonomies'][] = [
				'name'  => $taxonomy->name,
				'label' => $taxonomy->label,
			];
		}
		wp_localize_script( 'xycse-setting', 'Xycse', $variables );
	}

	/**
	 * Handle Ajax request
	 */
	public function _ajax() {
		try {
			if ( ! $this->input->verify_nonce( 'xycse_option' ) ) {
				throw new \Exception( __( 'You have no permission.', 'xycse' ), 401 );
			}
			$option = ( array ) $this->input->post_body( true );
			update_option( 'xycse_option', $option );
			wp_send_json( [
				'message' => __( 'Option is updated.', 'xycse' ),
			] );
		} catch ( \Exception $e ) {
			status_header( $e->getCode() );
			wp_send_json( [
				'status'  => $e->getCode(),
				'message' => $e->getMessage(),
				'error'   => true,
			] );
		}
	}

	/**
	 * Render admin screen
	 */
	public function _render() {
		?>
		<div class="wrap xycse-wrap" ng-app="xycse">

			<h2><span class="dashicons dashicons-text"></span> <?php esc_html_e( $this->get_title() ) ?></h2>

			<div class="xycse-fields" ng-controller="xycseForm" ng-class="{loading: loading}">

				<p class="description">
					<?php esc_html_e( 'Add relationship model.', 'xycse' ) ?>
					<?php printf( esc_html__( '%s is required.', 'xycse' ), '<span class="required">*</span>' ); ?>
				</p>

				<div class="xycse-message" ng-if="message.length" ng-class="{error: error, updated: !error}">
					<p ng-repeat="m in message">{{m}}</p>
				</div>

				<table class="xycse-controller">
					<thead>
					<tr>
						<th>
							<label for="xycse-name">
								<?= esc_html_x( 'Definition Name', 'definition', 'xycse' ) ?>
								<span class="required">*</span>
							</label>
						</th>
						<th>
							<label for="xycse-subject">
								<?= esc_html_x( 'Subject', 'definition', 'xycse' ) ?>
								<span class="required">*</span>
							</label>
						</th>
						<th>
							<label for="xycse-object">
								<?= esc_html_x( 'Object', 'definition', 'xycse' ) ?>
							</label>
						</th>
						<th>
							<label for="xycse-category">
								<?= esc_html_x( 'Category', 'definition', 'xycse' ) ?>
							</label>

						</th>
						<th>
							<label for="xycse-time">
								<?= esc_html_x( 'Time', 'definition', 'xycse' ) ?>
							</label>
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>
							<input type="text" id="xycse-name" ng-model="current.name"/>
						</td>
						<td>
							<select id="xycse-subject" ng-model="current.subject">
								<option ng-repeat="post_type in post_types" value="{{post_type.name}}"
								        ng-selected="post_type.name == current.subject">
									{{post_type.label}}
								</option>
							</select>
						</td>
						<td>

							<select id="xycse-object" ng-model="current.object">
								<option value="" ng-selected="'' == current.object">
									<?php esc_html_e( 'No object', 'xycse' ) ?>
								</option>
								<option ng-repeat="post_type in post_types" value="{{post_type.name}}"
								        ng-selected="post_type.name == current.object">
									{{post_type.label}}
								</option>
							</select>
						</td>
						<td>
							<select id="xycse-category" ng-model="current.taxonomy">
								<option value="" ng-selected="'' == current.taxonomy">
									<?php esc_html_e( 'No category', 'xycse' ) ?>
								</option>
								<option ng-repeat="taxonomy in taxonomies" value="{{taxonomy.name}}"
								        ng-selected="taxonomy.name == current.taxonomy">
									{{taxonomy.label}}
								</option>
							</select>

						</td>
						<td>
							<select id="xycse-time" ng-model="current.date">
								<option ng-repeat="date in dates" value="{{date.name}}"
								        ng-selected="date.name == current.date">
									{{date.label}}
								</option>
							</select>

						</td>
					</tr>
					</tbody>
				</table>

				<p class="xycse-button">
					<button class="button" ng-click="add()"><?php esc_html_e( 'Add new model' ) ?></button>
				</p>


				<div class="xycse-list-wrapper">

					<div class="no-content" ng-if="!option.length">
						<p class="description">
							<?php esc_html_e( 'No model is registered.', 'xycse' ) ?>
						</p>
					</div>

					<ul class="xycse-list">

						<li class="xycse-item" ng-repeat="item in option">
							<span class="xycse-item-title">
								<?php esc_html_e( 'Model Name: ', 'xycse' ) ?>
								<strong>{{item.name}}</strong>
							</span>

							<xycse-span item="item"></xycse-span>

							<button ng-click="remove($index)" title="<?php esc_attr_e( 'Remove', 'xycse' ) ?>">
								<span class="dashicons dashicons-no"></span>
							</button>

						</li>

					</ul>

					<p class="submit">
						<button ng-click="save()"
						        class="button-primary"><?php esc_html_e( 'Save Models', 'xysce' ) ?></button>
						<span class="xycse-loading" ng-if="loading">
						<span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Saving...', 'xycse' ) ?>
					</span>
					</p>
				</div>

			</div><!-- //.xycse-fields -->

		</div>
		<?php
	}


}
