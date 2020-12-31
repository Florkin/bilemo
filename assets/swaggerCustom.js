import './styles/swaggerCustom.scss';
import 'jquery';

$(document).ready(function () {
    let wrapper = $(".auth-wrapper");

    let newWrapper = document.createElement('div');
    newWrapper.classList.add("new-wrapper")

    let title = document.createElement('h2');
    let paragraph = document.createElement('p');
    title.classList.add("custom-title");
    paragraph.classList.add("custom-paragraph");
    let titleContent = document.createTextNode("Generate token :");
    let paragraphContent = "" +
        "Send a POST Request to url : <strong>/api/login_check</strong><br>" +
        "Content-Type : application/json<br>" +
        "Body: <code>{\"username\" : \"YOUR_EMAIL\", \"password\" : \"YOUR_PASSWORD\"}</code><br>" +
        "Include in your requests headers as: <code><strong>KEY:</strong> Authorization, <strong>VALUE:</strong> Bearer your_token</code>"

    title.appendChild(titleContent);
    paragraph.innerHTML = paragraphContent;

    newWrapper.appendChild(title);
    newWrapper.appendChild(paragraph);

    wrapper.prepend(newWrapper);
})



