<?php namespace Settings\Fields;

use Settings\SettingFieldAbstract;

class TextField extends SettingFieldAbstract {

	public function render() {
		?>
        <input type="<?php echo $this->getType(); ?>"
               name="<?php echo $this->getName() ?>"
               value="<?php echo $this->getValue(); ?>"
        >
		<?php
	}

	public function getType() {
		return ! empty( $this->args['type'] ) ? $this->args['type'] : 'text';
	}
}