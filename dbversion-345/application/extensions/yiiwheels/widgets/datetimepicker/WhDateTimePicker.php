<?php
/**
 * WhDateTimePicker widget class
 * A simple implementation for date range picker for Twitter Bootstrap
 * @see <http://www.dangrossman.info/2012/08/20/a-date-range-picker-for-twitter-bootstrap/>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @copyright Copyright &copy; 2amigos.us 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package YiiWheels.widgets.datetimepicker
 * @uses YiiStrap.helpers.TbArray
 */
Yii::import('bootstrap.helpers.TbArray');

class WhDateTimePicker extends CInputWidget
{

	/**
	 * @var string $selector if provided, then no input field will be rendered. It will write the JS code for the
	 * specified selector.
	 */
	public $selector;

	/**
	 * @var string the date format.
	 */
	public $format = 'dd/MM/yyyy hh:mm:ss';

	/**
	 * @var string the icon to display when selecting times
	 */
	public $iconTime = 'icon-time';

	/**
	 * @var string the icon to display when selecting dates
	 */
	public $iconDate = 'icon-calendar';

	/**
	 * @var array pluginOptions to be passed to datetimepicker plugin. Defaults are:
	 *
	 * - maskInput: true, disables the text input mask
	 * - pickDate: true,  disables the date picker
	 * - pickTime: true,  disables de time picker
	 * - pick12HourFormat: false, enables the 12-hour format time picker
	 * - pickSeconds: true, disables seconds in the time picker
	 * - startDate: -Infinity, set a minimum date
	 * - endDate: Infinityset a maximum date
	 */
	public $pluginOptions = array();

	/**
	 * @var string[] the JavaScript event handlers.
	 */
	public $events = array();

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		$this->attachBehavior('ywplugin', array('class' => 'yiiwheels.behaviors.WhPlugin'));
		$this->htmlOptions['id'] = TbArray::getValue('id', $this->htmlOptions, $this->getEscapedId());
        foreach($this->pluginOptions as $key => $pluginOption){
            if(is_array($pluginOption)) continue;
            $this->htmlOptions['data-'.$key] = $pluginOption;
        }
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$this->renderField();
		$this->registerClientScript();
	}

	/**
	 * Renders the field if no selector has been provided
	 */
	public function renderField()
	{
		if (null === $this->selector) {
			$options = array();

			list($name, $id) = $this->resolveNameID();

			$options['id'] = $id . '_datetimepicker';
			TbHtml::addCssClass('input-group date', $options);

			echo TbHtml::openTag('div', $options);
			if ($this->hasModel()) {
				echo TbHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
			} else {
				echo TbHtml::textField($name, $this->value, $this->htmlOptions);
			}
            echo TbHtml::openTag('span', array('class' => 'input-group-addon'));
			echo TbHtml::openTag('span', array('class' => 'fa fa-calendar'));
			echo TbHtml::closeTag('span');
            echo TbHtml::closeTag('span');
			echo TbHtml::closeTag('div');
		}
	}


	/**
	 *
	 * Registers required css js files
	 */
	public function registerClientScript()
	{
		/* publish assets dir */
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
		$assetsUrl = $this->getAssetsUrl($path);

		/* @var $cs CClientScript */
		$cs = Yii::app()->getClientScript();
		$cs->registerPackage('bootstrap-datetimepicker');

		/* initialize plugin */
		$selector = null === $this->selector
			? '#' . TbArray::getValue('id', $this->htmlOptions, $this->getId()) . '_datetimepicker'
			: $this->selector;

		$this->getApi()->registerPlugin('datetimepicker', $selector, $this->pluginOptions, LSYii_ClientScript::POS_POSTSCRIPT);

		$showEventScript = $this->getShowEventScript($selector);
		$cs->registerScript("datetimepicker_show_handler_" . $this->getId(true), $showEventScript, CClientScript::POS_BEGIN);
		$hideEventScript = $this->getHideEventScript($selector);
		$cs->registerScript("datetimepicker_hide_handler_" . $this->getId(true), $hideEventScript, CClientScript::POS_BEGIN);

		if($this->events)
		{
			$this->getApi()->registerEvents($selector, $this->events);
		}
	}

    /**
     * If id contains brackets, we need to double escape it with \\
     * @return string
     */
    protected function getEscapedId()
    {
        $id = str_replace('[', '\\\\[',$this->getId());
        $id = str_replace(']', '\\\\]',$id);
        return $id;
	}

	protected function getShowEventScript($selector)
	{
		$script = '$(document).on("dp.show", "' . $selector . '", function () {
			$("#pjax-content").addClass("overflow-visible");
			$("#in_survey_common").addClass("overflow-visible");
		});';
		return $script;
	}

	protected function getHideEventScript($selector)
	{
		$script = '$(document).on("dp.hide", "' . $selector . '", function () {
			$("#pjax-content").removeClass("overflow-visible");
			$("#in_survey_common").removeClass("overflow-visible");
		});';
		return $script;
	}
}
