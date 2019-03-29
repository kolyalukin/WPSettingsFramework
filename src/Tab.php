<?php namespace Settings;

/**
 * Class Tab
 * @package PremmerceSettings
 */
class Tab {

    /**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var SettingsSection[]
	 */
	protected $sections = [];
	/**
	 * @var null|SettingsSection
	 */
	protected $defaultSection;


	/**
	 * Tab constructor.
	 *
	 * @param $slug
	 * @param $name
	 * @param null $defaultSection
	 */
	public function __construct( $slug, $name, $defaultSection = null ) {
		$this->slug = $slug;
		$this->name = $name;

		$this->defaultSection = $defaultSection;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * @param SettingsSection $section
	 *
	 * @return $this
	 */
	public function addSection( SettingsSection $section ) {
		if ( ! array_key_exists( $section->getSlug(), $this->sections ) ) {
			$this->sections[ $section->getSlug() ] = $section;
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getAllOptions() {
		$options = [];

		foreach ( $this->sections as $section ) {
			$options[] = $section->getAllOptions();
		}

		return $options;
	}

	/**
	 *
	 */
	public function getSections() {
		$this->sections;
	}

	/**
	 *
	 */
	public function render() {

	    settings_errors();

		if ( count( $this->sections ) > 1 ) {
			$_sections = array_values( $this->sections );
			$last      = end( $_sections );
			?>
            <div style="margin-top: 10px">
				<?php foreach ( $this->sections as $section ): ?>
					<?php if ( $this->getCurrentSection()->getSlug() === $section->getSlug() ): ?>
                        <b><?php echo $section->getTitle(); ?></b>
					<?php else: ?>
                        <a href="<?php echo add_query_arg( [ 'section' => $section->getSlug() ] ) ?>"><?php echo $section->getTitle(); ?></a>
					<?php endif; ?>
					<?php if ( $last->getSlug() !== $section->getSlug() ): ?>
                        |
					<?php endif; ?>
				<?php endforeach; ?>
            </div>
			<?php
		}

		$section = $this->getCurrentSection();

		if ( $section ) {
			$section->render();
		}
	}

	/**
	 * @return bool|SettingsSection
	 */
	public function getCurrentSection() {

		if ( ! empty( $_GET['section'] ) && array_key_exists( $_GET['section'], $this->sections ) ) {
			return $this->sections[ $_GET['section'] ];
		}

		if ( $this->defaultSection && array_key_exists( $this->defaultSection, $this->sections ) ) {
			return $this->sections[ $this->defaultSection ];
		}

		if ( ! empty( $this->sections ) ) {
			return array_values( $this->sections )[0];
		}

		return false;
	}
}