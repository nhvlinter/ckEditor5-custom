import { observable, action, computed } from "mobx";
import { BaseStore } from "./BaseStore";
import { TreeViewDataStore } from "./TreeViewDataStore";
import { aFetch } from "../services/api/fetch";
import { CKEditor } from "../models/CKEditor";
import ReactHtmlParser, { processNodes, convertNodeToElement } from 'react-html-parser';
import { transform } from "lodash-es";
import { TagData, TagData } from "../models/TagData";
import { renderToString } from 'react-dom/server'

export class CKEditorStore {
    @observable data: string = "";
    @observable dataChanges: string = "";
    @observable ckeditor: CKEditor;
    @observable reactId = null;
    @observable reactIds: string[] = [];
    @observable reactChildIds = [];
    @observable tagDatas: TagData[] = [];
    @observable isLoadData: boolean = true;
    constructor(private store: BaseStore) {
        this.ckeditor = new CKEditor();
        this.reactId = null;
        this.reactIds = [];
        this.tagDatas = [];
        this.isLoadData = true;
        this.reactChildIds = [];
    }
    @action set_data = (v: string) => {
        const emptyParagraphRegexp = /(^|<body\b[^>]*>)\s*<(p|div|address|h\d|center|pre)[^>]*>\s*(?:<br[^>]*>|&nbsp;|\u00A0|&#160;)?\s*(:?<\/\2>)?\s*(?=$|<\/body>)/gi;
        this.data = v.replaceAll(emptyParagraphRegexp, "");
    }
    @action set_dataChanges = (v: string) => { this.dataChanges = v; }
    @action set_reactId = (v: any) => { this.reactId = v };
    @action set_isLoadData = (v: boolean) => { this.isLoadData = v };
    @action set_tagDatas = (v: TagData[]) => { this.tagDatas = v.map(x => x); }
    @action async init() {
        // this.set_data(`<div style="color:red" onclick="alert('hello DIV')" preset="div tag">This is DIV</div>
        // <div style="color:blue" preset="div tag">This is DIV 2</div>
        // <label style="color:red" onclick="alert('hello label')" for="male">Male</label>
        // <span style="color:blue">blue</span>
        // <a href="https://www.w3schools.com" style="color:blue">Visit W3Schools.com!</a>
        // <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRp-YkmdkiXsrubFUF3mhz7QRGq1yFF7bnAtA&usqp=CAU" alt="Flowers in Chania" width="300" height="345">
        // <form action="/action_page.php">
        //     <label for="fname">First name:</label><br>
        //     <input type="text" id="fname" name="fname" value="John"><br>
        //     <label for="lname">Last name:</label><br>
        //     <input type="text" id="lname" name="lname" value="Doe"><br><br>
        //     <input type="submit" value="Submit" style="color:red;background-color:powderblue;">

        // </form> 
        // <label for="w3review">Review of W3Schools:</label>
        // <textarea id="w3review" name="w3review" rows="4" cols="50" placeholder="TextArea" >
        //     Hello World
        // </textarea>
        // <p><b>This text is bold</b></p>
        // <p><i>This text is italic</i></p>
        // <a href="https://www.w3schools.com">Visit W3Schools.com!</a>
        // <button type="submit" style="background-color: red" class="btn btn-info">Click Me!</button>
        // <h1>Hello World</h1>
        // <div>
        // <i class="fa fa-cloud" style="font-size:60px;color:blue;text-shadow:2px 2px 4px #000000;"></i>
        // </div>
        // <ul style="list-style-type: square;margin:100px;">
        //     <li style="list-style-type: square;">List A</li>
        //     <li>List B</li>
        //     <li>List C</li>
        // </ul>
        // `);
        // return;
        //TODO: GET API
        const [err, dataGet] = await CKEditor.get();
        if (!err && dataGet) {
            // this.set_data(dataGet);
            let dataHtml = await this.addReactIdToAllTag(dataGet);
            dataHtml = dataHtml.replaceAll(">", ">\n");
            this.set_data(dataHtml);
            if (this.isLoadData) {
                let dataHtml2 = ReactHtmlParser(this.data);
                let dataConvertTree = await this.convertToTree(dataHtml2);
                this.set_tagDatas(dataConvertTree);
                this.set_isLoadData(false);
            }
        }
    }

    @action async get() {
        const [err, dataGet] = await CKEditor.get();
        if (!err && dataGet) {
            return dataGet;
        }
        return err;
    }

    @action async save() {
        this.ckeditor.set_id("editor");
        this.set_data(this.ckeditor.getContent);
        await CKEditor.save(this.ckeditor);
    }

    @action async saveDataChanged(data) {
        const emptyParagraphRegexp = /(^|<body\b[^>]*>)\s*<(p|div|address|h\d|center|pre)[^>]*>\s*(?:<br[^>]*>|&nbsp;|\u00A0|&#160;)?\s*(:?<\/\2>)?\s*(?=$|<\/body>)/gi;
        let dataHtml = await this.removeReactIdToAllTag(data);
        dataHtml = dataHtml.replaceAll(emptyParagraphRegexp, "");
        let ckeditor = new CKEditor();
        ckeditor.set_id("editor");
        ckeditor.set_content(dataHtml);
        await CKEditor.save(ckeditor);
        this.set_isLoadData(true);
        this.init();
    }

    @action async addReactIdToAllTag(data) {
        let idTemp = 1;
        let dataHtml = ReactHtmlParser(data, {
            transform(node) {
                if (node.name != null && node.name != undefined) {
                    node.attribs.reactid = idTemp;
                    idTemp++;
                }
            }
        });
        return renderToString(dataHtml);
    }

    @action async removeReactIdToAllTag(data) {
        let dataHtml = ReactHtmlParser(data, {
            transform(node) {
                if (node.name != null && node.name != undefined) {
                    if (node.attribs.reactid != null && node.attribs.reactid != undefined) {
                        delete node.attribs.reactid;
                    }
                }
            }
        });
        return renderToString(dataHtml);
    }


    @action async findAllReactIdsParentOfNode(item) {
        if (item != null) {
            if (item.id != undefined && item.id != null && item.id != "") {
                let index = this.reactIds.findIndex(x => item.id == x);
                if (index < 0) {
                    this.reactIds.push(item.id);
                }
                if (item.parent != null && item.parent.id != null) {
                    await this.findAllReactIdsParentOfNode(item.parent);
                }
            }
        }
    }

    @action async removeOrAddEleFromReactIds(item) {
        let index = this.reactIds.findIndex(x => item.id == x);
        if (index != null && index >= 0) {
            this.reactIds.splice(index, 1);
            this.reactChildIds = [];
            await this.findAllReactIdsChildrenOfNode(item);
            for (let i = 0; i < this.reactChildIds.length; i++) {
                let indexChild = this.reactIds.findIndex(x => x == this.reactChildIds[i]);
                if (indexChild >= 0) {
                    this.reactIds.splice(indexChild, 1);
                }
            }
        } else {
            this.findAllReactIdsParentOfNode(item);
        }
    }

    @action async findAllReactIdsChildrenOfNode(item) {
        if (item != null) {
            if (item.id != undefined && item.id != null && item.id != "") {
                if (item.children != null && item.children.length > 0) {
                    for (let i = 0; i < item.children.length; i++) {
                        let index = this.reactChildIds.findIndex(x => item.children[i].id == x);
                        if (index < 0) {
                            this.reactChildIds.push(item.children[i].id);
                        }
                        await this.findAllReactIdsChildrenOfNode(item.children[i]);
                    }

                }
            }
        }
    }

    // @action async findAllReactIdsOfNodeTreeView(node) {
    //     if (node != null && node.name != undefined && node.name != null) {
    //         if (node.attribs.reactid != undefined && node.attribs.reactid != null) {
    //             this.reactIds.push(node.attribs.reactid);
    //             if (node.parent != null) {
    //                 await this.findAllReactIdsOfNode(node.parent);
    //             }
    //         }
    //     }
    // }


    @action async convertToTree(html) {
        let result: TagData[] = [];
        for (let i = 0; i < html.length; i++) {
            if (html[i] != null) {
                let dataTemp: TagData = new TagData();
                dataTemp.set_parent(null);
                dataTemp = await this.handledNode(html[i], dataTemp);
                // this.tagDatas.push(dataTemp);
                result.push(dataTemp);
            }
        }
        return result;
    }

    @action async handledNode(html, dataTemp: TagData) {
        if (html.type != null) {
            dataTemp.set_name(html.type);
        }
        if (html.props != null) {
            let checkReactId = html.props.reactid != null;
            let checkChildren = html.props.children != null && html.props.children.length >= 0;
            if (checkReactId) {
                dataTemp.set_id(html.props.reactid);
            }
            if (checkChildren) {
                if (html.props.children.length == 1 && html.props.children[0].type == undefined) {
                    dataTemp.set_text(html.props.children);
                } else {
                    let tagChildren: TagData[] = [];
                    for (let i = 0; i < html.props.children.length; i++) {
                        let children: TagData = new TagData();
                        children.set_parent(dataTemp);
                        children = await this.handledNode(html.props.children[i], children);
                        tagChildren.push(children);
                    }
                    if (tagChildren.length > 0) {
                        dataTemp.set_children(tagChildren);
                    }
                }

            }
            if (checkReactId && checkChildren) {
                const { reactid, children, ...objectTemp } = html.props
                dataTemp.set_props(objectTemp);
            } else if (checkReactId && !checkChildren) {
                const { reactid, ...objectTemp } = html.props
                dataTemp.set_props(objectTemp);
            } else if (!checkReactId && checkChildren) {
                const { children, ...objectTemp } = html.props
                dataTemp.set_props(objectTemp);
            } else {
                dataTemp.set_props(html.props);
            }
        }
        return dataTemp;
    }

}