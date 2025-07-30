// import { sha512 } from '/scripts/sha512.min.js';

function generateSignature(str) {
    var _sub_signature = 0;
    var _signature = "";
    var char = "hjrncdiyqsmbfvgteazwxulkpoUJFPNBEVGYRTCMXKLSIOWQAZDH ~`!@#$%^&*()-_=+[{]}\\|;:'\",<.>/?";
    for (let i = 0; i < 100; i++) {
        _sub_signature = 0;
        for (let j = 0; j < 20; j++) {
            let _index = (i + j) % str.length;
            _sub_signature += char.indexOf(str[_index]) * 1044 * i * j;
        }
        _sub_signature *= i * 365;
        _signature += String(btoa(_sub_signature));
    }
    // $signature = strlen($str);
    return _signature;
    // return _signature;
}

function replace_backspace(str) {
    var _str = str;
    while (_str.indexOf('\x08') != -1) {
        // let index = _str.indexOf('\b');
        // _str = _str.substring(0, index - 1) + _str.substring(index + 1);
        _str = _str.replace(/[\s\S\w\W\d\D]\x08/, '');
    }
    return _str;
}

function replace_carriageReturn(str) {
    var _str = "\n" + str;
    while (_str.indexOf('\r') != -1) {
        // let index = _str.indexOf('\r');
        // _str = _str.substring(0, index - _str.lastIndexOf('\n', index) + 1) + _str.substring(index + 1);
        _str = _str.replace(/\n.*\r/, '\n');
    }
    return _str.substring(1);
}

var Terminal = {
    window: document.getElementById("server-terminal"),
    input: document.getElementById("input-terminal"),
    stopBtn: document.getElementById("ctrl-z"),
    closeBtn: document.getElementById("ctrl-c"),
    arrUp: document.getElementById("arr-up"),
    arrDown: document.getElementById("arr-down"),
    scaleUp: document.getElementById("scale-up"),
    scaleDown: document.getElementById("scale-down"),
    scale: 14,
    history: [],
    historyCursor: 0,
    isSending: true,
    send: () => {},
    update: () => {},
    kill: () => {},
    terminate: () => {}
};

Terminal.send = function (init = false) {
    if (Terminal.input.innerText == "clear") {
        Terminal.window.querySelector("#history").innerHTML = "";
        Terminal.input.innerHTML = "";
        // return;
    }
    var http = new XMLHttpRequest();
    var form = new FormData(); 
    var command = "";

    if (init) {
        command = " ";
    }
    else if (Terminal.input.innerText == "") {
        command = " ";
    }
    else {
        command = Terminal.input.innerText;
    }
    form.append("command", command);
    console.log(command);
    form.append("signature", sha512(command));
    // form.append("signature", null);
    http.open("POST", "terminal.php");
    // Terminal.input.hidden = true;

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            // Terminal.window.querySelector("#history").innerHTML += this.responseText;
            Terminal.input.innerHTML = "";
            // Terminal.isSending = false;
            // Terminal.input.hidden = false;
            // Terminal.input.focus();
            setTimeout(Terminal.update, 100);
        }
    }
    Terminal.window.querySelector("#history").innerHTML += Terminal.input.innerText + (!init? '\n' : '');
    Terminal.history.unshift(Terminal.input.innerHTML);
    Terminal.input.innerHTML = "";
    http.send(form);
    Terminal.isSending = true;
}

// if (localStorage.getItem("history") === null)
// else
//     Terminal.window.querySelector("#history").innerHTML = localStorage.getItem("history");

Terminal.update = function(control = "") {
    var http = new XMLHttpRequest();
    
    // if (control == "")
    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            var data = JSON.parse(this.responseText);
            var history = Terminal.window.querySelector("#history").innerHTML;
            history += data.out;
            history = replace_backspace(replace_carriageReturn(history));
            Terminal.window.querySelector("#history").innerHTML = history;
            if (data.run) {
                if (control == "") setTimeout(Terminal.update, 10);
            }
            else {
                Terminal.isSending = false;
                Terminal.window.querySelector("#out").innerHTML = "";
                // localStorage.setItem("history", Terminal.window.querySelector("#history").innerHTML);
            }
            window.scroll(0, Terminal.window.scrollHeight);
        }
    }
    http.open("GET", "terminal.php?update=" + control);
    Terminal.input.innerHTML = "";
    http.send();
}

