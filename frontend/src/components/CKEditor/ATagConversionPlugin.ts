export default function ATagConversionPlugin(editor) {
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
            view: (attributeValue, { writer }) => {
                if (debug) {
                    console.log({
                        attributeValue: attributeValue, writer: writer
                    })
                }
                let attr = { href: attributeValue };
                if (typeof attributeValue === 'string') {
                    if (!attributeValue.match(/ckeditor\.com/)) {
                        attr.target = '_blank';
                    }
                }
                const linkElement = writer.createAttributeElement('a', attr, { priority: 5 });
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
            thisEditor.model.schema.extend('$text', { allowAttributes: attrName + 'A' });
            if (debug) {
                console.log("allowAttr(" + attrName + ")")
            }
            // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
            thisEditor.conversion.for('downcast').attributeToElement({
                model: attrName + 'A',
                view: (attributeValue, { writer }) => {

                    let attr = {};
                    attr[attrName] = attributeValue;
                    if (debug) {
                        console.log("allowAttr(" + attrName + ")", {
                            attr: attr, attributeValue: attributeValue, writer: writer
                        })
                    }
                    const linkElement = writer.createAttributeElement('a', attr, { priority: 5 });
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