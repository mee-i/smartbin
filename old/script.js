var Data = {
    dailyAverage: {
        organik: [12, 19, 13, 16, 12, 9, 5],
        anorganik: [32, 23, 31, 20, 30, 12, 8] 
    },
    current: {
        organik: 30,
        anorganik: 68
    }
}

var StatusElem = {
    organik: document.getElementById('organik'),
    anorganik: document.getElementById('anorganik')
}

function openTab(id) {
    var tabs = document.querySelectorAll(".tab");
    for (var i = 0; i < tabs.length; i++) {
        if (tabs[i].id != id) {
            tabs[i].hidden = true;
        }
        else {
            tabs[i].hidden = false;
        }
    }
}

openTab('home');

function dataSmartBin() {
    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", "/smartbinstatus.php");

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            const smartbin_state = JSON.parse(this.responseText);
            // console.log("Data received: ");
            // console.log(server_data);
            Data.current.organik = Number(smartbin_state.organic);
            Data.current.anorganik = Number(smartbin_state.anorganic);
            // alert("Data received!");
            loadDataSmartBin();
            // update_data();
            // Server.lastUpdate = (new Date()).getTime();;
        }
    }
    http.send();
}

function myFunction() {
    var element = document.body;
    element.classList.toggle("dark-mode");
}

function loadDataSmartBin() {
    var meter_o = document.querySelector('#organik meter');
    var meter_a = document.querySelector('#anorganik meter');

    var value_o = document.querySelector('#organik .value');
    var value_a = document.querySelector('#anorganik .value');

    meter_o.value = Data.current.organik / 100;
    meter_a.value = Data.current.anorganik / 100;

    value_o.innerHTML = Data.current.organik + '%';
    value_a.innerHTML = Data.current.anorganik + '%';
}
