import { observable, action, computed } from "mobx";
import { BaseStore } from "./BaseStore";
import { aFetch } from "../services/api/fetch";

export class CKEditorStore {
    @observable data: string = "";
    constructor(private store: BaseStore) {
    }
    @action set_data = (v: string) => {
        const emptyParagraphRegexp = /(^|<body\b[^>]*>)\s*<(p|div|address|h\d|center|pre)[^>]*>\s*(?:<br[^>]*>|&nbsp;|\u00A0|&#160;)?\s*(:?<\/\2>)?\s*(?=$|<\/body>)/gi;
        this.data = v.replaceAll(emptyParagraphRegexp, "");
    }
    @action async init() {
        this.set_data(`<div style="color:red" onclick="alert('hello DIV')" preset="div tag">This is DIV</div>
        <div style="color:blue" preset="div tag">This is DIV 2</div>
        <label style="color:red" onclick="alert('hello label')" for="male">Male</label>
        <i class="fa fa-cloud" style="font-size:60px;color:lightblue;text-shadow:2px 2px 4px #000000;"></i>
        <span style="color:blue">blue</span>
        <a href="https://www.w3schools.com" style="color:blue">Visit W3Schools.com!</a>
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRp-YkmdkiXsrubFUF3mhz7QRGq1yFF7bnAtA&usqp=CAU" alt="Flowers in Chania" width="460" height="345">
        <form action="/action_page.php">
            <label for="fname">First name:</label><br>
            <input type="text" id="fname" name="fname" value="John"><br>
            <label for="lname">Last name:</label><br>
            <input type="text" id="lname" name="lname" value="Doe"><br><br>
            <input type="submit" value="Submit">
        </form> 
        <ul style="list-style-type:circle">
            <li>Coffee</li>
            <li>Tea</li>
            <li>Milk</li>
        </ul>
        <label for="w3review">Review of W3Schools:</label>
        <textarea id="w3review" name="w3review" rows="4" cols="50">
            At w3schools.com you will learn how to make a website. They offer free tutorials in all web development technologies.
        </textarea>
        <p><b>This text is bold</b></p>
        <p><i>This text is italic</i></p>
        <p>This is<sub> subscript</sub> and <sup>superscript</sup></p>
        <a href="https://www.w3schools.com">Visit W3Schools.com!</a>
        <button type="button" onclick="alert('Hello world!')">Click Me!</button>
        <h1>Hello World</h1>`);
        return;
        //TODO: GET API
        const [err, dataGet] = await aFetch<{}>("GET", `/get.php`);
        if (!err && dataGet) {
            this.set_data(dataGet);
        }
    }
    @action async update(dataChanges: string) {
        return;
        //TODO: POST API

        this.set_data(dataChanges);
        const [err, dataPost] = await aFetch<{}>("POST", `/post.php`, {
            data: this.data
        });
    }
}