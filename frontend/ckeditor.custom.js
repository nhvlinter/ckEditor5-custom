(function( $ ) {
$.fn.customCkEditor= function( options ) {
    var _settings = $.extend( {}, $.fn.customCkEditor.defaults.option, options );
    function convertDiv(editor) {
        let debug = false;
        if(debug){console.log("convertDiv")}

        this.editor = editor;
        this.init = function () {
            if(debug){console.log("convertDiv.init")}
            let thisEditor = this.editor;
            thisEditor.model.schema.register( 'div', {
                allowWhere: '$text',
                allowContentOf: '$block'
            } );

        };

        this.afterInit = function () {
            if(debug){console.log("convertDiv.afterInit")}
            let thisEditor = this.editor;

            thisEditor.model.schema.addAttributeCheck( context => {
                if(debug){console.log("convertDiv.addAttributeCheck", {
                    context : context
                })}
                if ( context.endsWith( 'div' ) ) {
                    if(debug){console.log("convertDiv.addAttributeCheck.endsWith( 'div' )")}
                    return true;
                }
            } );
            // The view-to-model converter converting a view <div> with all its attributes to the model.
            thisEditor.conversion.for( 'upcast' ).elementToElement( {
                view: 'div',
                model: ( viewElement, { writer: modelWriter } ) => {
                    if(debug){console.log("convertDiv", {
                        viewElement : viewElement
                    })}
                    return modelWriter.createElement( 'div', viewElement.getAttributes() );
                }
            } );

            // The model-to-view converter for the <div> element (attributes are converted separately).
            thisEditor.conversion.for( 'downcast' ).elementToElement( {
                model: 'div',
                view: 'div'
            } );

            // The model-to-view converter for <div> attributes.
            // Note that a lower-level, event-based API is used here.
            thisEditor.conversion.for( 'downcast' ).add( dispatcher => {
                if(debug){console.log("convertDiv.conversion.downcast")}
                dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
                    if(debug){console.log("convertDiv.conversion.downcast.dispatcher:attribute", {
                        evt : evt, data : data, conversionApi : conversionApi
                    })}
                    // Convert <div> attributes only.
                    if ( data.item.name !== 'div' ) {
                        return;
                    }

                    const viewWriter = conversionApi.writer;
                    const view = conversionApi.mapper.toViewElement( data.item );
                    if(debug){console.log("convertDiv.conversion.downcast.dispatcher:attribute", {
                        viewWriter : viewWriter, view : view
                    })}

                    // In the model-to-view conversion we convert changes.
                    // An attribute can be added or removed or changed.
                    // The below code handles all 3 cases.
                    if ( data.attributeNewValue ) {
                        viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, view );
                    } else {
                        viewWriter.removeAttribute( data.attributeKey, view );
                    }
                } );
            } );
        };
    }
    function convertSpan(editor) {
        let debug = false;
        if(debug){console.log("convertSpan")}

        this.editor = editor;
        this.init = function () {
            if(debug){console.log("convertSpan.init")}
            let thisEditor = this.editor;
            thisEditor.model.schema.register( 'span', {
                allowWhere: '$text',
                allowContentOf: '$block'
            } );

        };

        this.afterInit = function () {
            if(debug){console.log("convertSpan.afterInit")}
            let thisEditor = this.editor;

            thisEditor.model.schema.addAttributeCheck( context => {
                if(debug){console.log("convertSpan.addAttributeCheck", {
                    context : context
                })}
                if ( context.endsWith( 'span' ) ) {
                    if(debug){console.log("convertSpan.addAttributeCheck.endsWith( 'span' )")}
                    return true;
                }
            } );
            // The view-to-model converter converting a view <div> with all its attributes to the model.
            thisEditor.conversion.for( 'upcast' ).elementToElement( {
                view: 'span',
                model: ( viewElement, { writer: modelWriter } ) => {
                    if(debug){console.log("convertSpan", {
                        viewElement : viewElement
                    })}
                    return modelWriter.createElement( 'span', viewElement.getAttributes() );
                }
            } );

            // The model-to-view converter for the <div> element (attributes are converted separately).
            thisEditor.conversion.for( 'downcast' ).elementToElement( {
                model: 'span',
                view: 'span'
            } );

            // The model-to-view converter for <div> attributes.
            // Note that a lower-level, event-based API is used here.
            thisEditor.conversion.for( 'downcast' ).add( dispatcher => {
                if(debug){console.log("convertSpan.conversion.downcast")}
                dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
                    if(debug){console.log("convertSpan.conversion.downcast.dispatcher:attribute", {
                        evt : evt, data : data, conversionApi : conversionApi
                    })}
                    // Convert <a> attributes only.
                    if ( data.item.name !== 'span' ) {
                        return;
                    }

                    const viewWriter = conversionApi.writer;
                    const view = conversionApi.mapper.toViewElement( data.item );
                    if(debug){console.log("convertSpan.conversion.downcast.dispatcher:attribute", {
                        viewWriter : viewWriter, view : view
                    })}

                    // In the model-to-view conversion we convert changes.
                    // An attribute can be added or removed or changed.
                    // The below code handles all 3 cases.
                    if ( data.attributeNewValue ) {
                        viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, view );
                    } else {
                        viewWriter.removeAttribute( data.attributeKey, view );
                    }
                } );
            } );
        };
    }
    function convertAnchor(editor) {
        let debug = true;
        if(debug){console.log("convertAnchor")}

        this.editor = editor;
        this.init = function () {
            if(debug){console.log("convertAnchor.init")}
            let thisEditor = this.editor;
            thisEditor.model.schema.register( 'a', {
                allowWhere: '$text',
                allowContentOf: '$block'
            } );
        };

        this.afterInit = function () {
            if(debug){console.log("convertAnchor.afterInit")}
            let thisEditor = this.editor;

            thisEditor.model.schema.addAttributeCheck( context => {
                if(debug){console.log("convertAnchor.addAttributeCheck", {
                    context : context
                })}
                if ( context.endsWith( 'a' ) ) {
                    if(debug){console.log("convertAnchor.addAttributeCheck.endsWith( 'a' )")}
                    return true;
                }
            } );
            // The view-to-model converter converting a view <div> with all its attributes to the model.
            thisEditor.conversion.for( 'upcast' ).elementToElement( {
                view: 'a',
                model: ( viewElement, { writer: modelWriter } ) => {
                    if(debug){console.log("convertAnchor", {
                        viewElement : viewElement
                    })}
                    return modelWriter.createElement( 'a', viewElement.getAttributes() );
                }
            } );

            // The model-to-view converter for the <div> element (attributes are converted separately).
            thisEditor.conversion.for( 'downcast' ).elementToElement( {
                model: 'a',
                view: 'a',
            } );

            // The model-to-view converter for <div> attributes.
            // Note that a lower-level, event-based API is used here.
            thisEditor.conversion.for( 'downcast' ).add( dispatcher => {
                if(debug){console.log("convertAnchor.conversion.downcast")}
                dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
                    if(debug){console.log("convertAnchor.conversion.downcast.dispatcher:attribute", {
                        evt : evt, data : data, conversionApi : conversionApi
                    })}
                    // Convert <a> attributes only.
                    if ( data.item.name !== 'a' ) {
                        return;
                    }

                    const viewWriter = conversionApi.writer;
                    const view = conversionApi.mapper.toViewElement( data.item );
                    if(debug){console.log("convertAnchor.conversion.downcast.dispatcher:attribute", {
                        viewWriter : viewWriter, view : view
                    })}

                    // In the model-to-view conversion we convert changes.
                    // An attribute can be added or removed or changed.
                    // The below code handles all 3 cases.
                    
                    if ( data.attributeNewValue && data.attributeKey!="href") {
                        viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, view );
                    } else {
                        viewWriter.removeAttribute( data.attributeKey, view );
                    }
                } );
            } );
        };
        function findViewChild( viewElement, viewElementName, conversionApi ) {
            const viewChildren = Array.from( conversionApi.writer.createRangeIn( viewElement ).getItems() );

            return viewChildren.find( item => item.is( 'element', viewElementName ) );
        }
    }
    
    function convertImage(editor){
        let debug = false;
        if(debug){console.log("convertImage")}

        this.editor = editor;
        this.init = function () {
            if(debug){console.log("convertImage.init")}
            let thisEditor = this.editor;
            thisEditor.model.schema.register( 'img', {
                allowWhere: '$block',
                allowContentOf: '$root',
                allowIn : ['img']
            } );

        };

        this.afterInit = function () {
            if(debug){console.log("convertImage.afterInit")}
            let thisEditor = this.editor;
            
            // Define on which elements the CSS classes should be preserved:
            setupCustomClassConversion( 'img', 'image', thisEditor );
            thisEditor.conversion.for( 'upcast' ).add( upcastCustomClasses( 'figure' ), { priority: 'low' } );
            // Define custom attributes that should be preserved.
            setupCustomAttributeConversion( 'img', 'image', 'id', editor );
            setupCustomAttributeConversion( 'img', 'image', 'width', editor );
            setupCustomAttributeConversion( 'img', 'image', 'height', editor );
        };
        /**
         * Sets up a conversion that preserves classes on <img> and <table> elements.
         */
        function setupCustomClassConversion( viewElementName, modelElementName, editor ) {
            // The 'customClass' attribute stores custom classes from the data in the model so that schema definitions allow this attribute.
            editor.model.schema.extend( modelElementName, { allowAttributes: [ 'customClass' ] } );

            // Defines upcast converters for the <img> and <table> elements with a "low" priority so they are run after the default converters.
            editor.conversion.for( 'upcast' ).add( upcastCustomClasses( viewElementName ), { priority: 'low' } );

            // Defines downcast converters for a model element with a "low" priority so they are run after the default converters.
            // Use `downcastCustomClassesToFigure` if you want to keep your classes on <figure> element or `downcastCustomClassesToChild`
            // if you would like to keep your classes on a <figure> child element, i.e. <img>.
            editor.conversion.for( 'downcast' ).add( downcastCustomClassesToFigure( modelElementName ), { priority: 'low' } );
            // editor.conversion.for( 'downcast' ).add( downcastCustomClassesToChild( viewElementName, modelElementName ), { priority: 'low' } );
        }

        /**
         * Sets up a conversion for a custom attribute on the view elements contained inside a <figure>.
         *
         * This method:
         * - Adds proper schema rules.
         * - Adds an upcast converter.
         * - Adds a downcast converter.
         */
        function setupCustomAttributeConversion( viewElementName, modelElementName, viewAttribute, editor ) {
            // Extends the schema to store an attribute in the model.
            const modelAttribute = `custom${ viewAttribute }`;

            editor.model.schema.extend( modelElementName, { allowAttributes: [ modelAttribute ] } );

            editor.conversion.for( 'upcast' ).add( upcastAttribute( viewElementName, viewAttribute, modelAttribute ) );
            editor.conversion.for( 'downcast' ).add( downcastAttribute( modelElementName, viewElementName, viewAttribute, modelAttribute ) );

        }

        /**
         * Creates an upcast converter that will pass all classes from the view element to the model element.
         */
        function upcastCustomClasses( elementName ) {
            return dispatcher => dispatcher.on( `element:${ elementName }`, ( evt, data, conversionApi ) => {
                const viewItem = data.viewItem;
                const modelRange = data.modelRange;

                const modelElement = modelRange && modelRange.start.nodeAfter;

                if ( !modelElement ) {
                    return;
                }

                // The upcast conversion picks up classes from the base element and from the <figure> element so it should be extensible.
                const currentAttributeValue = modelElement.getAttribute( 'customClass' ) || [];

                currentAttributeValue.push( ...viewItem.getClassNames() );

                conversionApi.writer.setAttribute( 'customClass', currentAttributeValue, modelElement );
            } );
        }

        /**
         * Creates a downcast converter that adds classes defined in the `customClass` attribute to a <figure> element.
         *
         * This converter expects that the view element is nested in a <figure> element.
         */
        function downcastCustomClassesToFigure( modelElementName ) {
            return dispatcher => dispatcher.on( `insert:${ modelElementName }`, ( evt, data, conversionApi ) => {
                const modelElement = data.item;

                const viewFigure = conversionApi.mapper.toViewElement( modelElement );

                if ( !viewFigure ) {
                    return;
                }

                // The code below assumes that classes are set on the <figure> element.
                conversionApi.writer.addClass( modelElement.getAttribute( 'customClass' ), viewFigure );
            } );
        }

        /**
         * Creates a downcast converter that adds classes defined in the `customClass` attribute to a <figure> child element.
         *
         * This converter expects that the view element is nested in a <figure> element.
         */
        function downcastCustomClassesToChild( viewElementName, modelElementName ) {
            return dispatcher => dispatcher.on( `insert:${ modelElementName }`, ( evt, data, conversionApi ) => {
                const modelElement = data.item;

                const viewFigure = conversionApi.mapper.toViewElement( modelElement );

                if ( !viewFigure ) {
                    return;
                }

                // The code below assumes that classes are set on the element inside the <figure>.
                const viewElement = findViewChild( viewFigure, viewElementName, conversionApi );

                conversionApi.writer.addClass( modelElement.getAttribute( 'customClass' ), viewElement );
            } );
        }

        /**
         * Helper method that searches for a given view element in all children of the model element.
         *
         * @param {module:engine/view/item~Item} viewElement
         * @param {String} viewElementName
         * @param {module:engine/conversion/downcastdispatcher~DowncastConversionApi} conversionApi
         * @return {module:engine/view/item~Item}
         */
        function findViewChild( viewElement, viewElementName, conversionApi ) {
            const viewChildren = Array.from( conversionApi.writer.createRangeIn( viewElement ).getItems() );

            return viewChildren.find( item => item.is( 'element', viewElementName ) );
        }

        /**
         * Returns the custom attribute upcast converter.
         */
        function upcastAttribute( viewElementName, viewAttribute, modelAttribute ) {
            return dispatcher => dispatcher.on( `element:${ viewElementName }`, ( evt, data, conversionApi ) => {
                const viewItem = data.viewItem;
                const modelRange = data.modelRange;

                const modelElement = modelRange && modelRange.start.nodeAfter;

                if ( !modelElement ) {
                    return;
                }

                conversionApi.writer.setAttribute( modelAttribute, viewItem.getAttribute( viewAttribute ), modelElement );
            } );
        }

        /**
         * Returns the custom attribute downcast converter.
         */
        function downcastAttribute( modelElementName, viewElementName, viewAttribute, modelAttribute ) {
            return dispatcher => dispatcher.on( `insert:${ modelElementName }`, ( evt, data, conversionApi ) => {
                const modelElement = data.item;

                const viewFigure = conversionApi.mapper.toViewElement( modelElement );
                const viewElement = findViewChild( viewFigure, viewElementName, conversionApi );

                if ( !viewElement ) {
                    return;
                }

                conversionApi.writer.setAttribute( viewAttribute, modelElement.getAttribute( modelAttribute ), viewElement );
            } );
        }
    }
    InlineEditor
        .create( this[0], {
            extraPlugins: [ convertDiv, convertImage, convertSpan, convertAnchor ]
        })
        .then(editor=>{
            window.editor = editor;
            console.log( window.editor.getData());
            editor.model.document.on( 'change:data', () => {
                console.log( window.editor.getData());
            } );
        })
        .catch( error => {
            console.error( error );
        });
}; 

$.fn.customCkEditor.defaults = {
    option:{}
}
}( jQuery ));