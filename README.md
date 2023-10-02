# Only-a-simple-WebP-Converter
Convert JPG and PNG to WEBP


Yes, there are many plugins that convert images to WebP and embed them in different ways.

My point was to keep the maintenance of our websites as low as possible. That's why I started to replace used plugins with my own solutions. In most cases I don't need all the features of an external plugin and besides every plugin is a possible security risk.

That's why I create my own plugins that do only what I need. According to the "activate and forget principle".

Since all browsers understand WebP by now, I don't need fallbacks or anything like that.

To keep the loading times of the Wordpress page as low as possible, I also have Alpine JS in use now instead of jQuery.

This converter plugin therefore also only works with Alpine JS.

The plugin is divided into 3 components:

1) Conversion of existing graphics.
2) Update of the filename in the database.
3) Conversion after uploading new graphics.

The first component is needed only once and can be deactivated afterwards.

The other two components are permanently active. The filename update could also be run as a one-time function through all DB entries.

I do not use the plugin as a plugin in the usual sense that a ZIP file is uploaded. I have WPCodeBox2 in use and entered and activated the components there.

If someone wants to play around with it it is ok - support I do not take. Everything is at your own risk.


LINKS:
https://alpinejs.dev/
[wpcodebox](https://wpcodebox.com/)https://wpcodebox.com/
