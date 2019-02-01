var canvas = document.getElementById('canvas');
var context = canvas.getContext('2d');
var video = document.getElementById('video');
var filters = document.getElementsByName('filter');
var snap = document.getElementById('take');
var post =  document.getElementById('add_pic');
var took = false;

function camera_setup() {
    snap.addEventListener("click", () => {
        for (var i = 0; i < filters.length; i++) {
            if (filters[i].checked) {
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                if (filters[i].id != "Aucun") {
                    base_image = new Image();
                    base_image.src = filters[i].nextElementSibling.childNodes[0].src;
                    base_image.onload = function(){
                        context.drawImage(base_image, 0, 0, canvas.width, canvas.height);
                    }
                }
                took = true;
                return;
            }
        }
        alert("Vous devez choisir un filtre!");
    });
}

function no_camera() {
    window.addEventListener("load", event => {
        video.parentNode.removeChild(video);
        snap.parentNode.removeChild(snap);
    });
    var form_filter = document.getElementById('form_filter');
    var upload = document.createElement('form');
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('name', 'picture');
    input.setAttribute('accept', 'image/*');
    upload.appendChild(input);
    form_filter.parentNode.insertBefore(upload, form_filter.nextSibling);

    upload.addEventListener('input', event => {
        var file = input.files[0];
        var reader = new FileReader();
        var img = new Image();
        reader.readAsDataURL(file);
        reader.onload = () => {
            img.src = reader.result;
            img.onload = () => {
                for (var i = 0; i < filters.length; i++) {
                    if (filters[i].checked) {
                        context.drawImage(img, 0, 0, canvas.width, canvas.height);
                        if (filters[i].id != "none") {
                            base_image = new Image();
                            base_image.src = filters[i].nextElementSibling.childNodes[0].src;
                            base_image.onload = function(){
                                context.drawImage(base_image, 0, 0, canvas.width, canvas.height);
                            }
                        }
                        took = true;
                        return;
                    }
                }
                upload.reset();
                alert("Vous devez choisir un filtre!");
            };
        };
    });
}

if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
        video.srcObject = stream;
        video.play();
        camera_setup();
    }).catch(function (err) {
        no_camera();
    });
} else {
    no_camera();
}

post.addEventListener("submit", event => {
    if (took == false) {
        alert("Il faut déjà prendre une photo!");
        event.preventDefault();
        return false;
    } else {
        var resized = document.createElement('canvas');
        var resizedContext = resized.getContext('2d');
        resized.height = "480";
        resized.width = "640";
        resizedContext.drawImage(canvas, 0, 0, 640, 480);
        document.getElementById('picture').value = resized.toDataURL('image/png');
    }
});


var del_forms = document.querySelectorAll('form.deletepic');
del_forms.forEach(function(element) {
    element.addEventListener("submit", event => {
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
                        element.parentNode.parentNode.removeChild(element.parentElement);
                    }
                }
            }
        }
    });
});