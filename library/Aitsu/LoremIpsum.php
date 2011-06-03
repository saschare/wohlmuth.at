<?php


/**
 * Simple Lorem ipsum generator.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: LoremIpsum.php 16580 2010-05-26 07:23:38Z akm $}
 */

class Aitsu_LoremIpsum {

	const LOREMIPSUM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sagittis, neque in 
	fringilla cursus, turpis ipsum facilisis velit, non aliquam diam massa ut enim. Duis 
	in elit urna. Integer nec lacus id est luctus venenatis at in ipsum. Nullam eget 
	enim sit amet arcu blandit eleifend. Nullam dictum sodales ullamcorper. Mauris nulla 
	nunc, pulvinar quis porttitor in, scelerisque sit amet nisl. Ut ac libero quis felis 
	lacinia aliquam sed vitae sapien. Nulla varius luctus purus, at auctor purus consectetur 
	et. Nulla facilisi. Proin ac turpis neque. Fusce nec nibh diam, eget fermentum libero. 
	Sed dictum, turpis id semper aliquam, sapien metus ultrices metus, ac varius lectus 
	tellus sed risus. In lacinia enim accumsan felis blandit lacinia. Ut in nunc eget sapien 
	aliquam rhoncus quis vel ligula. Ut felis quam, tincidunt sit amet malesuada in, 
	fringilla eu tortor. Aliquam egestas nibh blandit arcu pretium accumsan. 
	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sagittis, neque in 
	fringilla cursus, turpis ipsum facilisis velit, non aliquam diam massa ut enim. Duis 
	in elit urna. Integer nec lacus id est luctus venenatis at in ipsum. Nullam eget 
	enim sit amet arcu blandit eleifend. Nullam dictum sodales ullamcorper. Mauris nulla 
	nunc, pulvinar quis porttitor in, scelerisque sit amet nisl. Ut ac libero quis felis 
	lacinia aliquam sed vitae sapien. Nulla varius luctus purus, at auctor purus consectetur 
	et. Nulla facilisi. Proin ac turpis neque. Fusce nec nibh diam, eget fermentum libero. 
	Sed dictum, turpis id semper aliquam, sapien metus ultrices metus, ac varius lectus 
	tellus sed risus. In lacinia enim accumsan felis blandit lacinia. Ut in nunc eget sapien 
	aliquam rhoncus quis vel ligula. Ut felis quam, tincidunt sit amet malesuada in, 
	fringilla eu tortor. Aliquam egestas nibh blandit arcu pretium accumsan. 
	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sagittis, neque in 
	fringilla cursus, turpis ipsum facilisis velit, non aliquam diam massa ut enim. Duis 
	in elit urna. Integer nec lacus id est luctus venenatis at in ipsum. Nullam eget 
	enim sit amet arcu blandit eleifend. Nullam dictum sodales ullamcorper. Mauris nulla 
	nunc, pulvinar quis porttitor in, scelerisque sit amet nisl. Ut ac libero quis felis 
	lacinia aliquam sed vitae sapien. Nulla varius luctus purus, at auctor purus consectetur 
	et. Nulla facilisi. Proin ac turpis neque. Fusce nec nibh diam, eget fermentum libero. 
	Sed dictum, turpis id semper aliquam, sapien metus ultrices metus, ac varius lectus 
	tellus sed risus. In lacinia enim accumsan felis blandit lacinia. Ut in nunc eget sapien 
	aliquam rhoncus quis vel ligula. Ut felis quam, tincidunt sit amet malesuada in, 
	fringilla eu tortor. Aliquam egestas nibh blandit arcu pretium accumsan. 
	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sagittis, neque in 
	fringilla cursus, turpis ipsum facilisis velit, non aliquam diam massa ut enim. Duis 
	in elit urna. Integer nec lacus id est luctus venenatis at in ipsum. Nullam eget 
	enim sit amet arcu blandit eleifend. Nullam dictum sodales ullamcorper. Mauris nulla 
	nunc, pulvinar quis porttitor in, scelerisque sit amet nisl. Ut ac libero quis felis 
	lacinia aliquam sed vitae sapien. Nulla varius luctus purus, at auctor purus consectetur 
	et. Nulla facilisi. Proin ac turpis neque. Fusce nec nibh diam, eget fermentum libero. 
	Sed dictum, turpis id semper aliquam, sapien metus ultrices metus, ac varius lectus 
	tellus sed risus. In lacinia enim accumsan felis blandit lacinia. Ut in nunc eget sapien 
	aliquam rhoncus quis vel ligula. Ut felis quam, tincidunt sit amet malesuada in, 
	fringilla eu tortor. Aliquam egestas nibh blandit arcu pretium accumsan. 
	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sagittis, neque in 
	fringilla cursus, turpis ipsum facilisis velit, non aliquam diam massa ut enim. Duis 
	in elit urna. Integer nec lacus id est luctus venenatis at in ipsum. Nullam eget 
	enim sit amet arcu blandit eleifend. Nullam dictum sodales ullamcorper. Mauris nulla 
	nunc, pulvinar quis porttitor in, scelerisque sit amet nisl. Ut ac libero quis felis 
	lacinia aliquam sed vitae sapien. Nulla varius luctus purus, at auctor purus consectetur 
	et. Nulla facilisi. Proin ac turpis neque. Fusce nec nibh diam, eget fermentum libero. 
	Sed dictum, turpis id semper aliquam, sapien metus ultrices metus, ac varius lectus 
	tellus sed risus. In lacinia enim accumsan felis blandit lacinia. Ut in nunc eget sapien 
	aliquam rhoncus quis vel ligula. Ut felis quam, tincidunt sit amet malesuada in, 
	fringilla eu tortor. Aliquam egestas nibh blandit arcu pretium accumsan. ';

	public static function get($words = 50) {

		$list = explode(' ', self :: LOREMIPSUM);
		$text = implode(' ', array_slice($list, 0, min($words, count($list))));
		$text = rtrim($text, '., ');

		return $text . '.';
	}
}