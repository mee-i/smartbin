const UPDATE_TIMEOUT = 20000;

const tabName = [
    "home",
    "dashboard",
    "messages",
    "account",
    "setting"
];

var CurrentTab = 'home';

var server_socket_url = 'wss://socket.smartbin.my.id';
var server_socket = {};
server_socket.close = () => {};

var socket2 = {};
socket2.close = () => {};
socket2.__isopen = false;

function start_ws(mode = "admin_default") {
    var cookies = document.cookie.split(/;\s*/g);
    var data = {};
    for (let i in cookies) {
        let params = cookies[i].split('=');
        if (params[0] == 'PHPSESSID') continue;
        data[params[0]] = params[1];
    }
    data.mode = mode;
    return JSON.stringify(data);
}

function openTab(id) {
    var tabs = document.querySelectorAll(".tab");
    for (var i = 0; i < tabs.length; i++) {
        // console.log("link-" + tabs[i].id);
        if (tabs[i].id != id) {
            tabs[i].hidden = true;
            // document.getElementById("link-" + tabs[i].id).classList.add("current-tab");
        }
        else {
            tabs[i].hidden = false;
            // document.getElementById("link-" + tabs[i].id).classList.add("current-tab");
        }
    }
    CurrentTab = id;
    history.replaceState(null, "", "?tab=" + id + "&redirect=no");

    if (id == 'messages') {
        getMessages();
    }
    else if (id == 'account') {
        getUsersData();
    }

    // if (id == 'experimental') {
    //     connect_socket2();
    // }
    // else {
    //     socket2.onclose = () => {};
    //     socket2.close();
    //     console.log("[WS EXPERIMENTAL] Connection closed");
    // }
}
// openTab(tabName[0]);

function getReq(link, event = function(e) {}) {
    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", link.replace(/ /g, "%20"));

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            event(this);
        }
    }
    http.send();
}

function delMsg(link) {
    getReq(link);
    getMessages();
}

var Server = {
    CPU: 0,
    memory: 0,
    memoryMax: 1,
    storage: 0,
    storageMax: 1,
    lastUpdate: 0,
    reconnect: -1,
    ping: false,
    socketSendBuffer: [],
    pingInternet: 'Unpinged',
    recentLog: "2023/10/25 12:41 ANORGANIK, O:99%, A:100%",
    log:
    "2021/10/25 12:15 ORGANIK, O:99%, A:199%\n" +
    "2021/10/25 12:29 ANORGANIK, O:99%, A:99%\n" +
    "2021/10/25 12:35 ANORGANIK, O:99%, A:100%\n" +
    "2023/10/25 12:41 ANORGANIK, O:99%, A:100%\n"
};

var Server_Socket2 = {
    reconnect: -1,
}

var TrashBin = {
    organic: 0,
    anorganic: 0,
    status: 'unknown'
};

var RealtimeUpdate = false;
var RealtimeUpdateLog = false;

var UpdateTimeout = 0;
var UpdateLogTimeout = 0;

var UpdateTimer = 0;
var UpdateLogTimer = 0;

//   CPU Update: #CPU-stat (.value -> %(000%), meter -> %(0.00))
//   Mem Update: #Mem-stat
//   Recent Log: #recent-log
//   Full stat: .warn

// var receive_cpu = new EventSource("update_data.php?issse=true");

// // Event when receiving a message from the server
// receive_cpu.onmessage = function(e) {
//     // Append the message to the ordered list
//     const server_data = JSON.parse(e.data);
//     console.log("Data received: ");
//     console.log(server_data);
//     Server.CPU = Number(server_data.cpu);
//     Server.memory = Number(server_data.mem);
//     Server.memoryMax = Number(server_data.mem_max);
//     Server.storage = Number(server_data.disk);
//     Server.storageMax = Number(server_data.disk_max);
//     // alert("Data received!");
//     loadData();
// };
// receive_cpu.onopen = function(e) {
//     // alert("SSE opened!");
// };

function set_received_data(server_data) {
    // console.log("Data received: ");
    // console.log(server_data);
    Server.CPU = Number(server_data.cpu);
    Server.memory = Number(server_data.mem);
    Server.memoryMax = Number(server_data.mem_max);
    Server.storage = Number(server_data.disk);
    Server.storageMax = Number(server_data.disk_max);

    TrashBin.organic = server_data.organic;
    TrashBin.anorganic = server_data.anorganic;
    // alert("Data received!");
    loadData();
}

