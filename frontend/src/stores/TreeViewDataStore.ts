import { observable, action } from "mobx";
import { CKEditor } from "../models/CKEditor";
import ReactDOM from 'react-dom';
import ReactHtmlParser, { processNodes, convertNodeToElement } from 'react-html-parser';
import { TreeViewData } from "../models/TreeViewData";
import { BaseStore } from "./BaseStore";
import Button from "@material-ui/core/Button";

export class TreeViewDataStore {

    @observable index: number = 0;
    @observable dataTemp: TreeViewData[] = [];
    @observable data: TreeViewData;
    constructor(private store: BaseStore) {
        this.index = 0;
        this.dataTemp = [];
        this.data = new TreeViewData();
    }

    @action async init() {
        await this.initTreeView();
        await this.createTree(this.data);
    }

    @action async initTreeView() {
        const [err, dataGet] = await CKEditor.get();
        if (!err && dataGet) {
            let data = "<html><body ><div class='body-inner' id='root'>" + dataGet + "</div></body></html>";
            let dataConvertHTML = ReactHtmlParser(data,
                {
                    decodeEntities: true,
                    transform(node, index) {
                    }
                });
            this.getAllNodeChild(dataConvertHTML, null);
        }
        return err;
    }

    @action getAllNodeChild(html, treeView: TreeViewData | null) {
        if (html.length > 0) {
            for (let i = 0; i < html.length; i++) {
                this.index++;
                if (html[i] != undefined && html[i] != null) {
                    let treeViewData = new TreeViewData();
                    if (html[i].type != undefined && html[i].type != null) {
                        treeViewData.set_label(html[i].type);
                        treeViewData.set_nodeId(this.index);
                        if (treeView != null) {
                            treeViewData.set_parent(treeView);
                        }
                        this.dataTemp.push(treeViewData);
                    }
                    if (html[i].props != undefined && html[i].props != null &&
                        html[i].props.children != undefined && html[i].props.children != null) {
                        if (html[i].props.children.length > 0) {
                            let childs = [];
                            for (let j = 0; j < html[i].props.children.length; j++) {
                                if (html[i].props.children[j] != undefined && html[i].props.children[j] != null &&
                                    html[i].props.children[j].type != undefined && html[i].props.children[j].type != null) {
                                    this.index++;
                                    let child = new TreeViewData();
                                    child.set_label(html[i].props.children[j].type);
                                    child.set_nodeId(this.index);
                                    child.set_parent(treeViewData);
                                    childs.push(child);
                                }
                            }
                            treeViewData.set_child(childs);
                            this.getAllNodeChild(html[i].props.children, treeViewData);
                        }
                    }

                }
            }
        }
    }

    @action async createTree(treeData: TreeViewData | null) {
        if (treeData == null || treeData.nodeId == null) {
            if (this.dataTemp.length > 0) {
                for (let i = 0; i < this.dataTemp.length; i++) {
                    if (this.dataTemp[i].nodeId == "1" && this.dataTemp[i].label == "html") {
                        this.data = this.dataTemp[i];
                    }
                }
                this.createTree(this.data);
            }
        } else {
            let children = this.findChildrenByParentId(treeData.nodeId != null ? treeData.nodeId : "1");
            if (children.length > 0) {
                treeData.set_child(children);
                for (let i = 0; i < children.length; i++) {
                    this.createTree(children[i]);
                }
            }
        }
    }

    @action findChildrenByParentId(parentId: number) {
        let children: TreeViewData[] = [];
        for (let i = 0; i < this.dataTemp.length; i++) {
            if (this.dataTemp[i].parent != null && this.dataTemp[i].parent.nodeId == parentId) {
                children.push(this.dataTemp[i]);
            }
        }
        return children;
    }

}