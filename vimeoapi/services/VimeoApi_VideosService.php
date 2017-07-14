<?php

namespace Craft;

class VimeoApi_VideosService extends BaseApplicationComponent
{
    /**
     * Populates the entry's video files field
     *
     * @return void
     */
    public function populateVideoFiles($entry)
    {
        // If a vimeo video id is given
        if ($entry->vimeoVideoId) {
            // Populate the video files field
            $entry->getContent()->vimeoVideoFiles = $this->getVideoFiles($entry->vimeoVideoId);
        }
    }

    /**
     * Handles the request to Vimeo HQ for the video files, and formats the return data
     *
     * @return array
     */
    protected function getVideoFiles($videoId)
    {
        // Make the request to Vimeo HQ
        $videoFiles = craft()->vimeoApi_connection->getVideoFiles($videoId);

        $widths = array();

        foreach ($videoFiles as $key => $file) {
            // Check this is a file we want to handle
            if (!isset($file['width'])) {
                unset($videoFiles[$key]);

                continue;
            }

            // Add this width to our aray
            $widths[$key] = $file['width'];
        }

        // Sort the video files by width
        array_multisort($widths, SORT_ASC, $videoFiles);

        // Return a filtered array with just the information we need
        return array_map(function($videoFile) {
            return array(
                'col1' => $videoFile['width'],
                'col2' => $videoFile['height'],
                'col3' => $videoFile['quality'],
                'col4' => $videoFile['link_secure'],
            );
        }, $videoFiles);
    }

    /**
     * Gets the highest quality video
     *
     * @return array
     */
    public function getHighestQualityVideo($entry)
    {
        $files = $this->getEntryVideoFiles($entry);

        if (empty($files)) {
            return $this->noFileFound();
        }

        return end($files);
    }

    /**
     * Gets the lowest quality video
     *
     * @return array
     */
    public function getLowestQualityVideo($entry)
    {
        $files = $this->getEntryVideoFiles($entry);

        if (empty($files)) {
            return $this->noFileFound();
        }

        return reset($files);
    }

    /**
     * Gets a video which is at least []px wide
     *
     * @return array
     */
    public function getMinWidthVideo($entry, $minWidth)
    {
        $files = $this->getEntryVideoFiles($entry);

        if (empty($files)) {
            return $this->noFileFound();
        }

        foreach ($files as $file) {
            if ($file['width'] >= $minWidth) {
                return $file;
            }
        }

        return $this->noFileFound();
    }

    /**
     * Gets a video which is at most []px wide
     *
     * @return array
     */
    public function getMaxWidthVideo($entry, $maxWidth)
    {
        $files = $this->getEntryVideoFiles($entry);

        if (empty($files)) {
            return $this->noFileFound();
        }

        foreach (array_reverse($files) as $file) {
            if ($file['width'] <= $maxWidth) {
                return $file;
            }
        }

        return $this->noFileFound();
    }

    /**
     * Returns the Vimeo Video Files field content if set
     *
     * @return Vimeo Video Files
     */
    protected function getEntryVideoFiles($entry) {
        if (!$entry) {
            return $this->noFileFound();
        }

        return $entry->vimeoVideoFiles;
    }

    protected function noFileFound()
    {
        return null;
    }
}