function redirect_login() {
    location.replace("/login?path=" + location.pathname + location.search);
}

function update_data(init = false) {
    // if ((RealtimeUpdate || !(CurrentTab == 'dashboard' || CurrentTab == 'home')) && !init) {
    //     setTimeout(update_data, 1000);
    //     return;
    // }
    if (!(RealtimeUpdate || init))
        return;

    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", "update_data.php");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            const server_data = JSON.parse(this.responseText);
            set_received_data(server_data);
            // update_data
            UpdateTimeout = setTimeout(update_data, 1);
            if (!init || RealtimeUpdate)
                Server.lastUpdate = (new Date()).getTime();
        }
        else if (http.readyState == 4 && http.status == 403) {
            location.replace("/login");
        }
    }
    http.send();
}
// update_data(true);

function ping_timeout() {
    var pingDisplay = document.getElementById("to-server-ping");
    var pingIcon    = document.getElementById("to-server-ping-icon");
    pingIcon.removeAttribute("class");
    pingIcon.classList.add("bx");
    pingIcon.classList.add("bx-wifi-off");
    pingIcon.style.color = "#FF0000";
    pingDisplay.innerHTML = "ERR_PING_TIMEOUT";
}

var ping_timeout = -1;

function update_trashbin_status() {
    var pingDisplay = document.getElementById("trashbin-status");
    var pingIcon    = document.getElementById("trashbin-status-icon");
    var status      = TrashBin.status;
    pingDisplay.innerHTML = status;
    pingIcon.removeAttribute("class");
    // ping_timeout = setTimeout(ping_server, 1000);
    if (status == 'online') {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi");
        pingIcon.style.color = "#00AF00";
    }
    else if (status == 'unknown') {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi-2");
        pingIcon.style.color = "#808080";
    }
    else if (status == 'offline') {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi-off");
        pingIcon.style.color = "#FF0000";
    }
    else {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-question-mark");
        pingIcon.style.color = "#808080";
    }
}

function update_ping() {
    if (Server.ping === false) return;
    var pingDisplay = document.getElementById("to-server-ping");
    var pingIcon    = document.getElementById("to-server-ping-icon");
    var ping        = Server.ping;
    pingDisplay.innerHTML = ping + "ms";
    pingIcon.removeAttribute("class");
    // ping_timeout = setTimeout(ping_server, 1000);
    if (ping <= 30) {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi");
        pingIcon.style.color = "#00AF00";
    }
    else if (ping <= 80) {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi-2");
        pingIcon.style.color = "#A0A000";
    }
    else if (ping <= 500) {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi-1");
        pingIcon.style.color = "#A0A000";
    }
    else {
        pingIcon.classList.add("bx");
        pingIcon.classList.add("bx-wifi-0");
        pingIcon.style.color = "#FF0000";
    }
}

function ping_server() {
    update_ping();
    // server_socket.send(JSON.stringify({message_type: 'PING', continue: false}));
    // Server.pingtime = (new Date()).getTime();
    // Server.socketSendBuffer.unshift({type: 'PING'});

    // http.onreadystatechange = function() {//Call a function when the state changes.
    //     if (http.readyState == 2) {
    //         var pingDisplay = document.getElementById("to-server-ping");
    //         var pingIcon    = document.getElementById("to-server-ping-icon");
    //         var ping        = (new Date()).getTime() - pingtime;
    //         pingDisplay.innerHTML = ping + "ms";
    //         pingIcon.removeAttribute("class");
    //         clearTimeout(pingtimeout);
    //         setTimeout(ping_server, 5000);
    //         if (ping <= 30) {
    //             pingIcon.classList.add("bx");
    //             pingIcon.classList.add("bx-wifi");
    //             pingIcon.style.color = "#00AF00";
    //         }
    //         else if (ping <= 80) {
    //             pingIcon.classList.add("bx");
    //             pingIcon.classList.add("bx-wifi-2");
    //             pingIcon.style.color = "#A0A000";
    //         }
    //         else if (ping <= 500) {
    //             pingIcon.classList.add("bx");
    //             pingIcon.classList.add("bx-wifi-1");
    //             pingIcon.style.color = "#A0A000";
    //         }
    //         else {
    //             pingIcon.classList.add("bx");
    //             pingIcon.classList.add("bx-wifi-0");
    //             pingIcon.style.color = "#FF0000";
    //         }
    //     }
    // }
    // pingtime = (new Date()).getTime();
    // pingtimeout = setTimeout(ping_timeout, 10000);
}
// setTimeout(ping_server, 1000);

