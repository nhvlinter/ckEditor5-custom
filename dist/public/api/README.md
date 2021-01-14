# ckEditor
ckEditor api


 ## Data send through this api is at the moment 2 data.
 
 * tagId : id of the html element to modify ex : section#page-image
 * data : the html to change from the selected element
 
 ```html
<!--{page-image}-->
<section  class="shadow bg-image" style="background-image: url('image/mainslider_element3.png')" id="page-image">
    <div class="bg-overlay"></div>
    <div class="container">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
            Asperiores aut deleniti fugit impedit maiores odit praesentium reiciendis? 
            Aliquid debitis doloribus eligendi et explicabo impedit incidunt, 
            ipsam laboriosam optio quae, quaerat quibusdam rerum suscipit totam voluptas.
        </p>
    </div>
</section>
<!--/{page-image}-->
 
 ```