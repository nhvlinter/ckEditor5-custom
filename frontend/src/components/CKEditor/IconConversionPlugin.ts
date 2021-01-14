export default function IconConversionPlugin(editor) {
    let debug = true;
    if (debug) {
        console.log('convertIcon')
    }

    this.editor = editor;

    editor.model.schema.extend('$text', { allowAttributes: 'icon' });
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
        this.editor.conversion.for('downcast').attributeToElement({
            model: 'icon',
            view: (attributeValue, { writer }) => {

                let attr = {};
                attr['class'] = attributeValue;
                attr['contenteditable'] = false;
                if (debug) {
                    console.log('convertIcon.conversion.forDowncast.attributeToElement', {
                        attr: attr, attributeValue: attributeValue, writer: writer
                    })
                }
                return writer.createAttributeElement('i', attr, { priority: 5 });
            },
            converterPriority: 'high'
        });


        // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
        this.editor.conversion.for('upcast').attributeToAttribute({
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