function update_server_ping() {
    // var http = new XMLHttpRequest();
    // var pingtime = 0;
    // var pingtimeout;
    // var form = new FormData();
    // http.open("GET", "/serverping.php");

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    // http.onreadystatechange = function() {//Call a function when the state changes.
    //     if (http.readyState == 4 && http.status == 200) {
            var pingDisplay = document.getElementById("server-ping");
            var pingIcon    = document.getElementById("server-ping-icon");
            var ping        = Server.pingInternet;
            pingDisplay.innerHTML = ping + "ms";
            pingIcon.removeAttribute("class");
            // clearTimeout(pingtimeout);
            // setTimeout(get_server_ping, 5000);
            if (ping <= 30) {
                pingIcon.classList.add("bx");
                pingIcon.classList.add("bx-wifi");
                pingIcon.style.color = "#00AF00";
            }
            else if (ping <= 80) {
                pingIcon.classList.add("bx");
                pingIcon.classList.add("bx-wifi-2");
                pingIcon.style.color = "#A0A000";
            }
            else if (ping <= 500) {
                pingIcon.classList.add("bx");
                pingIcon.classList.add("bx-wifi-1");
                pingIcon.style.color = "#A0A000";
            }
            else {
                pingIcon.classList.add("bx");
                pingIcon.classList.add("bx-wifi-0");
                pingIcon.style.color = "#FF0000";
            }
    //     }
    // }
    // http.send();
    // pingtimeout = setTimeout(ping_timeout, 10000);
}
// setTimeout(get_server_ping, 1000);

// function dataSmartBin() {
//     var http = new XMLHttpRequest();
//     // var form = new FormData();
//     http.open("GET", "/smartbinstatus.php");


//     http.onreadystatechange = function() {//Call a function when the state changes.
//         if (http.readyState == 4 && http.status == 200) {
//             const smartbin_state = JSON.parse(this.responseText);
//             TrashBin.organic = Number(smartbin_state.organic);
//             TrashBin.anorganic = Number(smartbin_state.anorganic);
//             // alert("Data received!");
//             loadData();
//         }
//     }
//     http.send();
// }
// dataSmartBin();

function getUsersData() {
    if (!document.getElementById("users-table"))
        return;
    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", "getdata.php?data=usersdata");

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            const usersTable = document.getElementById("users-table");
            const users = JSON.parse(this.responseText);
            let html = "";

            html += "<tr>";
            html += "<th>Account Name</th>";
            html += "<th>Username</th>";
            html += "<th>User Level</th>";
            html += "<th>Last Login</th>";
            html += "<th>Login Count</th>";
            html += "<th>Actions (Work in Progress)</th>";
            html += "</tr>"

            // console.log("Data received: ");
            // console.log(server_data);
            for (let user of users) {
                html += "<tr>";
                html += "<td>" + user.name + "</td>";
                html += "<td>" + user.username + "</td>";
                html += "<td>" + user.level + "</td>";
                html += "<td>" + user.lastlogin + "</td>";
                html += "<td>" + user.logincount + "</td>";
                html += "<td>" + '-' + /*(user.level == 'BASIC_ADMIN'? "Set as SUPER_ADMIN" : (user.level == 'SUPER_ADMIN'? 'Stop as SUPER_ADMIN' : " - ")) .*/ "</td>";
                html += "</tr>";
            }
            usersTable.innerHTML = html;
        }
    }
    http.send();
}

getUsersData();

function getAccessLog(init = false) {
    if (!RealtimeUpdateLog)
        return;

    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", "getdata.php?data=accesslog&cleanmode=true");

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            const logbox = document.getElementById("server-access-log");
            logbox.innerHTML = this.responseText;
            logbox.scroll(0, logbox.scrollHeight);
            if (!init || RealtimeUpdateLog)
                UpdateLogTimeout = setTimeout(getAccessLog, 1600);
        }
    }
    http.send();
}

