<?php

namespace Sunnysideup\Columns\Model;

use SilverStripe\Model\List\ArrayList;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\AssetAdmin\Forms\UploadField;

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SilverstripeColumnsPageExtension extends Extension
{
    private static $db = [
        'Summary' => 'HTMLVarchar(255)',
        'DefaultSidebarContent' => 'HTMLText',
    ];

    private static $has_one = [
        'SummaryImage' => 'Image',
        'SidebarImage' => 'Image',
    ];

    private static $casting = [
        'MyDefaultSidebarContent' => 'HTMLText',
        'FullWidthContent' => 'HTMLText',
        'SummaryContent' => 'HTMLText',
    ];

    private static $field_labels = [
        'Summary' => 'Page Summary',
        'DefaultSidebarContent' => 'Sidebar content',
        'SummaryImage' => 'Image for Summaries',
        'SidebarImage' => 'Sidebar Image',
    ];

    private static $field_labels_right = [
        'Summary' => 'A summary of the page for use on other pages.',
        'DefaultSidebarContent' => 'The sidebar show up to the right of the main content. It is usually for something like DID YOU KNOW? or CONTACT DETAILS.',
        'SummaryImage' => 'Image used to show a link to this page together with the summary of the page provided.',
        'SidebarImage' => 'Image to show up in the sidebar instead of content.',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fieldLabels = $this->getOwner()->FieldLabels();
        $fieldLabelsRight = Config::inst()->get(SilverstripeColumnsPageExtension::class, 'field_labels_right');
        $tabTitleSummary = _t('SilverstripeColumnsPageExtension.SUMMARY_TAB', 'Summary');
        $tabTitleContent = _t('SilverstripeColumnsPageExtension.ADDITIONAL_CONTENT_TAB', 'MoreContent');
        if ($this->getOwner()->UseSummaries()) {
            $fields->addFieldsToTab(
                'Root.' . $tabTitleSummary,
                [
                    HTMLEditorField::create(
                        'Summary',
                        $fieldLabels['Summary']
                    )->setRows(3)
                        ->setRightTitle($fieldLabelsRight['Summary']),
                    UploadField::create(
                        'SummaryImage',
                        $fieldLabels['SummaryImage']
                    )->setRightTitle($fieldLabelsRight['SummaryImage']),
                ]
            );
        }

        if ($this->getOwner()->UseDefaultSidebarContent()) {
            $fields->addFieldsToTab(
                'Root.' . $tabTitleContent,
                [
                    UploadField::create(
                        'SidebarImage',
                        $fieldLabels['SidebarImage']
                    )->setRightTitle($fieldLabelsRight['SidebarImage']),
                    HTMLEditorField::create(
                        'DefaultSidebarContent',
                        $fieldLabels['DefaultSidebarContent']
                    )->setRightTitle($fieldLabelsRight['DefaultSidebarContent']),
                ]
            );
        }

        return $fields;
    }

    /**
     * @return boolean
     */
    public function UseDefaultSideBarContent()
    {
        if ($this->getOwner()->hasMethod('UseDefaultSideBarContentOverloaded')) {
            $v = $this->getOwner()->UseDefaultSideBarContentOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function UseSummaries()
    {
        if ($this->getOwner()->hasMethod('UseSummariesOverloaded')) {
            $v = $this->getOwner()->UseSummariesOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return false;
    }

    /**
     * @return Image | null
     */
    public function MySidebarImage()
    {
        if ($this->getOwner()->hasMethod('MySidebarImageOverloaded')) {
            $v = $this->getOwner()->MySidebarImageOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        if ($this->getOwner()->SidebarImageID) {
            $image = $this->getOwner()->SidebarImage();
            if ($image && $image->exists()) {
                return $image;
            }
        }

        $parent = $this->getOwner()->Parent();
        if ($parent && $parent->exists() && $parent instanceof SiteTree) {
            return $parent->MySidebarImage();
        }

        return null;
    }

    /**
     * @return string (HTML)
     */
    public function getMyDefaultSidebarContent()
    {
        if ($this->getOwner()->hasMethod('MyDefaultSidebarContentOverloaded')) {
            $v = $this->getOwner()->MyDefaultSidebarContentOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return $this->getOwner()->DefaultSidebarContent;
    }

    /**
     * @return string (HTML)
     */
    public function getFullWidthContent()
    {
        if ($this->getOwner()->hasMethod('FullWidthContentOverloaded')) {
            $v = $this->getOwner()->FullWidthContentOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return $this->getOwner()->RenderWith('FullWidthContent');
    }

    /**
     * @return string (HTML)
     */
    public function getSummaryContent()
    {
        if ($this->getOwner()->hasMethod('SummaryContentOverloaded')) {
            $v = $this->getOwner()->SummaryContentOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        return $this->getOwner()->RenderWith('SummaryContent');
    }

    private static $_children_show_in_menu = [];

    private $showMenuItemsFor;

    public function setShowMenuItemsFor($showMenuItemsFor)
    {
        $showMenuItemsFor = intval($showMenuItemsFor);
        $this->showMenuItemsFor = $showMenuItemsFor;
    }

    public function ChildrenShowInMenu($root = false)
    {
        $key = $this->getOwner()->ID . '_' . ($root ? 'true' : 'false');
        if (! isset(self::$_children_show_in_menu[$key])) {
            if ($this->getOwner()->hasMethod('ChildrenShowInMenuOverloaded')) {
                $v = $this->getOwner()->ChildrenShowInMenuOverloaded();
                if ($v instanceof ArrayList) {
                    self::$_children_show_in_menu[$key] = $v;
                }
            } else {
                if ($root) {
                    $list = Page::get()->filter(['ShowInMenus' => 1, 'ParentID' => 0]);
                    foreach ($list as $page) {
                        if (! $page->canView()) {
                            $list = $list->exclude(['ID' => $page->ID]);
                        }
                    }
                } else {
                    $list = $this->getOwner()->Children();
                    foreach ($list as $page) {
                        if (! $page->ShowInMenus) {
                            $list = $list->exclude(['ID' => $page->ID]);
                        }
                    }
                }

                self::$_children_show_in_menu[$key] = $list;
            }
        }

        return self::$_children_show_in_menu[$key];
    }

    public function MyMenuItems()
    {
        if ($this->getOwner()->hasMethod('MyMenuItemsOverloaded')) {
            $v = $this->getOwner()->MyMenuItemsOverloaded();
            if ($v !== null) {
                return $v;
            }
        }

        //first stop: children ...
        $parent = $this->getOwner();
        $dataSet = false;
        if ($this->showMenuItemsFor !== null) {
            if ($this->showMenuItemsFor) {
                $page = Page::get()->byID($this->showMenuItemsFor);
                $dataSet = $page->ChildrenShowInMenu();
            } else {
                $dataSet = $this->ChildrenShowInMenu(true);
            }
        } else {
            $isHomePage = $this->getOwner()->URLSegment === Config::inst()->get('RootURLController', 'default_homepage_link');
            while ($parent && $dataSet === false) {
                $dataSet = $parent->ChildrenShowInMenu($isHomePage);
                if ($dataSet->count() === 0) {
                    $dataSet = false;
                }

                if ($dataSet === false) {
                    $parent = Page::get()->byID($parent->ParentID);
                }
            }

            if ($dataSet === false) {
                $dataSet = $this->ChildrenShowInMenu(true);
            }
        }

        return $dataSet;
    }

    public function MyMenuItemsParentPage()
    {
        $children = $this->MyMenuItems();
        if ($children && $child = $children->first()) {
            $page = Page::get()->byID($child->ParentID);
            if ($page && $page->ShowInMenus && $page->canView()) {
                return $page;
            }
        }
    }

    public function MyMenuItemsParentLink()
    {
        $parent = $this->MyMenuItemsParentPage();
        if ($parent) {
            return $parent->MyMenuItemsMenuLink($parent->ParentID);
        }

        return $this->MyMenuItemsMenuLink(0);
    }

    public function MyMenuItemsMenuLink($id = null)
    {
        if ($id === null) {
            $id = $this->getOwner()->ID;
        }

        return Controller::curr()->Link() . 'myspecificpagemenuitems/' . $id . '/';
    }
}
