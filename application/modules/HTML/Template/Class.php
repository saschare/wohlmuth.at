<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 *
 * {@id $Id: Class.php 19954 2010-11-18 20:00:29Z akm $}
 */

class Module_HTML_Template_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Template',
			'description' => Aitsu_Translate :: translate('Outputs the content of the default template or the specified template, if configured accordingly.'),
			'type' => 'Layout',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce57ea4-8b60-4118-aaf1-4c097f000101'
		);
	}

	protected static function _getDefaultTemplate($index, $params) {

		if (!isset (Aitsu_Article_Config :: factory()->module->template-> $index->defaultTemplate)) {
			return $params->defaultTemplate;
		}

		$defaultTemplate = Aitsu_Article_Config :: factory()->module->template-> $index->defaultTemplate;

		if (!isset ($defaultTemplate->ifindex)) {
			return $defaultTemplate->default;
		}

		if (Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->isIndex()) {
			return $defaultTemplate->ifindex;
		}

		return $defaultTemplate->default;
	}

	public static function init($context) {

		if (isset ($_REQUEST['renderOnly'])) {
			return '<script type="application/x-aitsu" src="' . $_REQUEST['renderOnly'] . '">' . (isset ($_REQUEST['params']) ? $_REQUEST['params'] : '') . '</script>';
		}

		Aitsu_Content_Edit :: noEdit('Template', true);

		$index = str_replace('_', ' ', $context['index']);
		$parameters = ($context['params'] === null) ? null : Aitsu_Util :: parseSimpleIni($context['params']);
		$params = Aitsu_Ee_Config_Hidden :: set($index, 'Template_params', $parameters);

		$idartlang = Aitsu_Registry :: get()->env->idartlang;

		$startTag = '';
		$endTag = '';
		$output = '';

		$keys = array ();

		if (isset ($params->template)) {

			$data = (array) $params->template;

			foreach ($data as $key => $line) {
				$keyValuePairs[$line->name] = $key;
				$keys[] = $key;
			}
			$template = Aitsu_Ee_Config_Radio :: set($index, 'SubTemplate', '', $keyValuePairs, 'Template');

			if (Aitsu_Registry :: isEdit()) {
				$startTag = '<div id="Template-' . $index . '-' . $idartlang . '" class="aitsu_editable on-demand"><div class="aitsu_hover">';
				$startTag .= '<div class="show-on-demand" style="cursor:pointer; background-color:black; color:white; padding:10px; margin-bottom:5px; display:none;">Edit template area <strong>' . $index . '</strong></div>';
				$endTag = '</div></div>';
			}

			if (Aitsu_Registry :: isBoxModel() && count($keys) > 1) {
				$startTag = '<shortcode method="Template" index="' . $index . '">';
				$startTag .= 'isEdit: ' . var_export(Aitsu_Registry :: isEdit(), true);
				$endTag = '</shortcode>';
			}

			if (empty ($template) && isset ($params->defaultTemplate)) {
				$template = self :: _getDefaultTemplate($index, $params);
			}
		} else {
			$template = self :: _getDefaultTemplate($index, $params);

			if (!isset ($params->defaultTemplate) && $index != 'Root') {
				$output .= '<!-- use of template shortcode without defaultTemplate ' . var_export($context, true) . ' -->';
			}
		}

		$code = '';

		if ((Aitsu_Registry :: isEdit() || Aitsu_Registry :: get()->env->editAction == '1') && count($keys) > 1) {
			$parameters = str_replace("\n", '\n', str_replace("\r\n", "\n", $context['params']));
			$code = '<code class="aitsu_params" style="display:none;">' . $parameters . '</code>';
		}

		try {
			if (!empty ($template)) {
				$view = new Zend_View();
				$view->setScriptPath(APPLICATION_PATH . '/skins/' . Aitsu_Registry :: get()->config->skin);

				if (isset ($params->template-> $template->param)) {
					$view->param = $params->template-> $template->param;
				}

				if (isset ($params->template-> $template->file)) {
					$output = $view->render($params->template-> $template->file);
				} else {
					/*
					 * No templates given. The defaultTemplate must therefore represent a path
					 * to the template.
					*/
					$output = $view->render($template);
				}
			}
		} catch (Exception $e) {
			$output = '<strong>' . $e->getMessage() . '</strong><pre>' . $e->getTraceAsString() . '</pre>';
		}

		return $startTag . $code . $output . $endTag;
	}
}