getAccessLog(true);

function getMessages() {
    var http = new XMLHttpRequest();
    http.open("GET", "getdata.php?data=messages");
    const messageBox = document.getElementById("message-box");
    var html = "";
    html += "<div id='message-box'>";
    html += "<div class='section-card'>";
    html += "<div class='box-title'> Loading </div>";
    html += "</div></div>";
    messageBox.innerHTML = html;

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            messageBox.innerHTML = this.responseText;
        }
    }
    http.send();
}

// function checkLastUpdate() {
//     var lastUpdate = Server.lastUpdate;
//     var timeNow = (new Date()).getTime();

//     // console.log(timeNow - lastUpdate); // De-comment to DEBUG

//     if (timeNow - lastUpdate > (10 * 1000)) {
//         console.warn("Update data timeout, requesting update data...");
//         update_data();
//     }
// }
// setInterval(checkLastUpdate, 5000);

const CLASS_WARN = "warn";
const WARN_MIN = 75;

var StatElem = {
    home: {
        CPU: document.getElementById("CPU-stat"),
        memory: document.getElementById("Mem-stat"),
        log: document.getElementById("recent-log")
    },
    dashboard: {
        CPU: document.getElementById("cpu-dashboard"),
        memory: document.getElementById("mem-dashboard"),
        storage: document.getElementById("storage-dashboard"),
        organic: document.getElementById("stat-organic"),
        anorganic: document.getElementById("stat-anorganic"),
        log: document.getElementById("log")
    }
};

