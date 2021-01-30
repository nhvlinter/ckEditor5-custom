import { observable, action } from "mobx";

export class TreeViewData {
    @observable check: boolean = false;
    @observable child: TreeViewData[] | null = [];
    @observable parent: TreeViewData | null = null;
    @observable nodeId: string | null = null;
    @observable label: string = "";

    constructor(data?: any) {
        if (data != null) {
            const {child, parent, ...pData } = data;
            Object.assign(this, data);
            if(child) {
                this.child = child.map((x:any) => new TreeViewData(x));
            }
            if(this.child == null ) {
                this.child = [];
            }
            if(parent) {
                this.parent = new TreeViewData(parent);
            }
            if(this.parent == null) {
                this.parent = new TreeViewData();
            }
        }
    }

    @action set_check = (v: boolean) => { this.check = v }
    @action set_label = (v: string) => { this.label = v }
    @action set_nodeId = (v: string) => { this.nodeId = v }
    @action set_parent = (v: TreeViewData | null) => { 
        if(!v) {
            this.parent = new TreeViewData();
        } else {
            this.parent = new TreeViewData(v);
        }
    }
    @action set_child = (v: TreeViewData[] | null) => {
        if(!v) {
            this.child = [];
        } else {
            this.child = v.map(x => x);
        }
    }

    toJS() {
        return ({
            label: this.label,
            nodeId: this.nodeId,
            parent: this.parent != null ? this.parent.toJS() : null,
            child: this.child != null ? this.child.map(x => x.toJS()) : null
        })
    }
}