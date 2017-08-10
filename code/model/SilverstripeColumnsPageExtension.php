<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SilverstripeColumnsPageExtension extends DataExtension
{
    private static $db = [
        'Summary' => 'HTMLVarchar(255)',
        'SecondColumn' => 'HTMLText',
        'DefaultSidebarContent' => 'HTMLText'
    ];

    private static $has_one = [
        'SummaryImage' => 'Image',
        'SidebarImage' => 'Image'
    ];

    private static $casting = [
        'MyDefaultSidebarContent' => 'HTMLText',
        'MySecondColumnContent' => 'HTMLText',
        'FullWidthContent' => 'HTMLText',
        'SummaryContent' => 'HTMLText'
    ];

    private static $field_labels = [
        'Summary' => 'Page Summary',
        'SecondColumn' => 'Second content section',
        'DefaultSidebarContent' => 'Sidebar content',
        'SummaryImage' => 'Image for Summaries',
        'SidebarImage' => 'Sidebar Image'
    ];

    private static $field_labels_right = [
        'Summary' => 'A summary of the page for use on other pages.',
        'SecondColumn' => 'The second column is basically an extension of the primary content field',
        'DefaultSidebarContent' => 'The sidebar show up to the right of the main content. It is usually for something like DID YOU KNOW? or CONTACT DETAILS.',
        'SummaryImage' => 'Image used to show a link to this page together with the summary of the page provided.',
        'SidebarImage' => 'Image to show up in the sidebar instead of content.'
    ];

    private static $page_types_that_use_the_default_sidebar = [];

    private static $page_types_that_use_the_second_column = [];

    public function updateCMSFields(FieldList $fields)
    {
        $fieldLabels = $this->owner->FieldLabels();
        $fieldLabelsRight = Config::inst()->get('SilverstripeColumnsPageExtension', 'field_labels_right');
        $tabTitleSummary = _t('SilverstripeColumnsPageExtension.SUMMARY_TAB', 'Summary');
        $tabTitleContent = _t('SilverstripeColumnsPageExtension.ADDITIONAL_CONTENT_TAB', 'MoreContent');
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
                )->setRightTitle($fieldLabelsRight['SummaryImage'])
            ]
        );
        if($this->owner->UseSecondColumn()) {
            $fields->addFieldsToTab(
                'Root.' . $tabTitleContent,
                [
                    HTMLEditorField::create(
                        'SecondColumn',
                        $fieldLabels['SecondColumn']
                    )->setRows(20)
                    ->setRightTitle($fieldLabelsRight['SecondColumn'])
                ]
            );
        }
        if($this->owner->UseDefaultSidebarContent()) {
            $fields->addFieldsToTab(
                'Root.' . $tabTitleContent,
                [
                    HTMLEditorField::create(
                        'DefaultSidebarContent',
                        $fieldLabels['DefaultSidebarContent']
                    )->setRightTitle($fieldLabelsRight['DefaultSidebarContent']),
                    UploadField::create(
                        'SidebarImage',
                        $fieldLabels['SidebarImage']
                    )->setRightTitle($fieldLabelsRight['SidebarImage'])
                ]
            );
        }

        return $fields;
    }



    /**
     * @return boolean
     */
    function UseDefaultSideBarContent()
    {
        if($this->owner->hasMethod('UseDefaultSideBarContentOverloaded')) {
            $v = $this->owner->UseDefaultSideBarContentOverloaded();
            if($v !== null) {
                return $v;
            }
        }

        $testArray = Config::inst()->get('SilverstripeColumnsPageExtension', 'page_types_that_use_the_default_sidebar');
        if(count($testArray) === 0) {

            return true;
        } else {
            if(in_array($this->owner->ClassName, $testArray)) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @return boolean
     */
    function UseSecondColumn()
    {
        if($this->owner->hasMethod('UseSecondColumnOverloaded')) {
            $v = $this->owner->UseSecondColumnOverloaded();
            if($v !== null) {
                return $v;
            }
        }

        $testArray = Config::inst()->get('SilverstripeColumnsPageExtension','page_types_that_use_the_second_column');
        if(count($testArray) === 0) {
            return true;
        } else {
            if(in_array($this->owner->ClassName, $testArray)) {
                return true;
            }
            return false;
        }
    }

    /**
     * @return Image | null
     */
    function MySidebarImage()
    {
        if($this->owner->hasMethod('MySidebarImageOverloaded')) {
            $v = $this->owner->MySidebarImageOverloaded();
            if($v !== null) {
                return $v;
            }
        }

        if($this->owner->SidebarImageID) {
            $image = $this->owner->SidebarImage();
            if($image && $image->exists()) {
                return $image;
            }
        }
        $parent = $this->owner->Parent();
        if($parent && $parent->exists() && $parent instanceof SiteTree) {
            return $parent->MySidebarImage();
        }

        return null;
    }

    /**
     *
     * @return string (HTML)
     */
    function getMyDefaultSidebarContent()
    {
        if($this->owner->hasMethod('MyDefaultSidebarContentOverloaded')) {
            $v = $this->owner->MyDefaultSidebarContentOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        return $this->owner->DefaultSidebarContent;
    }

    /**
     *
     * @return string (HTML)
     */
    function getMySecondColumnContent()
    {
        if($this->owner->hasMethod('MySecondColumnContentOverloaded')) {
            $v = $this->owner->MySecondColumnContentOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        return $this->owner->SecondColumn;
    }

    /**
     *
     * @return string (HTML)
     */
    function getFullWidthContent()
    {
        if($this->owner->hasMethod('FullWidthContentOverloaded')) {
            $v = $this->owner->FullWidthContentOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        return $this->owner->renderWith('FullWidthContent');
    }

    /**
     *
     * @return string (HTML)
     */
    function getSummaryContent()
    {
        if($this->owner->hasMethod('SummaryContentOverloaded')) {
            $v = $this->owner->SummaryContentOverloaded();
            if($v !== null) {
                return $v;
            }
        }
        return $this->owner->renderWith('SummaryContent');
    }

}
