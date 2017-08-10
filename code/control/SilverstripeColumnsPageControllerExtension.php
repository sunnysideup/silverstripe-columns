<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SilverstripeColumnsPageControllerExtension extends Extension
{

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
        if($this->owner->getFullWidthContent()) {
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
                strlen($this->getMyDefaultSidebarContent()) > 17
            )
            ||
            $this->MySidebarImage()
        ) {
            return true;
        }
    }


    /**
     * @return bool
     */
    function HasSecondColumn()
    {
        if($this->owner->hasMethod('HasSecondColumnOverloaded')) {
            $v = $this->owner->HasSecondColumnOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        if(
            (
                $this->owner->UseSecondColumn() &&
                strlen($this->getMySecondColumnContent()) > 17
            )
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
        if($this->HasSideBar()) {
            $count++;
        }
        if($this->HasSecondColumn()) {
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
        return Page::get()->filter(array('ParentID' => $this->ID, 'ShowInMenus' => 1));
    }

    /**
     * Siblings
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
        if($this->ParentID) {
            return Page::get()
                ->filter(array('ParentID' => $this->ParentID, 'ShowInMenus' => 1))
                ->exclude(array('ID' => $this->ID));
        }
    }

}
