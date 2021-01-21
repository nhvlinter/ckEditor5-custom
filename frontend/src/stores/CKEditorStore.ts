import { observable, action, computed } from "mobx";
import { BaseStore } from "./BaseStore";
import { aFetch } from "../services/api/fetch";
import { CKEditor } from "../models/CKEditor";
export class CKEditorStore {
    @observable data: string = "";
    @observable dataChanges: string = "";
    @observable ckeditor: CKEditor;
    constructor(private store: BaseStore) {
        this.ckeditor = new CKEditor();
    }
    @action set_data = (v: string) => {
        const emptyParagraphRegexp = /(^|<body\b[^>]*>)\s*<(p|div|address|h\d|center|pre)[^>]*>\s*(?:<br[^>]*>|&nbsp;|\u00A0|&#160;)?\s*(:?<\/\2>)?\s*(?=$|<\/body>)/gi;
        this.data = v.replaceAll(emptyParagraphRegexp, "");
    }
    @action set_dataChanges = (v: string) => { this.dataChanges = v; }
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
            this.set_data(dataGet);
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
}