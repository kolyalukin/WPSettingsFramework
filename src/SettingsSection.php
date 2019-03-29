<?php namespace Settings;

class SettingsSection {

	/**
	 * @var SettingFieldAbstract[]
	 */
	protected $fields = [];

	/**
	 * @var string $title
	 */
	protected $title;

	/**
	 * @var string $htmlContent
	 */
	protected $htmlContent;

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * SettingsSection constructor.
	 *
	 * @param string $slug
	 * @param string $title
	 */
	public function __construct( $slug, $title ) {
		$this->title = $title;
		$this->slug  = $slug;

		return $this;
	}

	/**
	 * Get section id
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * Get section title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set content of section
	 *
	 * @param string $content
	 *
	 * @return $this
	 */
	public function setContent( $content ) {
		$this->htmlContent = $content;

		return $this;
	}

	/**
	 * @param SettingFieldAbstract $field
	 *
	 * @param bool $useInSection
	 *
	 * @return $this
	 */
	public function addField( SettingFieldAbstract $field, $useInSection = true ) {

		if ( $useInSection ) {
			$field->setSection( $this );
		}

		if ( ! array_key_exists( $field->getName(), $this->fields ) ) {
			$this->fields[ $field->getName() ] = $field;
		}

		return $this;
	}

	/**
	 * @param bool $withPrefix
	 *
	 * @return array
	 */
	public function getAllOptions( $withPrefix = false ) {
		$options = [];

		foreach ( $this->fields as $field ) {
			$options[ $field->getName( $withPrefix ) ] = $field->getValue();
		}

		return $options;
	}

	/**
	 * @return SettingFieldAbstract[]
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Render content of section
	 */
	public function render() {
		if ( ! empty( $this->htmlContent ) ) {
			echo $this->htmlContent;

			return;
		}
		?>
        <form method="post" action="options.php">

			<?php wp_nonce_field( 'update-options' ); ?>

            <table class="form-table">
				<?php foreach ( $this->fields as $field ): ?>
                    <tr>
                        <th scope="row"><?php echo $field->getTitle(); ?></th>
                        <td>
							<?php echo $field->render(); ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
            </table>

            <input type="hidden" name="action" value="update"/>

            <input type="hidden" name="page_options"
                   value="<?php echo implode( ',', array_keys( $this->getAllOptions( true ) ) ); ?>"/>

			<?php submit_button( __( 'Save Changes' ) ); ?>
        </form>
		<?php
	}

	/**
	 * Return content of section
	 *
	 * @return false|string
	 */
	public function getHtml() {
		ob_start();
		$this->render();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}