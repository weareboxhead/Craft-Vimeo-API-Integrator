<?php

namespace Craft;

class VimeoApiVariable
{
    public function getHighestQualityVideo($entry = null)
    {
        return craft()->vimeoApi_videos->getHighestQualityVideo($entry);
    }

    public function getLowestQualityVideo($entry = null)
    {
        return craft()->vimeoApi_videos->getLowestQualityVideo($entry);
    }

    public function getMinWidthVideo($entry = null, $minWidth = 0)
    {
        return craft()->vimeoApi_videos->getMinWidthVideo($entry, $minWidth);
    }

    public function getMaxWidthVideo($entry = null, $maxWidth = 0)
    {
        return craft()->vimeoApi_videos->getMaxWidthVideo($entry, $maxWidth);
    }
}