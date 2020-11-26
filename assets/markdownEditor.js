let markdown = require( "markdown" ).markdown;

function Editor(input, preview) {
    this.update = function () {
        preview.innerHTML = markdown.toHTML(input.value);
    };
    input.editor = this;
    this.update();
}
let inputArea = document.getElementById("doc_section_content");
let outputArea = document.getElementById("markdown_view");
new Editor(inputArea, outputArea);

inputArea.addEventListener("input", function () {
    this.editor.update();
})


