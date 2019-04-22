<?php namespace WPSettingsFramework\Framework;

/**
 * Class SettingFieldAbstract
 * @package PremmerceSettings
 */
abstract class SettingFieldAbstract {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var SettingsSection
	 */
	protected $section;

	/**
	 * @var mixed
	 */
	protected $defaultValue = false;

	/**
	 * SettingFieldAbstract constructor.
	 *
	 * @param string $name
	 * @param string $title
	 * @param array $args
	 */
	public function __construct( $name, $title, $args ) {
		$this->name         = $name;
		$this->title        = $title;
		$this->args         = $args;

		$this->defaultValue = ! empty( $args['default'] ) ? $args['default'] : false;
	}

	/**
	 * @param SettingsSection $section
	 *
	 * @return $this
	 */
	public function setSection( SettingsSection $section ) {
		$this->section = $section;
		return  $this;
	}

	/**
	 * @param bool $withPrefix
	 *
	 * @return string
	 */
	public function getName( $withPrefix = true ) {

		if ( $withPrefix && $this->getSection() ) {

			return $this->getSection()->getSlug() . '__' . $this->name;
		}

		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return SettingsSection
	 */
	public function getSection() {
		return $this->section;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {

		$value = get_option( $this->getName() );

		if ( ! $value ) {
			return $this->defaultValue;
		}

		return $value;
	}

	/**
	 * @return mixed
	 */
	abstract public function render();
}