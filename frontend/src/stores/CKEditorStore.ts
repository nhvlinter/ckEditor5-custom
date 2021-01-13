import { observable, action, computed } from "mobx";
import { BaseStore } from "./BaseStore";
import { aFetch } from "../services/api/fetch";

export class CKEditorStore {
    @observable data: string = "";
    constructor(private store: BaseStore) {
    }
    @action set_data = (v:string) => {
        const emptyParagraphRegexp = /(^|<body\b[^>]*>)\s*<(p|div|address|h\d|center|pre)[^>]*>\s*(?:<br[^>]*>|&nbsp;|\u00A0|&#160;)?\s*(:?<\/\2>)?\s*(?=$|<\/body>)/gi;
        this.data = v.replaceAll(emptyParagraphRegexp,"");
    }
    @action async init() {
        this.set_data(`<p><div style="color:red" onclick="alert('hello DIV')" preset="div tag">This is DIV</div>
        <div style="color:blue" preset="div tag">This is DIV 2</div></p>
        <label style="color:red" onclick="alert('hello label')" for="male">Male</label>
        <span style="color:blue">blue</span>
        <a href="https://www.w3schools.com">Visit W3Schools.com!</a>
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRp-YkmdkiXsrubFUF3mhz7QRGq1yFF7bnAtA&usqp=CAU" alt="Flowers in Chania" width="460" height="345">`);
        return;
        //TODO: GET API
        const [err, dataGet] = await aFetch<{}>("GET", `/get.php`);
        if(!err && dataGet){
            this.set_data(dataGet);
        }
    }
    @action async update(dataChanges: string){
        return;
        //TODO: POST API
        
        this.set_data(dataChanges);
        const [err, dataPost] = await aFetch<{}>("POST", `/post.php`,{
            data: this.data
        });
    }
}