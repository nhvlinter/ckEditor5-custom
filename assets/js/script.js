
// import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder';
// require("../plugin/ckfinder/ckfinder");
// require("../plugin/ckfinder/ckfinder");
import CKEditorInspector from '@ckeditor/ckeditor5-inspector';

import MyEditor from "../plugin/ckeditor5/src/ckeditor";
// import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder';
// window.CKFinder = require ("../plugin/ckfinder/ckfinder");

(function () {


// import CKFinder from "../plugin/ckfinder/ckfinder";
// CKFinder.basePath = "/assets/plugin/ckfinder/";
// CKFinder.connector ="php";
        const NAME = "Son Nguyen";
    // window.CKFinder = CKFinder;
    // window.CKFinder.basePath = "/assets/plugin/ckfinder/";
    //     console.log({
    //         CKFinder : window.CKFinder
    //     });
// console.log({
//     NAME : NAME
// });

        class Test {
            constructor(name){
                this._name = name;
            }


            get name() {
                return this._name;
            }

            set name(value) {
                this._name = value;
            }
        }

        let test = new Test(NAME);
        let name = test.name;

        console.log(name);


        /**
         * Sets up a conversion that preserves classes on <img> and <table> elements.
         */
        function setupCustomClassConversion(viewElementName, modelElementName, editor) {
            // The 'customClass' attribute stores custom classes from the data in the model so that schema definitions allow this attribute.
            editor.model.schema.extend(modelElementName, {allowAttributes: ['customClass']});

            // Defines upcast converters for the <img> and <table> elements with a "low" priority so they are run after the default converters.
            editor.conversion.for('upcast').add(upcastCustomClasses(viewElementName), {priority: 'low'});

            // Defines downcast converters for a model element with a "low" priority so they are run after the default converters.
            // Use `downcastCustomClassesToFigure` if you want to keep your classes on <figure> element or `downcastCustomClassesToChild`
            // if you would like to keep your classes on a <figure> child element, i.e. <img>.
            editor.conversion.for('downcast').add(downcastCustomClassesToFigure(modelElementName), {priority: 'low'});
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
        function setupCustomAttributeConversion(viewElementName, modelElementName, viewAttribute, editor) {
            // Extends the schema to store an attribute in the model.
            const modelAttribute = `custom${ viewAttribute }`;

            editor.model.schema.extend(modelElementName, {allowAttributes: [modelAttribute]});

            editor.conversion.for('upcast').add(upcastAttribute(viewElementName, viewAttribute, modelAttribute));
            editor.conversion.for('downcast').add(downcastAttribute(modelElementName, viewElementName, viewAttribute, modelAttribute));
        }

        /**
         * Creates an upcast converter that will pass all classes from the view element to the model element.
         */
        function upcastCustomClasses(elementName) {
            return dispatcher => dispatcher.on(`element:${ elementName }`, (evt, data, conversionApi) => {
                const viewItem = data.viewItem;
                const modelRange = data.modelRange;

                const modelElement = modelRange && modelRange.start.nodeAfter;

                if (!modelElement) {
                    return;
                }

                // The upcast conversion picks up classes from the base element and from the <figure> element so it should be extensible.
                const currentAttributeValue = modelElement.getAttribute('class') || [];

                currentAttributeValue.push(...viewItem.getClassNames());

                conversionApi.writer.setAttribute('customClass', currentAttributeValue, modelElement);
            });
        }

        /**
         * Creates a downcast converter that adds classes defined in the `customClass` attribute to a <figure> element.
         *
         * This converter expects that the view element is nested in a <figure> element.
         */
        function downcastCustomClassesToFigure(modelElementName) {
            return dispatcher => dispatcher.on(`insert:${ modelElementName }`, (evt, data, conversionApi) => {
                const modelElement = data.item;

                const viewFigure = conversionApi.mapper.toViewElement(modelElement);

                if (!viewFigure) {
                    return;
                }

                // The code below assumes that classes are set on the <figure> element.
                conversionApi.writer.addClass(modelElement.getAttribute('customClass'), viewFigure);
            });
        }

        /**
         * Creates a downcast converter that adds classes defined in the `customClass` attribute to a <figure> child element.
         *
         * This converter expects that the view element is nested in a <figure> element.
         */
        function downcastCustomClassesToChild(viewElementName, modelElementName) {
            return dispatcher => dispatcher.on(`insert:${ modelElementName }`, (evt, data, conversionApi) => {
                const modelElement = data.item;

                const viewFigure = conversionApi.mapper.toViewElement(modelElement);

                if (!viewFigure) {
                    return;
                }

                // The code below assumes that classes are set on the element inside the <figure>.
                const viewElement = findViewChild(viewFigure, viewElementName, conversionApi);

                conversionApi.writer.addClass(modelElement.getAttribute('customClass'), viewElement);
            });
        }

        /**
         * Helper method that searches for a given view element in all children of the model element.
         *
         *
         */

        function findViewChild(viewElement, viewElementName, conversionApi) {
            const viewChildren = Array.from(conversionApi.writer.createRangeIn(viewElement).getItems());

            return viewChildren.find(item => item.is('element', viewElementName));
        }

        /**
         * Returns the custom attribute upcast converter.
         */
        function upcastAttribute(viewElementName, viewAttribute, modelAttribute) {
            return dispatcher => dispatcher.on(`element:${ viewElementName }`, (evt, data, conversionApi) => {
                const viewItem = data.viewItem;
                const modelRange = data.modelRange;

                const modelElement = modelRange && modelRange.start.nodeAfter;

                if (!modelElement) {
                    return;
                }

                conversionApi.writer.setAttribute(modelAttribute, viewItem.getAttribute(viewAttribute), modelElement);
            });
        }

        /**
         * Returns the custom attribute downcast converter.
         */
        function downcastAttribute(modelElementName, viewElementName, viewAttribute, modelAttribute) {
            return dispatcher => dispatcher.on(`insert:${ modelElementName }`, (evt, data, conversionApi) => {
                const modelElement = data.item;

                const viewFigure = conversionApi.mapper.toViewElement(modelElement);
                const viewElement = findViewChild(viewFigure, viewElementName, conversionApi);

                if (!viewElement) {
                    return;
                }

                conversionApi.writer.setAttribute(viewAttribute, modelElement.getAttribute(modelAttribute), viewElement);
            });
        }


        /*******************************
         * FINAL CONVERTERS
         ********************************/

        let _convertersApi = {
            _batch: function (editor, viewName, modelName) {
                let debug = true;
                if (debug) {
                    console.log("_convertersApi._batch()", arguments)
                }

                editor.model.schema.extend(modelName, {allowAttributes: modelName + 'Class'});


                // //TODO this is the original
                // // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
                editor.conversion.for('downcast').attributeToElement({
                    model: modelName,
                    view: (attributeValue, {writer}) => {
                        const linkElement = writer.createAttributeElement(viewName, {
                            "class": attributeValue
                        }, {priority: 0});
                        if (debug) {
                            console.log({
                                attributeValue: attributeValue, writer: writer, linkElement: linkElement
                            })
                        }
                        writer.setCustomProperty(modelName, true, linkElement);

                        return linkElement;
                    },
                    converterPriority: 'low'
                });

                //
                //TODO this is the original
                // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
                editor.conversion.for('upcast').attributeToAttribute({
                    view: "class",
                    model: 'class',
                    converterPriority: 'low'
                });
                editor.conversion.for('upcast').attributeToAttribute({
                    view: 'id',
                    model: 'id',
                    converterPriority: 'low'
                });


                // Allow <div> elements in the model to have all attributes.
                editor.model.schema.addAttributeCheck(context => {
                    if (context.endsWith(modelName)) {
                        return true;
                    }
                });


                // The model-to-view converter for the <div> element (attributes are converted separately).
                // editor.conversion.for( 'downcast' ).elementToElement( {
                //     model: 'classParagraph',
                //     view: 'paragraph'
                // } );

                // The model-to-view converter for <div> attributes.
                // Note that a lower-level, event-based API is used here.
                // editor.conversion.for('downcast').add(dispatcher => {
                //     dispatcher.on('attribute', (evt, data, conversionApi) => {
                //         if (debug) {
                //             console.log({
                //                 evt: evt, data: data, conversionApi: conversionApi
                //             })
                //         }
                //         // Convert <div> attributes only.
                //         if (data.item.name !== modelName) {
                //             return;
                //         }
                //
                //         const viewWriter = conversionApi.writer;
                //         const viewDiv = conversionApi.mapper.toViewElement(data.item);
                //
                //         // In the model-to-view conversion we convert changes.
                //         // An attribute can be added or removed or changed.
                //         // The below code handles all 3 cases.
                //         if (data.attributeNewValue) {
                //             viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, viewDiv);
                //         } else {
                //             viewWriter.removeAttribute(data.attributeKey, viewDiv);
                //         }
                //     });
                // });
            }
        };

        function convert_P_H_attr(editor) {
            this.editor = editor;
            this.afterInit = function () {
                let batch = {
                    paragraph: "p", heading1: "h1", heading2: "h2", heading3: "h3",
                    heading4: "h4", heading5: "h5", heading6: "h6", label: "label"
                };
                Object.keys(batch).forEach(function (modelName) {
                    let viewName = batch[modelName];
                    _convertersApi._batch(editor, viewName, modelName);
                });
            };
        }

        function convertIcon(editor) {
            let debug = false;
            if (debug) {
                console.log('convertIcon')
            }

            this.editor = editor;

            editor.model.schema.extend('$text', {allowAttributes: 'icon'});
            this.init = function () {
                if (debug) {
                    console.log('convertIcon.init')
                }
            };
            this.afterInit = function () {

                if (debug) {
                    console.log("allowAttr(class)")
                }
                // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
                editor.conversion.for('downcast').attributeToElement({
                    model: 'icon',
                    view: (attributeValue, {writer}) => {

                        let attr = {};
                        attr['class'] = attributeValue;
                        attr['contenteditable'] = false;
                        if (debug) {
                            console.log('convertIcon.conversion.forDowncast.attributeToElement', {
                                attr: attr, attributeValue: attributeValue, writer: writer
                            })
                        }
                        return writer.createAttributeElement('i', attr, {priority: 5});
                    },
                    converterPriority: 'high'
                });


                // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
                editor.conversion.for('upcast').attributeToAttribute({
                    view: {
                        name: 'i',
                        key: 'class',
                        classes: ["fa", "fab", "far", "fas"]
                    },
                    model: 'icon',
                    converterPriority: 'high'
                });


            };

        }

        function convertImg(editor) {
            let debug = false;
            if (debug) {
                console.log('convertImg')
            }

            this.editor = editor;
            this.init = function () {
                if (debug) {
                    console.log('convertImg.init')
                }

            };
            this.afterInit = function () {
                if (debug) {
                    console.log('convertImg.afterInit')
                }
                let thisEditor = this.editor;
                // thisEditor.conversion.for( 'upcast' ).add( upcastCustomClasses( 'figure' ), { priority: 'low' } );
                // setupCustomAttributeConversion( 'img', 'image', 'class', editor );
                // The model-to-view converter for <div> attributes.
                // Note that a lower-level, event-based API is used here.
                thisEditor.conversion.for('downcast').add(dispatcher => {
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        // Convert <div> attributes only.
                        if (data.item.name !== 'image') {
                            return;
                        }

                        if (debug) {
                            console.log({
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }
                        const viewWriter = conversionApi.writer;
                        const viewDiv = conversionApi.mapper.toViewElement(data.item);

                        // In the model-to-view conversion we convert changes.
                        // An attribute can be added or removed or changed.
                        // The below code handles all 3 cases.
                        if (data.attributeNewValue) {
                            viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, viewDiv);
                        } else {
                            viewWriter.removeAttribute(data.attributeKey, viewDiv);
                        }
                        if (data.attributeKey === "imageStyle") {

                            viewWriter.removeAttribute(data.attributeKey, viewDiv);
                        }
                        viewWriter.removeAttribute("customclass", viewDiv);
                    });
                });
                setupCustomAttributeConversion('img', 'image', 'class', editor);
            };


        }

        function convertDiv(editor) {
            let debug = false;
            if (debug) {
                console.log("convertDiv")
            }

            this.editor = editor;
            this.init = function () {
                if (debug) {
                    console.log("convertDiv.init")
                }
                let thisEditor = this.editor;
                thisEditor.model.schema.register('div', {
                    allowWhere: '$block',
                    allowContentOf: '$root',
                    allowIn: ['div', 'form']
                });

            };

            this.afterInit = function () {
                if (debug) {
                    console.log("convertDiv.afterInit")
                }
                let thisEditor = this.editor;

                thisEditor.model.schema.addAttributeCheck(context => {
                    if (debug) {
                        console.log("convertDiv.addAttributeCheck", {
                            context: context
                        })
                    }
                    if (context.endsWith('div')) {
                        if (debug) {
                            console.log("convertDiv.addAttributeCheck.endsWith( 'div' )")
                        }
                        return true;
                    }
                });
                // The view-to-model converter converting a view <div> with all its attributes to the model.
                thisEditor.conversion.for('upcast').elementToElement({
                    view: 'div',
                    model: (viewElement, {writer: modelWriter}) => {
                        if (debug) {
                            console.log("convertDiv", {
                                viewElement: viewElement
                            })
                        }
                        return modelWriter.createElement('div', viewElement.getAttributes());
                    }
                });

                // The model-to-view converter for the <div> element (attributes are converted separately).
                thisEditor.conversion.for('downcast').elementToElement({
                    model: 'div',
                    view: 'div'
                });

                // The model-to-view converter for <div> attributes.
                // Note that a lower-level, event-based API is used here.
                thisEditor.conversion.for('downcast').add(dispatcher => {
                    if (debug) {
                        console.log("convertDiv.conversion.downcast")
                    }
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log("convertDiv.conversion.downcast.dispatcher:attribute", {
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }
                        // Convert <div> attributes only.
                        if (data.item.name !== 'div') {
                            return;
                        }

                        const viewWriter = conversionApi.writer;
                        const view = conversionApi.mapper.toViewElement(data.item);
                        if (debug) {
                            console.log("convertDiv.conversion.downcast.dispatcher:attribute", {
                                viewWriter: viewWriter, view: view
                            })
                        }

                        // In the model-to-view conversion we convert changes.
                        // An attribute can be added or removed or changed.
                        // The below code handles all 3 cases.
                        if (data.attributeNewValue) {
                            viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, view);
                        } else {
                            viewWriter.removeAttribute(data.attributeKey, view);
                        }
                    });
                });
            };

        }

        function convertForm(editor) {
            let debug = false;
            if (debug) {
                console.log("convertForm")
            }

            this.editor = editor;
            this.init = function () {
                if (debug) {
                    console.log("convertForm.init")
                }
                let thisEditor = this.editor;
                thisEditor.model.schema.register('form', {
                    allowWhere: '$block',
                    allowContentOf: '$root',
                    allowIn: ['div']
                });

            };

            this.afterInit = function () {
                if (debug) {
                    console.log("convertForm.afterInit")
                }
                let thisEditor = this.editor;

                thisEditor.model.schema.addAttributeCheck(context => {
                    if (debug) {
                        console.log("convertForm.addAttributeCheck", {
                            context: context
                        })
                    }
                    if (context.endsWith('form')) {
                        if (debug) {
                            console.log("convertForm.addAttributeCheck.endsWith( 'form' )")
                        }
                        return true;
                    }
                });
                // The view-to-model converter converting a view <div> with all its attributes to the model.
                thisEditor.conversion.for('upcast').elementToElement({
                    view: 'form',
                    model: (viewElement, {writer: modelWriter}) => {
                        if (debug) {
                            console.log("convertForm", {
                                viewElement: viewElement
                            })
                        }
                        return modelWriter.createElement('form', viewElement.getAttributes());
                    }
                });

                // The model-to-view converter for the <div> element (attributes are converted separately).
                thisEditor.conversion.for('downcast').elementToElement({
                    model: 'form',
                    view: 'form'
                });

                // The model-to-view converter for <div> attributes.
                // Note that a lower-level, event-based API is used here.
                thisEditor.conversion.for('downcast').add(dispatcher => {
                    if (debug) {
                        console.log("convertForm.conversion.downcast")
                    }
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log("convertForm.conversion.downcast.dispatcher:attribute", {
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }
                        // Convert <div> attributes only.
                        if (data.item.name !== 'form') {
                            return;
                        }

                        const viewWriter = conversionApi.writer;
                        const view = conversionApi.mapper.toViewElement(data.item);
                        if (debug) {
                            console.log("convertForm.conversion.downcast.dispatcher:attribute", {
                                viewWriter: viewWriter, view: view
                            })
                        }

                        // In the model-to-view conversion we convert changes.
                        // An attribute can be added or removed or changed.
                        // The below code handles all 3 cases.
                        if (data.attributeNewValue) {
                            viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, view);
                        } else {
                            viewWriter.removeAttribute(data.attributeKey, view);
                        }
                    });
                });
            };

        }

        function convertUl(editor) {
            let debug = false;
            if (debug) {
                console.log("convertUl")
            }

            this.editor = editor;
            if (debug) {
                console.log("convertUl.init")
            }
            let thisEditor = this.editor;
            // thisEditor.model.schema.register( 'ul', {
            //     inheritAllFrom: 'listItem',
            //     allowAttributes : ['class']
            // } );
            this.afterInit = function () {
                // this.init = function () {
                editor.model.schema.extend('listItem', {allowAttributes: 'class'});

                // editor.conversion.for( 'upcast' ).attributeToAttribute( {
                //     model: 'listItem',
                //     view: {
                //         name: 'li',
                //         key: 'class'
                //     },
                //     converterPriority: 'low'
                // } );


                // Allow <div> elements in the model to have all attributes.
                editor.model.schema.addAttributeCheck(context => {
                    if (context.endsWith('listItem')) {
                        if (debug) {
                            console.log("context.endsWith( 'listItem' )")
                        }
                        return true;
                    }
                });

                editor.conversion.for('downcast').attributeToAttribute({
                    model: 'customClass',
                    view: 'class'
                });
                // //TODO this is the original
                // // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
                // editor.conversion.for( 'downcast' ).attributeToElement( {
                //     model: 'ul',
                //     view: ( attributeValue, { writer } ) => {
                //         const linkElement = writer.createAttributeElement( 'ul', { "class": attributeValue
                //         }, { priority: 0 } );
                //         if(debug){console.log({
                //             attributeValue : attributeValue, writer : writer, linkElement : linkElement
                //         })}
                //         writer.setCustomProperty( 'ul', true, linkElement );
                //
                //         return linkElement;
                //     },
                //     converterPriority: 'low'
                // } );
                //
                // //
                // //TODO this is the original
                // // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
                // editor.conversion.for( 'upcast' ).attributeToAttribute( {
                //     view: {
                //         name : 'li'
                //     },
                //     model: 'ul',
                //     converterPriority: 'high'
                // } );
                // editor.conversion.for( 'upcast' ).attributeToAttribute( {
                //     view: 'listItem',
                //     model: 'li',
                //     converterPriority: 'high'
                // } );


                // The model-to-view converter for the <div> element (attributes are converted separately).
                // editor.conversion.for( 'downcast' ).elementToElement( {
                //     model: 'classParagraph',
                //     view: 'paragraph'
                // } );

                // The model-to-view converter for <div> attributes.
                // Note that a lower-level, event-based API is used here.
                editor.conversion.for('downcast').add(dispatcher => {
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log('downcast|' + evt.name, {
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }

                    },);
                    dispatcher.on('element', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log('downcast|' + evt.name, {
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }

                    });
                });
                editor.conversion.for('upcast').add(dispatcher => {
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log('upcast|' + evt.name, {
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }

                    });
                    dispatcher.on("element:li", (evt, data, conversionApi) => {
                        // if(debug){console.log('upcast|'+evt.name)}
                        const viewItem = data.viewItem;
                        const writer = conversionApi.writer;

                        if (viewItem.name !== 'li') {
                            return;
                        }
                        const modelRange = data.modelRange;

                        const modelElement = modelRange && modelRange.start.nodeAfter;

                        if (!modelElement) {
                            return;
                        }

                        // The upcast conversion picks up classes from the base element and from the <figure> element so it should be extensible.
                        const currentAttributeValue = modelElement.getAttribute('class') || [];

                        currentAttributeValue.push(...viewItem.getClassNames());

                        writer.setAttribute('customClass', currentAttributeValue, modelElement);
                        if (debug) {
                            console.log('upcast|' + evt.name, {
                                evt: evt, data: data, writer: writer, viewItem: viewItem, modelRange: modelRange,
                                modelElement: modelElement, currentAttributeValue: currentAttributeValue
                            })
                        }

                    });
                });

            }

        }

        function convertInput(editor) {
            let debug = false;
            if (debug) {
                console.log("convertInput")
            }
            this.editor = editor;
            // Allow <div> elements in the model.
            this.init = function () {
                if (debug) {
                    console.log("convertInput.init")
                }
                let thisEditor = this.editor;
                thisEditor.model.schema.register('input', {
                    allowWhere: 'div',
                    allowContentOf: 'div',
                    isSelectable: false,
                    allowAttributes: ['id', 'class', 'disabled', 'required', 'type', 'name', 'placeholder']
                });

            };

            this.afterInit = function () {
                if (debug) {
                    console.log("convertInput.afterInit")
                }
                let thisEditor = this.editor;
                // Allow <div> elements in the model to have all attributes.
                thisEditor.model.schema.addAttributeCheck(context => {
                    if (context.endsWith('input')) {
                        return true;
                    }
                });
                // The model-to-view converter for the <div> element (attributes are converted separately).
                thisEditor.conversion.for('downcast').elementToElement({
                    model: 'input',
                    view: (modelElement, conversionApi) => {
                        const modelWriter = conversionApi.writer;
                        let view = modelWriter.createAttributeElement('input', modelElement.getAttributes(), {priority: 5});
                        // modelWriter.setAttribute('disabled', 'true', view);

                        if (debug) {
                            console.log("convertInput.downcast.elementToElement", {
                                modelElement: modelElement, view: view
                            })
                        }
                        return view;

                        // return writer.createContainerElement( 'h' + modelElement.getAttribute( 'level' ) );
                    }
                });
                // The view-to-model converter converting a view <div> with all its attributes to the model.
                thisEditor.conversion.for('upcast').elementToElement({
                    view: {
                        name: 'input'
                    },
                    model: (viewElement, {writer: modelWriter}) => {
                        let view = modelWriter.createElement('input', viewElement.getAttributes());
                        if (debug) {
                            console.log("convertInput.upcast.elementToElement", {
                                viewElement: viewElement, view: view
                            })
                        }
                        // modelWriter.setAttribute('disabled', 'true', view);
                        return view;
                    }

                });

                thisEditor.conversion.for('downcast').add(dispatcher => {
                    dispatcher.on('element', (evt, data, conversionApi) => {
                        // if(debug){console.log("downcast|element:input")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        // if(debug){console.log("downcast|attribute:disabled")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                    dispatcher.on('properties', (evt, data, conversionApi) => {
                        // if(debug){console.log("downcast|attribute:disabled")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                });

                thisEditor.conversion.for('upcast').add(dispatcher => {
                    dispatcher.on('element:input', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log("upcast|element:input")
                        }
                    });
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        // if(debug){console.log("upcast|attribute:disabled")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                });

            };

        }

        function convertTextArea(editor) {
            let debug = false;
            if (debug) {
                console.log("convertTextArea")
            }
            this.editor = editor;
            // Allow <div> elements in the model.
            this.init = function () {
                if (debug) {
                    console.log("convertTextArea.init")
                }
                let thisEditor = this.editor;
                thisEditor.model.schema.register('textarea', {
                    allowWhere: 'div',
                    allowContentOf: 'div',
                    isSelectable: false,
                    allowAttributes: ['id', 'class', 'disabled', 'required', 'type', 'name', 'placeholder']
                });

            };

            this.afterInit = function () {
                if (debug) {
                    console.log("convertTextArea.afterInit")
                }
                let thisEditor = this.editor;
                // Allow <div> elements in the model to have all attributes.
                thisEditor.model.schema.addAttributeCheck(context => {
                    if (context.endsWith('textarea')) {
                        return true;
                    }
                });
                // The model-to-view converter for the <div> element (attributes are converted separately).
                thisEditor.conversion.for('downcast').elementToElement({
                    model: 'textarea',
                    view: (modelElement, conversionApi) => {
                        const modelWriter = conversionApi.writer;
                        let view = modelWriter.createAttributeElement('textarea', modelElement.getAttributes(), {priority: 5});
                        // modelWriter.setAttribute('disabled', 'true', view);

                        if (debug) {
                            console.log("convertTextArea.downcast.elementToElement", {
                                modelElement: modelElement, view: view
                            })
                        }
                        return view;

                        // return writer.createContainerElement( 'h' + modelElement.getAttribute( 'level' ) );
                    }
                });
                // The view-to-model converter converting a view <div> with all its attributes to the model.
                thisEditor.conversion.for('upcast').elementToElement({
                    view: {
                        name: 'textarea'
                    },
                    model: (viewElement, {writer: modelWriter}) => {
                        let view = modelWriter.createElement('textarea', viewElement.getAttributes());
                        if (debug) {
                            console.log("convertTextArea.upcast.elementToElement", {
                                viewElement: viewElement, view: view
                            })
                        }
                        // modelWriter.setAttribute('disabled', 'true', view);
                        return view;
                    }

                });

                thisEditor.conversion.for('downcast').add(dispatcher => {
                    dispatcher.on('element', (evt, data, conversionApi) => {
                        // if(debug){console.log("downcast|element:input")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        // if(debug){console.log("downcast|attribute:disabled")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                    dispatcher.on('properties', (evt, data, conversionApi) => {
                        // if(debug){console.log("downcast|attribute:disabled")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                });

                thisEditor.conversion.for('upcast').add(dispatcher => {
                    dispatcher.on('element:input', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log("upcast|element:input")
                        }
                    });
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        // if(debug){console.log("upcast|attribute:disabled")}
                        if (debug) {
                            console.log(evt.name)
                        }
                    });
                });

            };

        }

        function convertItalic(editor) {
            let debug = false;
            if (debug) {
                console.log("convertItalic")
            }
            this.editor = editor;
            this.init = function () {
                if (debug) {
                    console.log("convertItalic.init")
                }
            };
            this.afterInit = function () {
                if (debug) {
                    console.log("convertItalic.afterInit")
                }
                let thisEditor = this.editor;
                thisEditor.conversion.for('downcast').attributeToElement({
                    model: 'italic',
                    view: (attributeValue, {writer}) => {
                        if (debug) {
                            console.log("convertItalic", {
                                attributeValue: attributeValue, writer: writer
                            })
                        }
                        return writer.createAttributeElement('em', {}, {priority: 5});
                    },
                    converterPriority: 'high'
                });
                thisEditor.conversion.for('upcast').attributeToAttribute({
                    view: {
                        name: 'em'
                    },
                    model: 'italic',
                    converterPriority: 'high'
                });
            };


        }

        function convertA(editor) {
            let debug = false;
            if (debug) {
                console.log("convertA")
            }
            this.editor = editor;
            this.init = function () {
                if (debug) {
                    console.log("convertA.init")
                }

            };
            this.afterInit = function () {

                let thisEditor = this.editor;
                if (debug) {
                    console.log("convertA.afterInit")
                }
                thisEditor.conversion.for('downcast').attributeToElement({
                    model: 'linkHref',
                    view: (attributeValue, {writer}) => {
                        if (debug) {
                            console.log({
                                attributeValue: attributeValue, writer: writer
                            })
                        }
                        let attr = {href: attributeValue};
                        if (typeof attributeValue === 'string') {
                            if (!attributeValue.match(/ckeditor\.com/)) {
                                attr.target = '_blank';
                            }
                        }
                        const linkElement = writer.createAttributeElement('a', attr, {priority: 5});
                        writer.setCustomProperty('link', attributeValue, linkElement);

                        return linkElement;
                    },
                    converterPriority: 'high'
                });

                //TODO this is the original
                // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
                thisEditor.conversion.for('upcast').attributeToAttribute({
                    view: {
                        name: 'a',
                        key: 'data-href'
                    },
                    model: 'linkHref',
                    converterPriority: 'high'
                });

                // this function allow some attribute to the $text model like class, id or other
                function allowAttr(attrName) {
                    //extend the $text model to the name;
                    thisEditor.model.schema.extend('$text', {allowAttributes: attrName + 'A'});
                    if (debug) {
                        console.log("allowAttr(" + attrName + ")")
                    }
                    // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
                    thisEditor.conversion.for('downcast').attributeToElement({
                        model: attrName + 'A',
                        view: (attributeValue, {writer}) => {

                            let attr = {};
                            attr[attrName] = attributeValue;
                            if (debug) {
                                console.log("allowAttr(" + attrName + ")", {
                                    attr: attr, attributeValue: attributeValue, writer: writer
                                })
                            }
                            const linkElement = writer.createAttributeElement('a', attr, {priority: 5});
                            writer.setCustomProperty('link', true, linkElement);

                            return linkElement;
                        },
                        converterPriority: 'low'
                    });


                    // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
                    thisEditor.conversion.for('upcast').attributeToAttribute({
                        view: {
                            name: 'a',
                            key: attrName
                        },
                        model: attrName + 'A',
                        converterPriority: 'low'
                    });

                }

                allowAttr('class');
                allowAttr('id');
                allowAttr('target');

            };
        }

        function convertSpan(editor) {
            let debug = false;
            if (debug) {
                console.log("convertSpan")
            }

            this.editor = editor;

            this.init = function () {
                if (debug) {
                    console.log("convertSpan.init")
                }

                let thisEditor = this.editor;
                thisEditor.model.schema.register('span', {
                    inheritAllFrom: 'paragraph',
                    allowAttributes: ['class']
                });
            };
            this.afterInit = function () {
                if (debug) {
                    console.log("convertSpan.afterInit")
                }

                let thisEditor = this.editor;
                // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
                // editor.conversion.for( 'upcast' ).attributeToAttribute( {
                //     view: 'span',
                //     model: 'span',
                //     converterPriority: 'high'
                // } );

                thisEditor.conversion.for('upcast').elementToElement({
                    view: 'span',
                    model: (viewElement, {writer: modelWriter}) => {
                        let attr = viewElement.getAttributes();
                        attr['contenteditable'] = 'false';
                        if (debug) {
                            console.log("convertSpan.conversion.forUpcast.elementToElement", {
                                viewElement: viewElement, attr: attr
                            })
                        }
                        return modelWriter.createElement('span', viewElement.getAttributes());
                    },
                    converterPriority: 'low'
                });


                thisEditor.conversion.for('downcast').elementToElement({
                    model: 'span',
                    view: (modelElement, conversionApi) => {
                        const modelWriter = conversionApi.writer;
                        let view = modelWriter.createAttributeElement('span', modelElement.getAttributes(), {priority: 0});

                        if (debug) {
                            console.log("convertInput.downcast.elementToElement", {
                                modelElement: modelElement, view: view
                            })
                        }
                        return view;

                        // return writer.createContainerElement( 'h' + modelElement.getAttribute( 'level' ) );
                    },
                    converterPriority: 'low'
                });

                // thisEditor.conversion.for( 'downcast' ).add( dispatcher => {
                //     dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
                //         if ( data.item.name !== 'span' ) {
                //             return;
                //         }
                //         const viewWriter = conversionApi.writer;
                //         const viewDiv = conversionApi.mapper.toViewElement( data.item );
                //
                //         // In the model-to-view conversion we convert changes.
                //         // An attribute can be added or removed or changed.
                //         // The below code handles all 3 cases.
                //         if ( data.attributeNewValue ) {
                //             viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, viewDiv );
                //         } else {
                //             viewWriter.removeAttribute( data.attributeKey, viewDiv );
                //         }
                //     } );
                // } );


            };

        }

        function convertLabel(editor) {
            let debug = false;
            if (debug) {
                console.log("convertLabel")
            }
            this.editor = editor;
            /** Cannot register twice bc register on heading option already
             * otherwise execute this
             thisEditor.model.schema.register('label', {
            inheritAllFrom: 'paragraph',
            allowAttributes: ['class', 'for']
        });
             */
            this.init = function () {
                if (debug) {
                    console.log("convertLabel.init")
                }
            };
            this.afterInit = function () {
                if (debug) {
                    console.log("convertLabel.afterInit")
                }
                let thisEditor = this.editor;

                thisEditor.conversion.for('upcast').elementToElement({
                    view: 'label',
                    model: (viewElement, {writer: modelWriter}) => {
                        return modelWriter.createElement('label', viewElement.getAttributes());
                    },
                    converterPriority: 'high'
                });

                thisEditor.conversion.for('downcast').add(dispatcher => {
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        if (data.item.name !== 'label') {
                            return;
                        }
                        const viewWriter = conversionApi.writer;
                        const viewDiv = conversionApi.mapper.toViewElement(data.item);

                        // In the model-to-view conversion we convert changes.
                        // An attribute can be added or removed or changed.
                        // The below code handles all 3 cases.
                        if (data.attributeNewValue) {
                            viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, viewDiv);
                        } else {
                            viewWriter.removeAttribute(data.attributeKey, viewDiv);
                        }
                    });
                });
            }


        }


        function convertBtn(editor) {
            let debug = false;
            if (debug) {
                console.log("convertBtn")
            }

            this.editor = editor;

            this.init = function () {
                if (debug) {
                    console.log("convertBtn.init")
                }
                let thisEditor = this.editor;
                // thisEditor.model.schema.register('button', {
                //     allowWhere: '$text', isInline: true,
                //     allowContentOf: '$block', allowAttributes: ['class', 'id']
                // });
            };
            this.afterInit = function () {
                if (debug) {
                    console.log("convertBtn.afterInit")
                }
                let thisEditor = this.editor;
                thisEditor.model.schema.addAttributeCheck(context => {
                    if (debug) {
                        console.log("convertBtn.addAttributeCheck", {context: context})
                    }
                    if (context.endsWith('button')) {
                        return true;
                    }
                });
                // The view-to-model converter converting a view <div> with all its attributes to the model.
                thisEditor.conversion.for('upcast').elementToElement({
                    view: 'button',
                    model: (viewElement, {writer: modelWriter}) => {
                        if (debug) {
                            console.log("convertBtn.upcast.elementToElement", {viewElement: viewElement})
                        }
                        return modelWriter.createElement('button', viewElement.getAttributes());
                    },
                    converterPriority: 'high'
                });

                // The model-to-view converter for the <div> element (attributes are converted separately).
                thisEditor.conversion.for('downcast').elementToElement({
                    model: 'button',
                    view: 'button',
                    converterPriority: 'high'
                });

                // The model-to-view converter for <div> attributes.
                // Note that a lower-level, event-based API is used here.
                thisEditor.conversion.for('downcast').add(dispatcher => {
                    if (debug) {
                        console.log("convertBtn.downcast.addDispatcher", {dispatcher: dispatcher})
                    }
                    dispatcher.on('attribute', (evt, data, conversionApi) => {
                        if (debug) {
                            console.log({
                                evt: evt, data: data, conversionApi: conversionApi
                            })
                        }
                        // Convert <div> attributes only.
                        if (data.item.name !== 'button') {
                            return;
                        }

                        const viewWriter = conversionApi.writer;
                        const viewDiv = conversionApi.mapper.toViewElement(data.item);

                        // In the model-to-view conversion we convert changes.
                        // An attribute can be added or removed or changed.
                        // The below code handles all 3 cases.
                        if (data.attributeNewValue) {
                            viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, viewDiv);
                        } else {
                            viewWriter.removeAttribute(data.attributeKey, viewDiv);
                        }
                    });
                });
            };

        }





        let Ck_Model = {};

        let Ck_Fn = {};

        Ck_Fn._conversion = [
            convertDiv, convertForm, convert_P_H_attr,  convertA,
            convertItalic, convertLabel, convertInput, convertTextArea,
            convertImg, convertIcon, convertBtn, convertUl
        ];

        Ck_Model.ckFinderOption = {
            basePath : '/assets/plugin/ckfinder/',
            // Upload the images to the server using the CKFinder QuickUpload command.
            uploadUrl: '/assets/plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
            // uploadUrl: '/ckfinder/core/connector/php/connector.php',

            // Define the CKFinder configuration (if necessary).
            options: {
                resourceType: 'Images'
            },
            // Open the file manager in the pop-up window.
            openerMethod: 'modal',
            chooseFiles : true
        };

        Ck_Model.heading = {
            options: [
                {model: 'heading1', view: 'h1', title: 'Heading 1', 'class': ''},
                {model: 'heading2', view: 'h2', title: 'Heading 2', 'class': ''},
                {model: 'heading3', view: 'h3', title: 'Heading 3', 'class': ''},
                {model: 'heading4', view: 'h4', title: 'Heading 4', 'class': ''},
                {model: 'heading5', view: 'h5', title: 'Heading 5', 'class': ''},
                {model: 'heading6', view: 'h6', title: 'Heading 6', 'class': ''},
                {model: 'paragraph', view: 'p', title: 'Paragraph', 'class': ''},
                {model: 'button', view: 'button', title: 'Button', 'class': 'btn'},
                {model: 'label', view: 'label', title: 'Label', 'class': ''}/*,
                {model: 'span', view: 'span', title: 'Text', 'class': ''}*/
            ]
        };

        Ck_Model.contentOption = {
            // removePlugins: [ 'link' ],
            // extraPlugins: [convertDiv ],
            // plugins : [CKFinder ],
            // plugins: [ Essentials, Paragraph, Bold, Italic, Image, InsertImage, ImageCaption ],
            // toolbar: [ 'bold', 'italic', 'insertImage' ],
            extraPlugins: Ck_Fn._conversion,
            removePlugins: ['Title', 'RestrictedEditingMode'],
            heading: Ck_Model.heading,
            toolbar: {
                items: [
                    // 'restrictedEditing',
                    'undo', 'redo', 'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'indent', 'outdent', 'pageBreak', 'horizontalLine', '|',
                    'imageUpload', 'mediaEmbed', 'ckfinder',
                    '|', 'specialCharacters',
                    'alignment',
                    'fontSize', 'fontColor', 'fontBackgroundColor', 'fontFamily'
                ]
            },

            fontSize: {
                options: [
                    8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 40, 50
                ]
            },
            fontFamily : {
                // options : ["test"]
            },
            disallowedContent : "*(disallow)",
            ckfinder: Ck_Model.ckFinderOption,
            // plugins : [CKFinder],
            // restrictedEditing: {
            //     myCommand: ['p']
            // },
            // language: 'fr',
            image: {
                // Configure the available styles.
                styles: [
                    'alignLeft', 'alignCenter', 'alignRight', 'full'
                ],
                toolbar: [
                    '|',
                    'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight', 'imageStyle:full',
                    '|',
                    'imageResize',
                    '|',
                    'imageTextAlternative'
                ]
            },

            mediaEmbed: {
                toolbar: [
                    'mediaStyle:alignLeft', 'mediaStyle:alignCenter', 'mediaStyle:alignRight',
                    '|',
                    // 'imageTextAlternative',
                    'mediaResize'
                    // 'imageStyle:full',
                    // 'imageStyle:side'
                ],
                styles: [
                    'alignLeft', 'alignCenter', 'alignRight'
                ],
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableCellProperties',
                    'tableProperties'
                ]
            },
            placeholder: 'My custom placeholder for the body'
        };
        let editor;
        MyEditor
            .create(document.querySelector( '#editor' ) , Ck_Model.contentOption)
            .then(inlineEditor => {

                editor = inlineEditor;
                CKEditorInspector.attach( editor );
                // editElem = elem;
                // originalData = editor.getData();
                // var model = editor.model;
                // model.document.on( 'change:data', () => {
                //     var changes = model.document.differ.getChanges();
                //     if ( changes.length > 0) {
                //         API.btn._disabled(API.btn.cancel(), false);
                //         API.btn._disabled(API.btn.save(), false);
                //         console.log( 'The Document has changed!' );
                //         API._cancelListener();
                //     }else {
                //         API.btn._disabled(API.btn.save(), true);
                //
                //         console.log( 'The Document is the same!' );
                //     }
                // } );
                // Ck_Fn.ckEditor._removeDefClass(editor);
                // if(Fn._isFunction(callable)){
                //     callable();
                // }

            })
            .catch(error => {
                console.error('Oops, something went wrong!');
                console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
                console.warn('Build id: y2rzo0f34zg-re8crceyj9q2');
                console.error(error);
            });
}());