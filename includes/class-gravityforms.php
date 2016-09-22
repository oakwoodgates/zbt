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
		add_action( 'gform_field_standard_settings', array( $this, 'standard_settings' ), 10, 2 );
		add_action( 'gform_editor_js', array( $this, 'gform_editor_js' ) );
	}

	// adding image button to html field
	function standard_settings( $position, $form_id ) {
		//create settings on position 225 (right after HTML content area)
		if ( $position == 225 ) { ?>
			<li class="add_image_button field_setting">
				<input type="button" class="button upload_image_button" value="<?php _e("Insert Image", "giih'")?>" onclick="triggerMedia(jQuery(this).parent().siblings('.content_setting').find('textarea'));"/>
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

				  htmltag = '<img src="'+attachment.url+'"/>';

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
}
