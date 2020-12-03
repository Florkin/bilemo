import SimpleMDE from "simplemde";
let elem = document.getElementById("doc_section_content");
elem.classList.remove("form-control");
if (elem) {
    var simplemde = new SimpleMDE({element: elem});
}




