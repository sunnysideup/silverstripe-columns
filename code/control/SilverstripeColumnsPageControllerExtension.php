<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SilverstripeColumnsPageControllerExtension extends Extension
{

    private static $allowed_actions = [
        'myspecificpagemenuitems' => true
    ];

    /**
     * @return bool
     */
    function HasFullWidthContent()
    {
        if($this->owner->hasMethod('HasFullWidthContentOverloaded')) {
            $v = $this->owner->HasFullWidthContentOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        if($this->owner->owner->getFullWidthContent()) {
            return true;
        }
    }

    /**
     * @return bool
     */
    function HasSideBar()
    {
        if($this->owner->hasMethod('HasSideBarOverloaded')) {
            $v = $this->owner->HasSideBarOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        if(
            (
                $this->owner->UseDefaultSideBarContent() &&
                strlen($this->owner->getMyDefaultSidebarContent()) > 17
            )
            ||
            $this->owner->MySidebarImage()
        ) {
            return true;
        }
    }

    /**
     * @param boolean $asClassName
     *
     * @return string | int
     */
    function NumberOfColumns($asClassName = true)
    {
        if($this->owner->hasMethod('NumberOfColumnsOverloaded')) {
            return $this->owner->NumberOfColumnsOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        $count = 1;
        if($this->owner->HasSideBar()) {
            $count++;
        }
        if($asClassName) {
            $array = array(
                1 => 'one',
                2 => 'two',
                3 => 'three'
            );
            return $array[$count];
        }
        else {
            return $count;
        }
    }


    /**
     * returns a data list of items that have been edited last - up to one day ago.
     * This ensures that we do not show stuff we have just fixed up...
     * @return DataList
     */
    function RecentlyUpdated($limit = 5)
    {
        if($this->owner->hasMethod('RecentlyUpdatedOverloaded')) {
            $v = $this->owner->RecentlyUpdatedOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        return Page::get()
            ->filter(
                array(
                    'ShowInSearch' => true,
                    'LastEdited:LessThan' => date('Y-m-d h:i:s', time() - 86400)
                )
            )
            ->sort(array('LastEdited' => 'DESC'))
            ->limit($limit);
    }

    /**
     *  Children Menu Items
     * @return null | DataList
     */
    function InThisSection()
    {
        if($this->owner->hasMethod('InThisSectionOverloaded')) {
            $v = $this->owner->InThisSectionOverloaded();
            if($v !== null) {
                return $v;
            }
        }

        return $this->owner->ChildrenShowInMenu();
    }

    /**
     * Sibling Menu Items
     * @return null | DataList
     */
    function AlsoSee()
    {
        if($this->owner->hasMethod('AlsoSeeOverloaded')) {
            $v = $this->owner->AlsoSeeOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        if($this->owner->ParentID) {
            $parent = DataObject::get_one('Page', ['ParentID' => $this->owner->ParentID]);
            $list = $parent->ChildrenShowInMenu();
            $list->remove($this->owner);

            return $list;

        }
    }

    /**
     * returns relevant menus items for
     * @param  SS_Request
     * @return string (html)
     */
    function myspecificpagemenuitems($request)
    {
        if($this->owner->hasMethod('MySpecificPageMenuItemsOverloaded')) {
            $v = $this->owner->MySpecificPageMenuItemsOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        $id = intval($this->owner->request->param('ID'));
        $page = Page::get()->byID($id);
        if(!$page) {
            $page = DataObject::get_one('Page', ['ParentID' => 0]);
        }
        if($page) {
            return $page->renderWith('MyMenuItems');
        }
    }


}
