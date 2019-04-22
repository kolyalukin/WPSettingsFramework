<?php namespace WPSettingsFramework\Framework\Fields;

use WPSettingsFramework\Framework\SettingFieldAbstract;

class TextArea extends SettingFieldAbstract {

	public function render() {
		?>
        <textarea name="<?php echo $this->getName(); ?>" id="<?php echo $this->getName(); ?>" cols="30"
                  rows="5"><?php echo $this->getValue(); ?></textarea>
		<?php
	}
}