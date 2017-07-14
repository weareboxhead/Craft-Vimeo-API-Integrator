# Vimeo API Integrator - Plugin for Craft CMS

Vimeo API Integrator automatically retrieves and populates data for different quality versions of a given Vimeo Video, and provides a series of template helpers to retrieve the relevant data.

## Prerequisites

* All videos used must belong to the account for which the Client Id and Client Secret (and therefore access token) are affiliated.
* That account must be at least a Vimeo Pro account (this is because Vimeo's 'files' data is only available to accounts which are at least Vimeo Pro)

## Usage

* Download and extract the plugin files
* Copy `vimeoapi/` to your site's `/craft/plugins/` directory
* Install the plugin & fill out the required settings.
* Create a new entry in the `Vimeo Videos` channel (automatically created on plugin install), add the video's id to the `Vimeo Video Id` field, and click save!
* Re-open the entry to check the `Vimeo Video Files` table has been correctly populated.
* (Optional) Create an `entries` field for sections which require a Vimeo video, which will pull through the video(s) from the master `Vimeo Videos` section. `{% cache %}` this relationship of couse :)

## Template Variables

To make it easier to access the video files relevant to you, this plugin provides a few template variables for you to use:

### craft.vimeoApi.getHighestQualityVideo(entry)

Returns the file of the highest quality video

``` twig
{% set file = craft.vimeoApi.getHighestQualityVideo(entry) %}

{% if file %}
	<video width="{{ file.width }}" height="{{ file.height }}">
		<source src="{{ file.url }}">
	</video>
{% endif %}
```

### craft.vimeoApi.getLowestQualityVideo(entry)

Returns the file of the lowest quality video

``` twig
{% set file = craft.vimeoApi.getLowestQualityVideo(entry) %}

{% if file %}
	<video width="{{ file.width }}" height="{{ file.height }}">
		<source src="{{ file.url }}">
	</video>
{% endif %}
```

### craft.vimeoApi.getMinWidthVideo(entry, width)

Returns the file of which the width is at least the given width

``` twig
{% set file = craft.vimeoApi.getMinWidthVideo(entry, 1080) %}

{% if file %}
	<video width="{{ file.width }}" height="{{ file.height }}">
		<source src="{{ file.url }}">
	</video>
{% endif %}
```

### craft.vimeoApi.getMaxWidthVideo(entry, width)

Returns the file of which the width is at most the given width

``` twig
{% set file = craft.vimeoApi.getMaxWidthVideo(entry, 1080) %}

{% if file %}
	<video width="{{ file.width }}" height="{{ file.height }}">
		<source src="{{ file.url }}">
	</video>
{% endif %}
```