if (window.isRunning === undefined)
    Terminal.send(true);
else {
    Terminal.isSending = true;
    Terminal.update();
}

Terminal.window.ondblclick = () => {
    Terminal.input.focus();
}

Terminal.kill = () => {
    if (!Terminal.isSending) {
        return;
    }
    Terminal.window.querySelector("#history").innerHTML += "^Z";
    Terminal.update('SIGKILL');
}

Terminal.terminate = () => {
    if (!Terminal.isSending) {
        return;
    }
    Terminal.window.querySelector("#history").innerHTML += "^C";
    Terminal.update('SIGINT');
}

function historyBack() {
    Terminal.historyCursor++;
    if (Terminal.historyCursor > Terminal.history.length - 1) {
        Terminal.historyCursor = Terminal.history.length;
    }
    Terminal.input.innerHTML = Terminal.history[Terminal.historyCursor - 1];
    if (Terminal.historyCursor == 0) {
        Terminal.input.innerHTML = "";
    }
}

function historyForward() {
    Terminal.historyCursor--;
    if (Terminal.historyCursor < 0) {
        Terminal.historyCursor = 0;
    }
    Terminal.input.innerHTML = Terminal.history[Terminal.historyCursor - 1];
    if (Terminal.historyCursor == 0) {
        Terminal.input.innerHTML = "";
    }
}

function scaleUp() {
    Terminal.scale += 2;
    document.body.style.fontSize = Terminal.scale + 'px';
}

function scaleDown() {
    Terminal.scale -= 2;
    document.body.style.fontSize = Terminal.scale + 'px';
}

Terminal.input.onkeydown = (e) => {
    // Terminal.input.innerHTML = Terminal.input.innerText;
    if (Terminal.isSending) {
        if (e.ctrlKey) {
            switch (e.key) {
                case "z":
                    Terminal.kill();
                    break;
                case "c":
                    Terminal.terminate();
                    break;
            }
        }
        return;
    }
    if (e.key == "Enter") {
        Terminal.send();
    }
    else if (e.key == "ArrowUp") {
        historyBack();
    }
    else if (e.key == "ArrowDown") {
        historyForward();
    }
    else {
        Terminal.historyCursor = 0;
    }
}

Terminal.stopBtn.onclick = Terminal.kill;
Terminal.closeBtn.onclick = Terminal.terminate;

Terminal.arrUp.onclick = historyBack;
Terminal.arrDown.onclick = historyForward;

Terminal.scaleUp.onclick = scaleUp;
Terminal.scaleDown.onclick = scaleDown;

function createRange(node, chars, range) {
    if (!range) {
        range = document.createRange()
        range.selectNode(node);
        range.setStart(node, 0);
    }

    if (chars.count === 0) {
        range.setEnd(node, chars.count);
    } else if (node && chars.count >0) {
        if (node.nodeType === Node.TEXT_NODE) {
            if (node.textContent.length < chars.count) {
                chars.count -= node.textContent.length;
            } else {
                range.setEnd(node, chars.count);
                chars.count = 0;
            }
        } else {
           for (var lp = 0; lp < node.childNodes.length; lp++) {
                range = createRange(node.childNodes[lp], chars, range);

                if (chars.count === 0) {
                    break;
                }
            }
        }
    } 

    return range;
};

// Terminal.input.onpaste = function() {
//     setTimeout(() => {
//         Terminal.input.innerHTML = Terminal.input.innerText;
//     });
//     // Terminal.input.focus();
//     // var rangeStart = window.createRange();
//     // rangeStart.selectNode(Terminal.input);
//     // rangeStart
//     // var range = createRange(Terminal.input, Terminal.input.innerText.length);
//     // window.getSelection().addRange(range);
// }