function loadData() {
    var home =      StatElem.home;
    var dashboard = StatElem.dashboard;

    // Server.CPU += Math.round((Math.random() * 10000 % 101 - 51) / 10);
    // Server.memory += Math.round((Math.random() * 10000 % 101 - 51) / 10);

    // if (Server.CPU > 100) {
    //     Server.CPU = 100;
    // }
    // else if (Server.CPU < 0) {
    //     Server.CPU = 0;
    // }
    // if (Server.memory > 100) {
    //     Server.memory = 100;
    // }
    // else if (Server.memory < 0) {
    //     Server.memory = 0;
    // }

    // Value vars
    var valueCPU = document.querySelector("#CPU-stat #value");
    var valueMem = document.querySelector("#Mem-stat #value");
    var valueStg;
    var valueOrganic;
    var valueAnorganic;

    // Update for CPU (home)
    valueCPU.innerHTML = Server.CPU + "%";
    home.CPU.getElementsByTagName("meter")[0].value = Server.CPU / 100;
    if (Server.CPU >= WARN_MIN && !home.CPU.classList.contains(CLASS_WARN)) {
        home.CPU.classList.add(CLASS_WARN);
    }
    else if (Server.CPU < WARN_MIN && home.CPU.classList.contains(CLASS_WARN)) {
        home.CPU.classList.remove(CLASS_WARN);
    }

    // Update for memory (home)
    valueMem.innerHTML = Math.round(Server.memory * 100 / Server.memoryMax) + "%";
    home.memory.getElementsByTagName("meter")[0].value = Math.round(Server.memory * 100 / Server.memoryMax) / 100;
    document.querySelector("#Mem-stat .sub-value").innerHTML = `${Math.round(Server.memory)} / ${Math.round(Server.memoryMax)} MB`;
    if (Math.round(Server.memory * 100 / Server.memoryMax) >= WARN_MIN && !home.memory.classList.contains(CLASS_WARN)) {
        home.memory.classList.add(CLASS_WARN);
    }
    else if (Math.round(Server.memory * 100 / Server.memoryMax) < WARN_MIN && home.memory.classList.contains(CLASS_WARN)) {
        home.memory.classList.remove(CLASS_WARN);
    }

    document.querySelector("#recent-log textarea").value = Server.recentLog;

    valueCPU = document.querySelector("#cpu-dashboard .value");
    valueMem = document.querySelector("#mem-dashboard .value");
    valueStg = document.querySelector("#storage-dashboard .value");
    valueOrganic = document.querySelector("#stat-organic .value");
    valueAnorganic = document.querySelector("#stat-anorganic .value");
    
    // Update CPU (dashboard)
    valueCPU.innerHTML = Server.CPU + "%";
    dashboard.CPU.querySelector(".meter-box").style.width = Server.CPU + "%";
    if (Server.CPU >= WARN_MIN && !dashboard.CPU.classList.contains(CLASS_WARN)) {
        dashboard.CPU.classList.add(CLASS_WARN);
    }
    else if (Server.CPU < WARN_MIN && Server.CPU < WARN_MIN && dashboard.CPU.classList.contains(CLASS_WARN)) {
        dashboard.CPU.classList.remove(CLASS_WARN);
    }

    // Update for memory (dashboard)
    valueMem.innerHTML = `${Math.round(Server.memory)}/${Math.round(Server.memoryMax)} MB | ` + Math.round(Server.memory * 100 / Server.memoryMax) + "%";
    dashboard.memory.querySelector(".meter-box").style.width = Math.round(Server.memory * 100 / Server.memoryMax) + "%";
    if (Math.round(Server.memory * 100 / Server.memoryMax) >= WARN_MIN && !dashboard.memory.classList.contains(CLASS_WARN)) {
        dashboard.memory.classList.add(CLASS_WARN);
    }
    else if (Math.round(Server.memory * 100 / Server.memoryMax) < WARN_MIN && dashboard.memory.classList.contains(CLASS_WARN)) {
        dashboard.memory.classList.remove(CLASS_WARN);
    }

    // Update for storage (dashboard)
    valueStg.innerHTML = `${Math.round(Server.storage)}/${Math.round(Server.storageMax)} GB | ` + Math.round(Server.storage * 100 / Server.storageMax) + "%";
    dashboard.storage.querySelector(".meter-box").style.width = Math.round(Server.storage * 100 / Server.storageMax) + "%";
    if (Math.round(Server.storage * 100 / Server.storageMax) >= WARN_MIN && !dashboard.storage.classList.contains(CLASS_WARN)) {
        dashboard.storage.classList.add(CLASS_WARN);
    }
    else if (Math.round(Server.storage * 100 / Server.storageMax) < WARN_MIN && dashboard.storage.classList.contains(CLASS_WARN)) {
        dashboard.storage.classList.remove(CLASS_WARN);
    }

    // Update for organic trash bin (dashboard)
    valueOrganic.innerHTML = TrashBin.organic + "%";
    dashboard.organic.querySelector(".meter-box").style.width = TrashBin.organic + "%";
    if (TrashBin.organic >= WARN_MIN && !dashboard.organic.classList.contains(CLASS_WARN)) {
        dashboard.organic.classList.add(CLASS_WARN);
    }
    else if (TrashBin.organic < WARN_MIN && dashboard.organic.classList.contains(CLASS_WARN)) {
        dashboard.organic.classList.remove(CLASS_WARN);
    }

    // Update for anorganic trash bin (dashboard)
    valueAnorganic.innerHTML = TrashBin.anorganic + "%";
    dashboard.anorganic.querySelector(".meter-box").style.width = TrashBin.anorganic + "%";
    if (TrashBin.anorganic >= WARN_MIN && !dashboard.anorganic.classList.contains(CLASS_WARN)) {
        dashboard.anorganic.classList.add(CLASS_WARN);
    }
    else if (TrashBin.anorganic < WARN_MIN && dashboard.anorganic.classList.contains(CLASS_WARN)) {
        dashboard.anorganic.classList.remove(CLASS_WARN);
    }
}

