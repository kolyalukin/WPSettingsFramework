<?php namespace Settings\Fields;

use Settings\SettingFieldAbstract;

class Select extends SettingFieldAbstract {

	public function render() {
	    ?>
        <select name="<?php echo $this->getName(); ?>" id="<?php $this->getValue(); ?>">
			<?php foreach ( $this->getOptions() as $key => $option ): ?>
                <option value="<?php echo $key; ?>" <?php selected( true,
					$this->isSelected( $key ) ) ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
        </select>
		<?php
	}

	public function getOptions() {

		$args = $this->args;

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			return $args['options'];
		}

		return [];
	}

	public function isSelected( $option ) {

		if ( $this->getValue() === $option || $this->defaultValue === $option ) {
			return true;
		}

		return false;
	}
}