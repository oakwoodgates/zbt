<?php
/**
 * zbt Gravityforms
 *
 * @since 0.0.1
 * @since 0.0.2 media uploader in html field
 * @package zbt
 */
// References
// http://wpsmith.net/2011/plugins/how-to-create-a-custom-form-field-in-gravity-forms-with-a-terms-of-service-form-field-example/
// https://github.com/ethanpil/gravity-forms-image-in-html/
/**
 * zbt Gravityforms.
 *
 * @since 0.0.1
 */
class ZBT_Gravityforms {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function hooks() {
	//	add_action( 'gform_field_standard_settings', array( $this, 'standard_settings' ), 10, 2 );
	//	add_action( 'gform_editor_js', array( $this, 'gform_editor_js' ) );
		add_action( 'gform_enqueue_scripts_16', array( $this, 'enqueue_css' ), 10, 2 );
		add_action( 'gform_after_submission_16', array( $this, 'gf_16_to_op' ), 10, 2 );
	}

	// adding image button to html field
	function standard_settings( $position, $form_id ) {
		//create settings on position 225 (right after HTML content area)
		if ( $position == 225 ) { ?>
			<li class="add_image_button field_setting">
				<input type="button" class="button upload_image_button" value="<?php _e("Insert Image", "zbt'")?>" onclick="triggerMedia(jQuery(this).parent().siblings('.content_setting').find('textarea'));"/>
			</li>
			<?php
		}
	}

	// Add the Editor Form JS
	function gform_editor_js(){
		wp_enqueue_media();
		?>
		<script type='text/javascript'>
			var file_frame;
			function triggerMedia(target) {

			//console.log(target);

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
				  file_frame.open();
				  return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
				  title: jQuery( this ).data( 'uploader_title' ),
				  button: {
					text: jQuery( this ).data( 'uploader_button_text' ),
				  },
				  multiple: false  // Set to true to allow multiple files to be selected
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
				  // We set multiple to false so only get one image from the uploader
				  attachment = file_frame.state().get('selection').first().toJSON();

				  htmltag = '<img src="'+attachment.url+'">';

				  InsertVariable( target.attr('id'), null, htmltag  );

				  // trigger the change event to fire any functions tied to this input's onchange
				  target.change();
				});

				// Finally, open the modal
				file_frame.open();
			}

			jQuery(document).ready(function($) {

				//add the button 'setting' to html fields
				fieldSettings["html"] += ", .add_image_button";
			});
		</script>
		<?php
	}

	function enqueue_css( $form, $is_ajax ) {
		wp_enqueue_style( 'zbt_gf_quiz', ZBT::url( 'assets/gf_cbtr_quiz.css' ) );
	}

	function gf_16_to_op( $entry, $form ) {
		$email = rgar( $entry, '13' );
		$score = rgar( $entry, '34' );
		$first = rgar( $entry, '12.3' );
		$op_id = '';

		$data = array(
			'email'		=> $email,
			'firstname' => $first,
		);
		// Update/Create a contact if the email record
		// does/not exist in Ontraport
		$op = wontrapi_update_or_create_contact( $email, $data );

		// if the contact was created new in Ontraport,
		// get the id of the new user from OP
		$op_id = ( ! empty( $op->data->id ) ) ? $op->data->id : $op_id;
		// if contact was updated (or failed?) it
		// does not return the id from Ontraport
		if ( ! $op_id ){
			$op = '';
			// get the contact from OP by email
			$op = wontrapi_get_contacts_by( 'email', $email );
			// get the id of the user in OP
			if ( ! empty( $op->data[0]->id ) ){
				$op_id = $op->data[0]->id;
			} else {
				// something messed up
				return;
			}
		}

		// conditions
		$cbtr = ( $score < 16 ? '639' : ( $score < 24 ? '640' : '641' ) );
		// add tags to user
		wontrapi_add_tags_to_contacts( array( $op_id ), array( '638', $cbtr ) );

	}
}


