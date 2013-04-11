<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Navigation_Frontend {

    public static function getTree($idcat = null, $level = 1) {

        $idlang = Aitsu_Registry::get()->env->idlang;
        $user = Aitsu_Adm_User::getInstance();
        $currentCat = Aitsu_Registry::get()->env->idcat;

        return self::_getCategorieChilds($idcat, $level, $idlang, $user, $currentCat);
    }

    private static function _getCategorieChilds($idcat, $level, $idlang, $user, $currentCat) {

        $categories = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   o.idcat, ' .
                        '   catlng.name, ' .
                        '   catlng.claim, ' .
                        '   catlng.public as isPublic, ' .
                        '   if (child.idcat is null, false, if(child.idcat = o.idcat, false, true)) as isParent, ' .
                        '   count(p.idcat)-1 as level ' .
                        'from ' .
                        '   _cat as n, ' .
                        '   _cat as p, ' .
                        '   _cat as o ' .
                        'left join ' .
                        '   _cat_lang as catlng on ( ' .
                        '       catlng.idcat = o.idcat ' .
                        '       and ' .
                        '       catlng.idlang =:idlang ' .
                        '   ) ' .
                        'left join ' .
                        '   _art_lang as artlng on ( ' .
                        '       artlng.idartlang = catlng.startidartlang ' .
                        '   ) ' .
                        'left join ' .
                        '   _cat as child on ( ' .
                        '       child.idcat =:currentCat ' .
                        '       and ' .
                        '       child.lft between o.lft and o.rgt ' .
                        '   ) ' .
                        'where ' .
                        '   o.lft between p.lft and p.rgt ' .
                        'and ' .
                        '   o.lft between n.lft and n.rgt ' .
                        'and ' .
                        '   n.idcat =:id ' .
                        'and ' .
                        '   ( ' .
                        '       artlng.online =:online ' .
                        '       and ' .
                        '       catlng.visible =:visible ' .
                        '   ) ' .
                        'group by ' .
                        '   o.lft ' .
                        'having ' .
                        '   level =:level ' .
                        'order by ' .
                        '   o.lft asc', array(
                    ':id' => $idcat,
                    ':level' => $level,
                    ':idlang' => $idlang,
                    ':online' => 1,
                    ':visible' => 1,
                    ':currentCat' => $currentCat
        ));

        foreach ($categories as $key => $category) {
            $isAccessible = true;

            if (!$category['isPublic']) {
                $isAccessible = false;

                if ($user != null) {
                    $isAccessible = $user->isAllowed(array(
                        'language' => $idlang,
                        'resource' => array(
                            'type' => 'cat',
                            'id' => $category['idcat']
                        )
                    ));
                }
            }

            if ($isAccessible) {
                $categories[$key]['isCurrent'] = $currentCat == $category['idcat'] ? true : false;
                $categories[$key]['isParent'] = (bool) $categories[$key]['isParent'];

                $children = self::_getCategorieChilds($category['idcat'], $category['level'] + 1, $idlang, $user, $currentCat);

                if (empty($children)) {
                    $categories[$key]['hasChildren'] = false;
                } else {
                    $categories[$key]['hasChildren'] = true;
                    $categories[$key]['children'] = $children;
                }
            } else {
                unset($categories[$key]);
            }

            unset($categories[$key]['isPublic']);
            unset($categories[$key]['level']);
        }

        return $categories;
    }

}