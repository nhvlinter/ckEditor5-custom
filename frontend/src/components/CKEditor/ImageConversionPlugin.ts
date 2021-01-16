import { setupCustomAttributeConversion } from '../../utils/convert';

export default function ImageConversionPlugin(editor) {
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
        setupCustomAttributeConversion('img', 'image', 'width', editor);
        setupCustomAttributeConversion('img', 'image', 'height', editor);
    };
}