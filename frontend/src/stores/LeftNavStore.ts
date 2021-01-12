import { observable, action } from "mobx";
import { BaseStore } from "./BaseStore";
import { match } from "assert";

export class LeftNavStore {
    @observable menuLevel_1 = "home";
    @observable menuLevel_2 = "home";
    @observable collapsed = false;
    @observable newWidth:number =240;
    // @observable prePage = "home";
    // @observable preShow = "home";
    constructor(private store: BaseStore) {
    }

    @action set_menuLevel_1 = (v: string) => {
        sessionStorage.setItem("selected", v);
        if (this.menuLevel_1 != v )
            this.menuLevel_1 = v;
        else
            this.menuLevel_1 = v+ " ";
    }

    @action set_menuLevel_2 = (v: string) => {
        sessionStorage.setItem("subselected", v);
        if (this.menuLevel_2 != v)
            this.menuLevel_2 = v;
        else
            this.menuLevel_2 = "";
    }

    @action set_newWidth = ( v : number) => {
        this.newWidth = v;
    }
    @action set_collapsed = (v : boolean) => {
        this.collapsed = v;
    }

    @action toggleLeftNavCollapsed(matchesMax600: boolean) {
        this.collapsed = !this.collapsed;
        if(this.collapsed && matchesMax600)
            this.newWidth = 0;
        else if(this.collapsed)
            this.newWidth = 57;
        else
            this.newWidth = 240;
    }
}
