<?php

namespace Craft;

use \Vimeo\Vimeo;

class VimeoApi_ConnectionService extends BaseApplicationComponent
{
    private $lib;

    /**
     * Initializes the connection service.
     *
     * @return void
     */
    public function __construct()
    {
        require_once craft()->path->getPluginsPath() . 'vimeoapi/vendor/autoload.php';

        $settings = craft()->plugins->getPlugin('vimeoapi')->getSettings();

        $clientId = $settings->clientId;
        $clientSecret = $settings->clientSecret;
        $accessToken = $settings->accessToken;

        if (!($clientId && $clientSecret && $accessToken)) {
            Craft::log('Missing required settings to make connection.', LogLevel::Error);

            return;
        }

        $this->lib = new Vimeo($clientId, $clientSecret);

        $this->lib->setToken($accessToken);
    }

    /**
     * Gets a specified video by id
     *
     * @return array
     */
    public function getVideoById($videoId, $jsonKeys)
    {
        if (!$this->lib) {
            return false;
        }

        $response = $this->lib->request('/videos/' . $videoId . $this->buildJsonFilter($jsonKeys));

        if (!$this->isValidResponse($response)) {
            return false;
        }

        return $response['body'];
    }

    /**
     * Gets the files for a specified video
     *
     * @return void
     */
    public function getVideoFiles($videoId)
    {
        return $this->getVideoById($videoId, array('files'))['files'];
    }

    /**
     * Builds the Vimeo JSON filter in the required format
     *
     * @return string
     */
    protected function buildJsonFilter($filters)
    {
        if (!$filters) {
            return '';
        }

        return '?fields=' . implode(',', $filters);
    }

    /**
     * Determines whether this response is valid, and handles accordingly if not
     *
     * @return bool
     */
    protected function isValidResponse($response)
    {
        // If the call failed, or there is no body, don't go on
        if ($response['status'] !== 200) {
            $errorMessage = 'Invalid response from Vimeo. Status given: ' . $response['status'] . '.';

            if (!empty($response['body']['error'])) {
                $errorMessage .= ' Error message given: ' . $response['body']['error'] . '.';
            }

            Craft::log($errorMessage, LogLevel::Error);

            return false;
        }

        return true;
    }
}