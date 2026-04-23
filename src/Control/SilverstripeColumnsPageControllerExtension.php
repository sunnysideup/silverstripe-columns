<?php

namespace Sunnysideup\Columns\Control;

use SilverStripe\Model\ArrayData;
use SilverStripe\Core\Extension;
use Page;

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SilverstripeColumnsPageControllerExtension extends Extension
{
    private static $allowed_actions = [
        'myspecificpagemenuitems' => true,
    ];

    /**
     * @return bool
     */
    public function HasFullWidthContent()
    {
        if ($this->getOwner()->hasMethod('HasFullWidthContentOverloaded')) {
            $v = $this->getOwner()->HasFullWidthContentOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        if ($this->getOwner()->owner->getFullWidthContent()) {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function HasSideBar()
    {
        if ($this->getOwner()->hasMethod('HasSideBarOverloaded')) {
            $v = $this->getOwner()->HasSideBarOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        if (
            (
                $this->getOwner()->UseDefaultSideBarContent() &&
                strlen((string) $this->getOwner()->getMyDefaultSidebarContent()) > 17
            )
            ||
            $this->getOwner()->MySidebarImage()
        ) {
            return true;
        }
    }

    /**
     * @param boolean $asClassName
     *
     * @return string | int
     */
    public function NumberOfColumns($asClassName = true)
    {
        if ($this->getOwner()->hasMethod('NumberOfColumnsOverloaded')) {
            return $this->getOwner()->NumberOfColumnsOverloaded();
        }

        $count = 1;
        if ($this->getOwner()->HasSideBar()) {
            $count++;
        }

        if ($asClassName) {
            $array = [
                1 => 'one',
                2 => 'two',
                3 => 'three',
            ];
            return $array[$count];
        } else {
            return $count;
        }
    }

    /**
     * returns a data list of items that have been edited last - up to one day ago.
     * This ensures that we do not show stuff we have just fixed up...
     * @return DataList
     */
    public function RecentlyUpdated($limit = 5)
    {
        if ($this->getOwner()->hasMethod('RecentlyUpdatedOverloaded')) {
            $v = $this->getOwner()->RecentlyUpdatedOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return Page::get()
            ->filter(
                [
                    'ShowInSearch' => true,
                    'LastEdited:LessThan' => date('Y-m-d h:i:s', time() - 86400),
                ]
            )
            ->sort(['LastEdited' => 'DESC'])
            ->limit($limit);
    }

    /**
     *  Children Menu Items
     * @return null | DataList
     */
    public function InThisSection()
    {
        if ($this->getOwner()->hasMethod('InThisSectionOverloaded')) {
            $v = $this->getOwner()->InThisSectionOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return $this->getOwner()->ChildrenShowInMenu();
    }

    /**
     * Sibling Menu Items
     * @return null | DataList
     */
    public function AlsoSee()
    {
        if ($this->getOwner()->hasMethod('AlsoSeeOverloaded')) {
            $v = $this->getOwner()->AlsoSeeOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        if ($this->getOwner()->ParentID) {
            $list = Page::get()->filter(['ShowInMenus' => 1, 'ParentID' => $this->getOwner()->dataRecord->ParentID]);

            return $list->exclude(['ID' => $this->getOwner()->ID]);
        }
    }

    /**
     * returns relevant menus items for
     * @param  SS_Request
     * @return string (html)
     */
    public function myspecificpagemenuitems($request)
    {
        if ($this->getOwner()->hasMethod('MySpecificPageMenuItemsOverloaded')) {
            $v = $this->getOwner()->MySpecificPageMenuItemsOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        $id = intval($this->getOwner()->request->param('ID'));
        $this->getOwner()->setShowMenuItemsFor($id);
        return ArrayData::create(
            [
                'MyMenuItems' => $this->getOwner()->MyMenuItems(),
                'MyMenuItemsParentPage' => $this->getOwner()->MyMenuItemsParentPage(),
                'MyMenuItemsParentLink' => $this->getOwner()->MyMenuItemsParentLink(),
            ]
        )
            ->RenderWith('MyMenuItems');
    }

    public function IsNotHome()
    {
        $link = $this->getOwner()->Link();

        return $link === 'home' || $link = '/';
    }
}
