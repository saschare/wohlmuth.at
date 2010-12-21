<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Forms_Renderer_ExtJs {

	public static function render(Aitsu_Forms $form) {

		$out = 'new Ext.FormPanel({';

		$params = $form->getParams()->extjs->toArray();
		$params['title'] = $form->title;
		$params['url'] = $form->url;
		$params['id'] = $form->getUid();

		array_walk($params, array (
			self,
			'_transform'
		));

		$out .= implode(', ', $params);

		$groups = $form->getGroups()->toArray();

		array_walk($groups, array (
			self,
			'_transformGroups'
		));

		$out .= ', items: [' . implode(', ', $groups) . ']';

		$buttons = $form->getButtons()->toArray();

		array_walk($buttons, array (
			self,
			'_transformButtons'
		), $form->getUid());

		$out .= ', buttons: [' . implode(', ', $buttons) . ']';

		$out .= '})';

		return $out;
	}

	protected static function _transform(& $value, $key) {

		$val = null;

		if ($value === true || $value == 'true') {
			$val = 'true';
		}
		elseif ($value === false || $value == 'false') {
			$val = 'false';
		}
		elseif (is_numeric($value)) {
			$val = $value;
		}
		elseif (is_array($value)) {
			array_walk($value, array (
				self,
				'_transform'
			));
			$val = '{' . implode(', ', $value) . '}';
		} else {
			$val = "'$value'";
		}

		$value = $key . ': ' . $val;
	}

	protected static function _transformGroups(& $value, $key) {

		$configs = array ();

		$configs[] = "xtype: '{$value['type']}'";
		$configs[] = "title: '{$value['legend']}'";

		if (isset ($value['extjs'])) {
			foreach ($value['extjs'] as $key => $val) {
				if (is_array($val)) {
					array_walk($val, array (
						self,
						'_transform'
					));
					$configs[] = $key . ': {' . implode(', ', $val) . '}';
				} else {
					$configs[] = "$key: '$val'";
				}
			}
		}

		$fields = $value['field'];

		array_walk($fields, array (
			self,
			'_transformFields'
		));

		$configs[] = 'items: [' . implode(', ', $fields) . ']';

		$value = '{' . implode(', ', $configs) . '}';
	}

	protected static function _transformFields(& $value, $key) {

		$configs = array ();

		$configs[] = "xtype: '{$value['type']}'";
		$configs[] = "fieldLabel: '{$value['label']}'";
		$configs[] = "name: '{$key}'";
		$configs[] = "id: '{$key}'";

		if (isset ($value['extjs'])) {
			foreach ($value['extjs'] as $key => $val) {
				if (is_array($val)) {
					array_walk($val, array (
						self,
						'_transform'
					));
					$configs[] = $key . ': {' . implode(', ', $val) . '}';
				} else {
					$configs[] = "$key: '$val'";
				}
			}
		}

		$value = '{' . implode(', ', $configs) . '}';
	}

	protected static function _transformButtons(& $value, $key, $uid) {

		if (isset ($value['text'])) {
			$value = "{tooltip: '{$value['text']}'";
		} else {
			$value = "{tooltip: ''";
		}

		if ($key == 'save') {
			$value .= ", iconCls: 'save'";
			$value .= ", handler: function() {Ext.getCmp('$uid').getForm().submit({success: function() {alert('success');}});}";
		}

		$value .= '}';
	}
}