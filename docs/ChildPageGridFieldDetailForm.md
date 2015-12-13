ChildPageGridFieldDetailForm
============================

This provides a grid field detail form you can use to edit pages

This example below requires the exclude Children module.

````php
class NewsHolderPage extends Page {

    private static $can_be_root = true;
    private static $allowed_children = array(
        'NewsPage',
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        'NewsPage'
    );

    public function NewsArticles() {
        return DataList::create('NewsPage')->filter(array('ParentID' => $this->ID))->sort('Created DESC');
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        // load up the widget mananger in the main tab
        $gridField = new GridField("Children", "News Articles", $this->NewsArticles(), GridFieldConfig_RelationEditor::create());
        $gridField  ->getConfig()
                    ->removeComponentsByType('GridFieldDetailForm')
                    ->addComponent(new ChildPageGridFieldDetailForm)
                    ->getComponentByType('GridFieldDataColumns')
                    ->setDisplayFields(array(
                        'Title'                   => 'Title',
                        'Link'                    => 'Link',
                        'LastEditedDateSelector'  => 'Released'
                    ));
        $fields->addFieldToTab('Root.NewsArticles', $gridField);

        return $fields;

    }
}
````
