var like_forms = document.querySelectorAll('form.like');
like_forms.forEach(function(element) {
    element.addEventListener("submit", function(event) {
        event.preventDefault();
        var req;
        if (window.XMLHttpRequest) {
            req = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (req != undefined) {
            req.open("POST", element.action, true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");   
            var inputs = element.getElementsByTagName("input");
            var sendstr = inputs[0].name + "=" + inputs[0].value + "&" + inputs[1].name + "=" + inputs[1].value;
            req.send(sendstr);
            req.onload = function (e) {
                if (req.readyState === 4 && req.status === 200) {
                    if (req.responseText == "error") {
                        window.location.href = "/account";
                    } else if (req.responseText == "good") {
                        var likes = element.parentElement.querySelectorAll('.likes')[0];
                        var nbr = parseInt(likes.innerHTML);
                        var action = String(element.action);
                        if (action.match('/like/')) {
                            nbr++;
                        } else if (action.match('/dislike/')) {
                            nbr--;
                        }
                        likes.innerHTML = nbr + " likes";
                    }
                }
            }
        }
    });
});

var comment_forms = document.querySelectorAll('form.comment_form');
comment_forms.forEach(function(element) {
    element.addEventListener("submit", function(event) {
        event.preventDefault();
        var req;
        if (window.XMLHttpRequest) {
            req = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (req != undefined) {
            req.open("POST", element.action, true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");   
            var inputs = element.getElementsByTagName("input");
            var sendstr = inputs[0].name + "=" + inputs[0].value + "&" + inputs[1].name + "=" + inputs[1].value;
            req.send(sendstr);
            req.onload = function (e) {
                if (req.readyState === 4 && req.status === 200) {
                    if (req.responseText == "error") {
                        window.location.href = "/account";
                    } else if (req.responseText == "good") {
                        var comments = element.parentElement.querySelectorAll('.pic_comments')[0];
                        var new_comment = document.createElement('p');
                        var br = document.createElement('br');
                        new_comment.innerHTML = '<b>' + inputs[2].value + '</b> ' + inputs[0].value;
                        new_comment.setAttribute('class', 'comment');
                        comments.appendChild(new_comment);
                        comments.appendChild(br);
                        inputs[0].value = "";
                    }
                }
            }
        }
    });
});