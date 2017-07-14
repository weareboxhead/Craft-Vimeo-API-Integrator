<?php

namespace Craft;

class VimeoApiPlugin extends BasePlugin
{
    protected $sectionHandle = 'vimeoVideos';

    public function getName()
    {
        return Craft::t('Vimeo API');
    }

    public function getVersion()
    {
        return '0.1 Beta';
    }

    public function getDeveloper()
    {
        return 'Boxhead';
    }

    public function getDeveloperUrl()
    {
        return 'http://boxhead.io';
    }

    public function onAfterInstall()
    {
        $this->generateCMS();
    }

    /**
     * Parents the CMS generation process.
     *
     * @return void
     */
    private function generateCMS()
    {
        $section = $this->createSection();

        $fieldGroup = $this->createFieldGroup();

        if (!$fieldGroup) {
            return false;
        }

        $fields = $this->createFields($fieldGroup->id);

        $fieldsIds = array_map(function($field) {
            return $field->id;
        }, $fields);

        $entryLayout = $this->createEntryLayout($fieldsIds);

        $this->createEntryType($section, $entryLayout);
    }

    /**
     * Parents the CMS generation process.
     *
     * @return section object
     */
    private function createSection()
    {
        Craft::log('Creating the Vimeo Section.');

        $section = new SectionModel();

        $section->name      = 'Vimeo Videos';
        $section->handle    = $this->sectionHandle;
        $section->type      = SectionType::Channel;
        $section->hasUrls   = false;

        $primaryLocaleId = craft()->i18n->getPrimarySiteLocaleId();

        $locales[$primaryLocaleId] = new SectionLocaleModel(array(
            'locale' => $primaryLocaleId,
        ));

        $section->setLocales($locales);

        if (craft()->sections->saveSection($section)) {
            Craft::log('Vimeo Section created successfully.');

            return $section;
        } else {
            Craft::log('Could not create the Vimeo Section.', LogLevel::Error);

            return false;
        }
    }

    /**
     * Creates the field group.
     *
     * @return fieldGroup object
     */
    private function createFieldGroup()
    {
        // Create the Vimeo Field Group
        Craft::log('Creating the Vimeo Field Group.');

        $fieldGroup = new FieldGroupModel();
        $fieldGroup->name = 'Vimeo';

        if (craft()->fields->saveGroup($fieldGroup)) {
            Craft::log('Vimeo Field Group created successfully.');

            return $fieldGroup;
        } else {
            Craft::log('Could not create Vimeo Field Group.', LogLevel::Error);

            return false;
        }
    }

    /**
     * Creates the fields.
     *
     * @return fields array
     */
    private function createFields($fieldGroupId)
    {
        // Create the Fields
        Craft::log('Creating the Vimeo Fields.');

        $fields = array();

        foreach ($this->getFieldConfigs($fieldGroupId) as $fieldConfig) {
            $field = $this->createField($fieldConfig);

            if (craft()->fields->saveField($field)) {
                Craft::log($field->name . ' field created successfully.');

                $fields[] = $field;
            } else {
                Craft::log('Could not save the ' . $field->name . ' field.', LogLevel::Error);
            }
        }

        return $fields;
    }

    /**
     * Returns the field configs.
     *
     * @return array
     */
    private function getFieldConfigs($fieldGroupId)
    {
        $configs = array(
            array(
                'name'      => 'Vimeo Video Id',
                'handle'    => 'vimeoVideoId',
                'type'      => 'PlainText',
            ),

            array(
                'name'      => 'Vimeo Video Files',
                'handle'    => 'vimeoVideoFiles',
                'type'      => 'Table',
                'instructions'  => 'Automatically populated on save if Vimeo Video Id is filled in',

                'settings'  => array(
                    'columns'   => array(
                        'col1'      => array(
                            'heading'   => 'Width',
                            'handle'    => 'width',
                            'width'     => '7%',
                            'type'      => 'singleline',
                        ),

                        'col2'      => array(
                            'heading'   => 'Height',
                            'handle'    => 'height',
                            'width'     => '7%',
                            'type'      => 'singleline',
                        ),

                        'col3'      => array(
                            'heading'   => 'Quality',
                            'handle'    => 'quality',
                            'width'     => '7%',
                            'type'      => 'singleline',
                        ),

                        'col4'      => array(
                            'heading'   => 'Url',
                            'handle'    => 'url',
                            'width'     => '79%',
                            'type'      => 'singleline',
                        )
                    )
                )
            )
        );

        foreach ($configs as &$config) {
            $config['groupId'] = $fieldGroupId;
        }

        return $configs;
    }

    /**
     * Creates a field.
     *
     * @return field object
     */
    private function createField($fieldConfig)
    {
        Craft::log('Creating the ' . $fieldConfig['name'] . ' field.');

        $field = new FieldModel();

        $field->groupId         = $fieldConfig['groupId'];
        $field->name            = $fieldConfig['name'];
        $field->handle          = $fieldConfig['handle'];
        $field->instructions    = isset($fieldConfig['instructions']) ? $fieldConfig['instructions'] : null;
        $field->type            = $fieldConfig['type'];

        if (isset($fieldConfig['settings'])) {
            $field->settings = $fieldConfig['settings'];
        }

        return $field;
    }

    /**
     * Creates the entry layout.
     *
     * @return layout object
     */
    private function createEntryLayout($fieldIds)
    {
        Craft::log('Creating the Vimeo Video Entry Layout.');

        if ($layout = craft()->fields->assembleLayout(array('Vimeo Video' => $fieldIds))) {
            Craft::log('Vimeo Video Entry Layout created successfully.');
        } else {
            Craft::log('Could not create the Vimeo Video Entry Layout', LogLevel::Error);

            return false;
        }    

        // Set the layout type to an Entry
        $layout->type = ElementType::Entry;

        return $layout;
    }

    /**
     * Creates the entry type.
     *
     * @return void
     */
    private function createEntryType($section, $entryLayout)
    {
        // There will only entry type for our new section so get that
        $entryType = $section->getEntryTypes()[0];

        $entryType->hasTitleField   = true;
        $entryType->titleLabel      = 'Title';
        $entryType->setFieldLayout($entryLayout);

        if (craft()->sections->saveEntryType($entryType)) {
            Craft::log('Vimeo Section Entry Type saved successfully.');
        } else {
            Craft::log('Could not create the Vimeo Section Entry Type.', LogLevel::Error);
        }
    }

    public function init()
    {
        // Before saving an entry
        craft()->on('entries.onBeforeSaveEntry', function(Event $event) {
            // Check we're in the right section
            $entry = $event->params['entry'];

            if ($entry->section->handle !== $this->sectionHandle) {
                return;
            }

            // Populate the video files
            craft()->vimeoApi_videos->populateVideoFiles($entry);
        });
    }

    protected function defineSettings()
    {
        return array(
            'clientId'          => array(AttributeType::String, 'default' => ''),
            'clientSecret'      => array(AttributeType::String, 'default' => ''),
            'accessToken'       => array(AttributeType::String, 'default' => ''),
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('vimeoapi/settings', array(
            'settings' => $this->getSettings()
        ));
    }
}