if (document.getElementById("power-off"))
document.getElementById("power-off").onclick = () => {
    const xhttp = new XMLHttpRequest();
    
    // Send a request
    xhttp.open("GET", "server_control.php?cmd=shutdown");
    xhttp.send();
};
if (document.getElementById("restart"))
document.getElementById("restart").onclick = () => {
    const xhttp = new XMLHttpRequest();
    
    // Send a request
    xhttp.open("GET", "server_control.php?cmd=restart");
    xhttp.send();
};
// if (document.getElementById("realtime-update"))
// document.getElementById("realtime-update").onclick = () => {
//     const btn = document.getElementById("realtime-update");
//     btn.removeAttribute("class");
//     btn.classList.add("dashboard-btn");
//     if (RealtimeUpdate) {
//         btn.classList.add("bx");
//         btn.classList.add("bx-play");
//         RealtimeUpdate = false;
//         clearTimeout(UpdateTimeout);
//         clearTimeout(UpdateTimer);
//     }
//     else {
//         btn.classList.add("bx");
//         btn.classList.add("bx-pause");
//         RealtimeUpdate = true;
//         update_data();
//         UpdateTimer = setTimeout(() => btn.click(), UPDATE_TIMEOUT);
//     }
// };
if (document.getElementById("realtime-update-log"))
document.getElementById("realtime-update-log").onclick = () => {
    const btn = document.getElementById("realtime-update-log");
    btn.removeAttribute("class");
    btn.classList.add("dashboard-btn");
    if (RealtimeUpdateLog) {
        btn.classList.add("bx");
        btn.classList.add("bx-play");
        RealtimeUpdateLog = false;
        clearTimeout(UpdateLogTimeout);
        clearTimeout(UpdateLogTimer);
    }
    else {
        btn.classList.add("bx");
        btn.classList.add("bx-pause");
        RealtimeUpdateLog = true;
        getAccessLog();
        UpdateLogTimer = setTimeout(() => btn.click(), UPDATE_TIMEOUT);
    }
};

if (document.getElementById("refresh-messages"))
document.getElementById("refresh-messages").onclick = getMessages;

// document.getElementById("terminal").onclick = () => {
//     const xhttp = new XMLHttpRequest();
    
//     // Send a request
//     xhttp.open("GET", "server_control.php?cmd=open_cmd");
//     xhttp.send();
// };

loadData();
// setInterval(loadData, 2000);


const LED_BUILTIN = 2;
const D0   = 16;
const D1   = 5;
const D2   = 4;
const D3   = 0;
const D4   = 2;
const D5   = 14;
const D6   = 12;
const D7   = 13;
const D8   = 15;
const D9   = 3;
const D10  = 1;

const HIGH = 1;
const LOW  = 0;

const close_door   = 0;
const door_o       = 1;
const door_a       = 2;
const rst_capacity = 3; 


function digitalWrite(pin, state) {
    // var http = new XMLHttpRequest();
    // http.open("GET", `server_control.php?control=digitalWrite&pin=${pin}&state=${state}`);
    // http.send();
    socketSend("device_control", `${pin}|${state}`);
}

function control_trashbin(cmd) {
    var commands = [
        'close',
        'open_o',
        'open_a',
        'rst_capacity'
    ];
    socketSend("control_trashbin", commands[cmd]);
}

function ws_c() {
    localStorage.setItem("ws_connect", true);
    server_socket.close();
    connect_socket();
}

function ws_dc() {
    localStorage.setItem("ws_connect", false);
    server_socket.close();
}

function handleSocketMsg(str) {
    var message_data = JSON.parse(str);
    // console.log(message_data);
    if (message_data.message_type == 'data_server') {
        set_received_data(message_data);
        Server.pingInternet = message_data.server_ping;
        Server.ping = Math.round(Number(message_data.ping) * 1000);
        TrashBin.status = message_data.trashbin_status;
        update_ping();
        update_server_ping();
        update_trashbin_status();
    }
    else if (message_data.message_type == 'auth_request') {
        server_socket.send(start_ws());
    }
    else if (message_data.message_type == 'auth_success') {
        console.log("[WS DEFAULT] Authentication success!");
    }
    else if (message_data.message_type == 'auth_fail') {
        console.log("[WS DEFAULT] Authentication failed!");
        redirect_login();
    }
    else if (message_data.message_type == 'PONG') {
        update_ping();
    }
}

function socketSend(msg_type = 'no_msg', msg = null) {
    let message = {
        message_type: msg_type
    }
    if (typeof msg != 'undefined')
        message.msg = msg;
    server_socket.send(JSON.stringify(message));
    // }
    
    // Server.socketSendBuffer = [];
}

