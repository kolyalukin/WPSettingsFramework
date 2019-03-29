<?php namespace Settings;

/**
 * Class SettingsPage
 * @package PremmerceSettings
 */
class SettingsPage {

	/**
	 * @var Tab[]
	 */
	protected $tabs = [];

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var null|Tab
	 */
	protected $defaultTab;

	/**
	 * SettingsPage constructor.
	 *
	 * @param string $slug
	 * @param string $title
	 */
	public function __construct( $slug, $title ) {
		$this->slug  = $slug;
		$this->title = $title;

		add_action( 'admin_menu', [ $this, 'init' ] );
	}

	/**
	 * @param Tab $tab
	 *
	 * @return $this
	 */
	public function addTab( Tab $tab ) {
		if ( ! array_key_exists( spl_object_hash( $tab ), $this->tabs ) ) {
			$this->tabs[ $tab->getSlug() ] = $tab;
		}

		return $this;
	}

	/**
	 * @param $tab
	 *
	 * @return $this
	 */
	public function setDefaultTab( $tab ) {
		if ( array_key_exists( $tab, $this->tabs ) ) {
			$this->defaultTab = $tab;

			return $this;
		}

		// TODO: THROW EXCEPTION
		return $this;
	}

	/**
	 *
	 */
	public function init() {

		if ( count( $this->tabs ) < 1 ) {
			return;
		}

		add_menu_page(
			$this->title,
			// todo: change
			$this->title,
			'manage_options',
			$this->slug,
			[ $this, 'render' ]
		);
	}

	/**
	 * @return array
	 */
	public function getAllOptions() {
		$settings = [];

		foreach ( $this->tabs as $tab ) {
			$settings[ $tab->getSlug() ] = $tab->getAllOptions();
		}

		return $settings;
	}

	/**
	 * @return Tab[]
	 */
	public function getTabs() {
		return $this->tabs;
	}

	/**
	 * @return bool|Tab
	 */
	public function getCurrentTab() {
		if ( ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->tabs ) ) {
			return $this->tabs[ $_GET['tab'] ];
		}

		if ( $this->defaultTab && array_key_exists( $this->defaultTab, $this->tabs ) ) {
			return $this->tabs[ $this->defaultTab ];
		}

		if ( ! empty( $this->tabs ) ) {
			return array_values( $this->tabs )[0];
		}

		return false;
	}

	/**
	 *
	 */
	public function render() {
		$currentTab = $this->getCurrentTab();

		if ( $currentTab ) {
			?>
            <div class="wrap">
                <h1><?php echo $this->title; ?></h1>

                <h2 class="nav-tab-wrapper">

					<?php foreach ( $this->tabs as $tab ): ?>
						<?php $class = ( $currentTab->getSlug() === $tab->getSlug() ) ? ' nav-tab-active' : ''; ?>
                        <a class='nav-tab<?php echo $class ?>'
                           href='?page=<?php echo $this->slug; ?>&tab=<?php echo $tab->getSlug() ?>'><?php echo $tab->getName(); ?></a>
					<?php endforeach; ?>

                </h2>
                <div>
					<?php
					$currentTab->render();
					?>
                </div>
				<?php do_action( $this->slug . '_settings_page_end', $this ) ?>
            </div>
			<?php
		}

	}
}