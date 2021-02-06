# migxquasar

this is a simple example, how you could edit MODX resources including contents of MIGX TVs from frontend with a vuejs/quasar - app

at this stage, only text and textarea - fields work out of the box.

after installing the package, tweak it to your needs

Create a resource with the migxquasar2 - template, or better, with a copy of that template

The resourcelist - chunk, which resources, you want to edit
https://github.com/Bruno17/migxquasar/blob/main/core/components/migxquasar/elements/chunks/migxquasar_resourcelist.chunk.html

The tvlist - chunk, which tvs you want to edit
https://github.com/Bruno17/migxquasar/blob/main/core/components/migxquasar/elements/chunks/migxquasar_tvlist.chunk.html

within the app, modify the fields for each tab
https://github.com/Bruno17/migxquasar/blob/main/core/components/migxquasar/elements/chunks/migxquasar_app.chunk.html#L75-L106

within the template, modify the tabs and its contents to your needs
https://github.com/Bruno17/migxquasar/blob/main/core/components/migxquasar/elements/templates/migxquasar2.template.html#L76-L105

editor needs to be logged in and has permission to edit resources