function connect_socket2() {}
// function connect_socket2() {
//     console.log("[WS EXPERIMENTAL] Connecting to WebSocket");
//     socket2.__isopen = false;
//     var experimental = document.querySelector("#experimental .section-card");
//     experimental.style.opacity = 0.5;
//     socket2.close();
//     socket2 = new WebSocket(server_socket_url);
//     socket2.__isopen = false;
//     socket2.onmessage = function (event) {
//         var message_data = JSON.parse(event.data);
//         if (message_data.message_type == "auth_request") {
//             socket2.send(start_ws("one_way_client"));
//         }
//         else if (message_data.message_type == 'auth_success') {
//             console.log("[WS EXPERIMENTAL] Authentication success!");
//         }
//         else if (message_data.message_type == 'auth_fail') {
//             console.log("[WS EXPERIMENTAL] Authentication failed!");
//             redirect_login();
//         }
//     }
//     socket2.onopen = function () {
//         socket2.__isopen = true;
//         experimental.style.opacity = 1;
//         clearTimeout(Server_Socket2.reconnect);
//         // pingElem.innerHTML = 'Connnected!';
//         console.log("[WS EXPERIMENTAL] Connected to WebSocket!");
//         console.log("[WS EXPERIMENTAL] Starting authentication...");
//     }
//     socket2.onclose = function () {
//         socket2.__isopen = false;
//         experimental.style.opacity = 0.5;
//         console.log("[WS EXPERIMENTAL] Connection closed");
//         clearTimeout(Server_Socket2.reconnect);
//         Server_Socket2.reconnect = setTimeout(connect_socket2, 1000);
//     }
//     socket2.onerror = function () {
//         socket2.__isopen = false;
//         experimental.style.opacity = 0.5;
//         console.log("[WS EXPERIMENTAL] Error happens");
//         clearTimeout(Server_Socket2.reconnect);
//         Server_Socket2.reconnect = setTimeout(connect_socket2, 3000);
//     }
//     clearTimeout(Server_Socket2.reconnect);
//     Server_Socket2.reconnect = setTimeout(connect_socket2, 5000);
// }

function connect_socket() {
    console.log("[WS DEFAULT] Connecting to WebSocket");
    var pingElem = document.getElementById("to-server-ping");
    pingElem.innerHTML = "Connecting...";
    server_socket.close();
    server_socket = new WebSocket(server_socket_url);
    Server.ping = false;
    // clearTimeout(ping_timeout);
    server_socket.onmessage = function (event) {
        // console.log(event.data);
        // socketSend();
        handleSocketMsg(event.data);
        clearTimeout(Server.reconnect);
        Server.reconnect = setTimeout(connect_socket, 10000);
        // event.data = JSON.parse(event.data);
    }
    server_socket.onopen = function () {
        Server.ping = 0;
        // setTimeout(ping_server);
        clearTimeout(Server.reconnect);
        pingElem.innerHTML = 'Connnected!';
        console.log("[WS DEFAULT] Connected to WebSocket!");
        console.log("[WS DEFAULT] Starting authentication...");
        // server_socket.send(JSON.stringify({mode: 'update_data'}));
    }
    server_socket.onclose = function () {
        if (localStorage.getItem("ws_connect") == 'false') return;
        console.log("[WS DEFAULT] Connection closed");
        clearTimeout(Server.reconnect);
        Server.reconnect = setTimeout(connect_socket, 1000);
    }
    server_socket.onerror = function () {
        console.log("[WS DEFAULT] Error happens");
        clearTimeout(Server.reconnect);
        Server.reconnect = setTimeout(connect_socket, 3000);
    }
    clearTimeout(Server.reconnect);
    Server.reconnect = setTimeout(connect_socket, 5000);
    // connect_socket();
}

window.onload = () => {
    if (localStorage.getItem("ws_connect") === null) {
        localStorage.setItem("ws_connect", "true");
    }
    if (localStorage.getItem("ws_connect") == "true") connect_socket();
    // connect_socket2();
    // server_socket = new WebSocket(server_socket_url);
}

// function device_control(command) {
//     socketSend("device_control", command);
// }

function server_control(command) {
    var http = new XMLHttpRequest();
    http.open("GET", "server_control.php?control=" + command);
    // http.onreadystatechange = function() {//Call a function when the state changes.
    //     if (http.readyState == 4 && http.status == 200) {
    //         messageBox.innerHTML = this.responseText;
    //     }
    // }
    http.send();
    if (command == "WS_RST") {
        location.reload();
    } 
}