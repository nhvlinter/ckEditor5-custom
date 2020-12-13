# ckEditor
ckEditor adapter for custom use



## Description 
Create <a href="https://ckeditor.com/ckeditor-5/" target="_blank" >ckEditor adapter</a> for editing html content from a webpage and submit it through ajax. <br>
The adapter must :
<ul>
  <li>
    <a href="https://ckeditor.com/docs/ckeditor5/latest/framework/guides/deep-dive/conversion/conversion-introduction.html" target="_blank">Implement conversion</a>
  </li>
  <li>
    <a href="https://ckeditor.com/docs/ckeditor5/latest/framework/guides/deep-dive/conversion/conversion-preserving-custom-content.html" 
       target="_blank">
      Preserving custom content
    </a>
  </li>
  <li>
    <a href="https://ckeditor.com/docs/ckeditor5/latest/framework/guides/deep-dive/conversion/custom-element-conversion.html" 
       target="_blank">
      Implement Custom element conversion
    </a>
  </li>
  <li><a href="https://ckeditor.com/docs/ckeditor5/latest/framework/guides/deep-dive/conversion/conversion-preserving-custom-content.html">Custom image conversion</a></li>
</ul>

The purpose of this adapter is to preserve the layout, design, classes, attribute,... of every <TAG> (DOM element) when start editing. <br>
When start editing a page or section, the whole block must keep their initial attribute. <br>
Also the adaptor must implement image convertion. <br>


## Usage

```javascript
import modalFormify

$(DOM.selector).customCkEditor(options); # returns customCkEditor api
```

## Requirement 

<ul>
  <li>jquery 3.5.< </li>
  <li>js 2015 </li>
  <li>CkEditor 5 </li>
</ul>


## Todo
Provide Public Access to Default Plugin Settings
```javascript
// Plugin definition.

$.fn.customCkEditor= function( options ) {    
    // Extend our default options with those provided.    
    // Note that the first argument to extend is an empty    
    // object – this is to keep from overriding our "defaults" object.
     var _settings = $.extend( {}, $.fn.customCkEditor.defaults.option, options );  
    // Our plugin implementation code goes here. 

}; 

// Plugin defaults – added as a property on our plugin function.

var default = $.fn.customCkEditor.defaults = {
    option : {
      
    }
};
```
Define private variable for the plugin's use <br>
exemple : (list all private variable for the api and implement it, this is just an example)
```javascript
// private property must start with _ (underscore)
$.fn.customCkEditor= function( options ) {    

    var that = this;

}; 

```

Determine the default object property and value for the use of our plugin <br>
example :
```javascript
var pluginDefOption = default.option = {
    // file path to the ajax url
    ajax: "/path/to/ajax-api"
    // or object
    "ajax": {
        "url": "data.json",
        "type": "POST",
        "data": function ( d ) {
            return $.extend( {}, d, {
                "extra_search": $('#extra').val()
            } );
        }
    }
};
```


determine and implement Callback <br>
exemple : (list all event accessible for the api and implement it, this is just an example)
```javascript
$.fn.customCkEditor.defaults = {
    event : {

        init: function (customCkEditor) {
            if (customCkEditor.event.debug) {
                console.log('init(customCkEditor) called', {customCkEditor: customCkEditor});
            }

        },
        preInit: function (customCkEditor) {
            if (customCkEditor.event.debug) {
                console.log('preInit(customCkEditor) called', {customCkEditor: customCkEditor});
            }
            return true;
        },
        onInit: function (modalFormify) {
            if (customCkEditor.event.debug) {
                console.log('onInit(customCkEditor) called', {customCkEditor: customCkEditor});
            }

        },
        postInit: function (modalFormify) {
            if (customCkEditor.event.debug) {
                console.log('postInit(customCkEditor) called', {customCkEditor: customCkEditor});
            }
        },
        initDraw: function (customCkEditor) {
            if (customCkEditor.event.debug) {
                console.log('initDraw(customCkEditor) called', {customCkEditor: customCkEditor});
            }

        },
        preDraw: function (customCkEditor) {
            if (modalFormify.event.debug) {
                console.log('preDraw(modalFormify) called', {modalFormify: modalFormify});
            }
            return true;

        },
        onDraw: function (customCkEditor) {
            if (customCkEditor.event.debug) {
                console.log('onDraw(customCkEditor) called', {customCkEditor: customCkEditor});
            }

        },
        postDraw: function (customCkEditor) {
            if (customCkEditor.event.debug) {
                console.log('postDraw(customCkEditor) called', {customCkEditor: customCkEditor});
            }

        }
    }
};
```

Provide Public Access to Default Plugin functions (api functions)<br>
exemple : (list all public function for the api and implement it, this is just an example)
```javascript
// Plugin definition.

$.fn.customCkEditor= function( options ) {    
    var that = this;
    
    that.data = function (){
        return _data; // Array(object)
    };
    
    that.content = function(){
        // return OUTERhtml of the DOM 
    };

}; 

```
